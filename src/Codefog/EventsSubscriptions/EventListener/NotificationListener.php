<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\EventListener;

use Codefog\EventsSubscriptions\Event\SubscribeEvent;
use Codefog\EventsSubscriptions\Event\UnsubscribeEvent;
use Codefog\EventsSubscriptions\NotificationSender;
use Codefog\EventsSubscriptions\Services;

class NotificationListener
{
    /**
     * @var NotificationSender
     */
    private $sender;

    /**
     * NotificationListener constructor.
     */
    public function __construct()
    {
        $this->sender = Services::getNotificationSender();
    }

    /**
     * On subscribe to event
     *
     * @param SubscribeEvent $event
     */
    public function onSubscribe(SubscribeEvent $event)
    {
        $this->sender->sendByType('events_subscriptions_subscribe', $event->getModel());
    }

    /**
     * On unsubscribe to event
     *
     * @param UnsubscribeEvent $event
     */
    public function onUnsubscribe(UnsubscribeEvent $event)
    {
        $this->sender->sendByType('events_subscriptions_unsubscribe', $event->getModel());
    }
}
