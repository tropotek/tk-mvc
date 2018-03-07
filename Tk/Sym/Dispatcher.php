<?php
namespace Tk\Sym;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class Dispatcher extends \Tk\Event\Dispatcher implements \Symfony\Component\EventDispatcher\EventDispatcherInterface
{

    /**
     * Gets the listener priority for a specific event.
     *
     * Returns null if the event or the listener does not exist.
     *
     * @param string $eventName The name of the event
     * @param callable $listener The listener
     *
     * @return int|null The event listener priority
     */
    public function getListenerPriority($eventName, $listener)
    {
        if (empty($this->listeners[$eventName])) {
            return null;
        }

        if (is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure) {
            $listener[0] = $listener[0]();
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            foreach ($listeners as $k => $v) {
                if ($v !== $listener && is_array($v) && isset($v[0]) && $v[0] instanceof \Closure) {
                    $v[0] = $v[0]();
                    $this->listeners[$eventName][$priority][$k] = $v;
                }
                if ($v === $listener) {
                    return $priority;
                }
            }
        }
    }
}