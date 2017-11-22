<?php

namespace Drupal\nexteuropa_poetry;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EC\Poetry\Events\Notifications\TranslationReceivedEvent;
use EC\Poetry\Events\Notifications\StatusUpdatedEvent;
use EC\Poetry\Messages\Notifications\TranslationReceived;
use EC\Poetry\Messages\Notifications\StatusUpdated;

/**
 * Class NotificationSubscriber.
 *
 * @package Drupal\nexteuropa_poetry
 */
class NotificationSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      TranslationReceivedEvent::NAME => 'onTranslationReceivedEvent',
      StatusUpdatedEvent::NAME       => 'onStatusUpdatedEvent',
    ];
  }

  /**
   * Event handler.
   *
   * @param \EC\Poetry\Messages\Notifications\TranslationReceived $message
   *   Message object.
   */
  public function onTranslationReceivedEvent(TranslationReceived $message) {
    module_invoke_all('nexteuropa_poetry_notification_translation_received', $message);
  }

  /**
   * Event handler.
   *
   * @param \EC\Poetry\Messages\Notifications\StatusUpdated $message
   *   Message object.
   */
  public function onStatusUpdatedEvent(StatusUpdated $message) {
    module_invoke_all('nexteuropa_poetry_notification_status_updated', $message);
  }

}
