<?php

namespace Codefog\EventsSubscriptions;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;

class EventConfig
{
    /**
     * @var CalendarModel
     */
    private $calendar;

    /**
     * @var CalendarEventsModel
     */
    private $event;

    /**
     * EventConfig constructor.
     *
     * @param CalendarModel       $calendar
     * @param CalendarEventsModel $event
     */
    public function __construct(CalendarModel $calendar, CalendarEventsModel $event)
    {
        $this->calendar = $calendar;
        $this->event    = $event;
    }

    /**
     * Get the config value
     *
     * @param string $key
     */
    public function get($key)
    {
        if ($this->event->subscription_override) {
            return $this->event->$key;
        }

        return $this->calendar->$key;
    }

    /**
     * Get the event model
     *
     * @return \CalendarEventsModel|CalendarEventsModel|null
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Return true if the event can be subscribed to
     *
     * @return bool
     */
    public function canSubscribe()
    {
        return $this->calendar->subscription_enable ? true : false;
    }

    /**
     * Get the maximum subscriptions
     *
     * @return int
     */
    public function getMaximumSubscriptions()
    {
        return (int) $this->get('subscription_maximum');
    }

    /**
     * Get the last possible time of subscription
     *
     * @return int
     */
    public function getLastTime()
    {
        return (int) $this->get('subscription_lastDay');
    }

    /**
     * Create the instance by event ID
     *
     * @param int $eventId
     *
     * @return EventConfig
     */
    public static function create($eventId)
    {
        if (($event = CalendarEventsModel::findByPk($eventId)) === null) {
            throw new \InvalidArgumentException(sprintf('The event ID "%s" does not exist', $eventId));
        }

        if (($calendar = CalendarModel::findByPk($event->pid)) === null) {
            throw new \InvalidArgumentException(sprintf('The calendar ID "%s" does not exist', $event->pid));
        }

        return new static($calendar, $event);
    }
}