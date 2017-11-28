<?php

use Robo\Tasks;

/**
 * Class RoboFile.
 */
class RoboFile extends Tasks {

  use NuvoleWeb\Robo\Task\Config\loadTasks;

  /**
   * Setup project.
   *
   * This command will create the necessary symlinks and scaffolding files.
   *
   * @command project:setup
   * @aliases ps
   */
  public function projectSetup() {
    $collection = $this->collectionBuilder()->addTaskList([
      $this->taskFilesystemStack()->chmod($this->getSiteRoot() . '/sites', 0775, 0000, TRUE),
      $this->taskFilesystemStack()->symlink($this->getProjectRoot(), $this->getSiteRoot() . '/sites/all/modules/' . $this->getProjectName()),
      $this->taskWriteConfiguration($this->getSiteRoot() . '/sites/default/drushrc.php')->setConfigKey('drush'),
      $this->taskAppendConfiguration($this->getSiteRoot() . '/sites/default/default.settings.php')->setConfigKey('settings'),
    ]);

    if (file_exists('behat.yml.dist')) {
      $collection->addTask($this->projectSetupBehat());
    }

    if (file_exists('phpunit.xml.dist')) {
      $collection->addTask($this->projectSetupPhpUnit());
    }

    return $collection;
  }

  /**
   * Setup PHPUnit.
   *
   * This command will copy phpunit.xml.dist in phpunit.xml and replace
   * %DRUPAL_ROOT% and %BASE_URL% with configuration values provided in
   * robo.yml.dist (overridable by robo.yml).
   *
   * @command project:setup-phpunit
   * @aliases psp
   *
   * @return \Robo\Collection\CollectionBuilder
   *   Collection builder.
   */
  public function projectSetupPhpUnit() {
    return $this->collectionBuilder()->addTaskList([
      $this->taskFilesystemStack()->copy('phpunit.xml.dist', 'phpunit.xml'),
      $this->taskReplaceInFile('phpunit.xml')
        ->from(['%DRUPAL_ROOT%', '%BASE_URL%'])
        ->to([$this->getSiteRoot(), $this->config('site.base_url')]),
    ]);
  }

  /**
   * Setup Behat.
   *
   * This command will copy behat.yml.dist in behat.yml and replace
   * %DRUPAL_ROOT% and %BASE_URL% with configuration values provided in
   * robo.yml.dist (overridable by robo.yml).
   *
   * @command project:setup-behat
   * @aliases psb
   *
   * @return \Robo\Collection\CollectionBuilder
   *   Collection builder.
   */
  public function projectSetupBehat() {
    return $this->collectionBuilder()->addTaskList([
      $this->taskFilesystemStack()->copy('behat.yml.dist', 'behat.yml'),
      $this->taskReplaceInFile('behat.yml')
        ->from(['%DRUPAL_ROOT%', '%BASE_URL%'])
        ->to([$this->getSiteRoot(), $this->config('site.base_url')]),
    ]);
  }

  /**
   * Install target site.
   *
   * This command will install the target site using configuration values
   * provided in robo.yml.dist (overridable by robo.yml).
   *
   * @command project:install
   * @aliases pi
   */
  public function projectInstall() {
    return $this->collectionBuilder()->addTaskList([
      $this->getInstallTask()->arg($this->config('site.profile')),
      $this->getDrush()->arg('en')->args($this->config('modules.enable')),
      $this->getDrush()->arg('dis')->args($this->config('modules.disable')),
    ]);
  }

  /**
   * Get installation task.
   *
   * @return \Robo\Task\Base\Exec
   *   Drush installation task.
   */
  protected function getInstallTask() {
    return $this->getDrush()
      ->options([
        'site-name' => $this->config('site.name'),
        'site-mail' => $this->config('site.mail'),
        'locale' => $this->config('site.locale'),
        'account-mail' => $this->config('account.mail'),
        'account-name' => $this->config('account.name'),
        'account-pass' => $this->config('account.password'),
        'db-prefix' => $this->config('database.prefix'),
        'exclude' => $this->config('site.root'),
        'db-url' => sprintf("mysql://%s:%s@%s:%s/%s",
          $this->config('database.user'),
          $this->config('database.password'),
          $this->config('database.host'),
          $this->config('database.port'),
          $this->config('database.name')),
      ], '=')
      ->arg('site-install');
  }

  /**
   * Get configured Drush task.
   *
   * @return \Robo\Task\Base\Exec
   *   Exec command.
   */
  protected function getDrush() {
    return $this->taskExec($this->config('bin.drush'))
      ->option('-y')
      ->option('root', $this->getSiteRoot(), '=');
  }

  /**
   * Get root directory.
   *
   * @return string
   *   Root directory.
   */
  protected function getProjectRoot() {
    return getcwd();
  }

  /**
   * Get site root directory.
   *
   * @return string
   *   Root directory.
   */
  protected function getSiteRoot() {
    return $this->getProjectRoot() . '/' . $this->config('site.root');
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
