<?php

namespace Event\Event;

interface InterfaceEventHandler
{
    /**
     * @param string   $event
     * @param callable $callable
     */
    public function addListener($event, callable $callable);

    /**
     * @param string   $event
     * @param callable $callable
     */
    public function removeListener($event, callable $callable);

    /**
     * @param string $event
     */
    public function removeEvent($event);

    public function clear();

    /**
     * @param string $event
     * @param ...    $varargs
     */
    public function notify($event, $varargs = null);

    /**
     * Note that this method should not generate warnings or errors when the
     * provided event does not exist.
     *
     * @param string $event
     *
     * @return array
     */
    public function getListeners($event);

    /**
     * @return array of event names
     */
    public function getEvents();
}
