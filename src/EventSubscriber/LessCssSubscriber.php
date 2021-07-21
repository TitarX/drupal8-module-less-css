<?php

namespace Drupal\less_css\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides a LessCssSubscriber.
 */
class LessCssSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['clearCss', 20];
    return $events;
  }

  /**
   * // only if KernelEvents::REQUEST !!!
   *
   * @param Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The Event to process.
   *
   * @see Symfony\Component\HttpKernel\KernelEvents for details
   *
   */
  public function clearCss(GetResponseEvent $event) {
    $config = \Drupal::config('less_css.settings');
    if ($config->get('regenerate')) {
      drupal_flush_all_caches_less_css();
    }
  }

}
