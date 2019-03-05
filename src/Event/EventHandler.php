<?php
/**
 * KNUT7 K7F (http://framework.artphoweb.com/)
 * KNUT7 K7F (tm) : Rapid Development Framework (http://framework.artphoweb.com/).
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @see      http://github.com/zebedeu/artphoweb for the canonical source repository
 *
 * @copyright (c) 2015.  KNUT7  Software Technologies AO Inc. (http://www.artphoweb.com)
 * @license   http://framework.artphoweb.com/license/new-bsd New BSD License
 * @author    Marcio Zebedeu - artphoweb@artphoweb.com
 *
 * @version   1.0.2
 */

namespace Event\Event;
use Event\Event\InterfaceEventHandler; 
/**
 * EventHandler enforces that a specific instance of a listener can only be
 * attached to the same event once, minus an edge case or two that are not
 * worth fussing over.
 */
class EventHandler implements InterfaceEventHandler
{
    protected $events = [];

    /**
     * @param string   $event
     * @param callable $callable
     */
    public function addListener($event, callable $callable)
    {
        $this->events[$event][$this->hash($callable)] = $callable;
    }

    /**
     * @param string   $event
     * @param callable $callable
     */
    public function removeListener($event, callable $callable)
    {
        unset($this->events[$event][$this->hash($callable)]);
    }

    /**
     * hash.
     *
     * @param callable $callable
     */
    public function hash(callable $callable): string
    {
        if (is_string($callable)) {
            return $callable;
        }
        if (is_array($callable)) {
            if (is_object($callable[0])) {
                return spl_object_hash($callable[0]).$callable[1];
            }

            return "{$callable[0]}::{$callable[1]}";
        }

        return spl_object_hash($callable);
    }

    /**
     * @param string $event
     */
    public function removeEvent($event)
    {
        unset($this->events[$event]);
    }

    public function clear()
    {
        $this->events = [];
    }

    /**
     * @param string $event
     * @param ...    $varargs
     */
    public function notify($event, $varargs = null)
    {
        if (empty($this->events[$event])) {
            return;
        }

        $args = func_get_args();
        array_shift($args);

        foreach ($this->events[$event] as $listener) {
            call_user_func_array($listener, $args);
        }
    }

    /**
     * Note that this method should not generate warnings or errors when the
     * provided event does not exist.
     *
     * @param string $event
     *
     * @return array
     */
    public function getListeners($event)
    {
        $eventExists = array_key_exists($event, $this->events);
        if ($eventExists && is_array($this->events[$event])) {
            return array_values($this->events[$event]);
        }

        return [];
    }

    /**
     * @return array of event names
     */
    public function getEvents()
    {
        return array_keys($this->events);
    }
}
