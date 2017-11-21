<?php

use Robo\Tasks;

/**
 * Class RoboFile.
 */
class RoboFile extends Tasks {

  use Boedah\Robo\Task\Drush\loadTasks;
  use NuvoleWeb\Robo\Task\Config\loadTasks;

  /**
   * Install site.
   *
   * @command project:setup
   * @aliases pi
   */
  public function projectSetup() {
    $this->taskFilesystemStack()
      ->chmod('build/sites', 0775, 0000, TRUE)
      ->run();
    $this->_symlink($this->root(), $this->root() . '/build/sites/all/modules/' . $this->getProjectName());
    $this->taskWriteConfiguration('build/sites/default/drushrc.php')
      ->setConfigKey('drush')
      ->run();
    $this->taskAppendConfiguration('build/sites/default/default.settings.php')
      ->setConfigKey('settings')
      ->run();
  }

  /**
   * Install site.
   *
   * @command project:install
   * @aliases pi
   */
  public function projectInstall() {
    $this->getInstallTask()
      ->siteInstall($this->config('site.profile'))
      ->run();

    $modules_list = implode(' ', $this->config('modules.enable'));
    $this->taskDrushStack($this->config('bin.drush'))
      ->drupalRootDirectory($this->root() . '/build')
      ->drush('en ' . $modules_list)
      ->run();

    $modules_list = implode(' ', $this->config('modules.disable'));
    $this->taskDrushStack($this->config('bin.drush'))
      ->drupalRootDirectory($this->root() . '/build')
      ->drush('dis ' . $modules_list)
      ->run();
  }

  /**
   * Get installation task.
   *
   * @return \Boedah\Robo\Task\Drush\DrushStack
   *   Drush installation task.
   */
  protected function getInstallTask() {
    return $this->taskDrushStack($this->config('bin.drush'))
      ->arg("--root={$this->root()}/build")
      ->siteName($this->config('site.name'))
      ->siteMail($this->config('site.mail'))
      ->locale($this->config('site.locale'))
      ->accountMail($this->config('account.mail'))
      ->accountName($this->config('account.name'))
      ->accountPass($this->config('account.password'))
      ->dbPrefix($this->config('database.prefix'))
      ->dbUrl(sprintf("mysql://%s:%s@%s:%s/%s",
        $this->config('database.user'),
        $this->config('database.password'),
        $this->config('database.host'),
        $this->config('database.port'),
        $this->config('database.name')));
  }

  /**
   * Get root directory.
   *
   * @return string
   *   Root directory.
   */
  protected function root() {
    return getcwd();
  }

  /**
   * Get project name from composer.json.
   *
   * @return string
   *   Project name.
   */
  protected function getProjectName() {
    $package = json_decode(file_get_contents('./composer.json'));
    list(, $name) = explode('/', $package->name);
    return $name;
  }

}
