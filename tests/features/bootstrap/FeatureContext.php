<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Class FeatureContext.
 *
 * @package Drupal\nexteuropa_poetry\Tests\Behat
 */
class FeatureContext extends RawDrupalContext {

  /**
   * Restores the initial values of the Drupal variables.
   *
   * @BeforeScenario @watchdog
   */
  public function resetWatchdog() {
    db_truncate('watchdog')->execute();
  }

  /**
   * Assert message in Watchdog.
   *
   * @When the following message should be in the watchdog :message
   */
  public function assertWatchdogMessage($message) {
    $result = db_select('watchdog', 'w')
      ->fields('w')
      ->condition('message', $message)
      ->countQuery()
      ->execute()
      ->fetchField();
    if ($result == 0) {
      throw new \Exception("Message '{$message}' not found in Watchdog.");
    }
  }

}
