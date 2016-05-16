<?php
namespace Tk\Dispatcher;

/**
 * Class Dispatcher
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Dispatcher
{
    /**
     * @var array
     */
    private $listeners = array();
    
    /**
     * @var array
     */
    private $sorted = array();


    /**
     * @param string $eventName
     * @param callable $callback
     */
    public function listen($eventName, $callback)
    {
        $this->listeners[$eventName][] = $callback;
    }

    /**
     * @param $eventName
     * @param EventInterface $event
     * @return Event|EventInterface
     */
    public function dispatch($eventName, EventInterface $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }
        if (!isset($this->listeners[$eventName])) {
            return $event;
        }
        $this->doDispatch($this->getListeners($eventName), $eventName, $event);
        return $event;
    }

    /**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[]        $listeners The event listeners.
     * @param string            $eventName The name of the event to dispatch.
     * @param EventInterface    $event     The event object to pass to the event handlers/listeners.
     */
    protected function doDispatch($listeners, $eventName, EventInterface $event)
    {
        foreach ($listeners as $listener) {
            call_user_func($listener, $event, $eventName, $this);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }




    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     * @param int      $priority  The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     * 
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);
    }


    /**
     * Gets the listeners of a specific eventName or all listeners sorted by descending priority.
     *
     * @param string $eventName The name of the event
     * @return array The event listeners for the specified event, or all event listeners by event name
     */
    public function getListeners($eventName = null)
    {
        if (null !== $eventName) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
            return $this->sorted[$eventName];
        }
        foreach ($this->listeners as $eventName => $eventListeners) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
        }
        return array_filter($this->sorted);
    }


    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string $eventName The name of the event
     * @return bool true if the specified event has any listeners, false otherwise
     */
    public function hasListeners($eventName = null)
    {
        return (bool)count($this->getListeners($eventName));
    }

    /**
     * Removes an event listener from the specified events.
     *
     * @param string $eventName The event to remove a listener from
     * @param callable $listener The listener to remove
     * @return $this
     */
    public function removeListener($eventName, $listener)
    {
        if (!isset($this->listeners[$eventName])) {
            return $this;
        }
        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners, true))) {
                unset($this->listeners[$eventName][$priority][$key], $this->sorted[$eventName]);
            }
        }
        return $this;
    }

    
    /**
     * Adds an event subscriber.
     *
     * The subscriber is asked for all the events he is
     * interested in and added as a listener for these events.
     *
     * @param SubscriberInterface $subscriber The subscriber.
     * @return $this
     */
    public function addSubscriber(SubscriberInterface $subscriber)
    {
        $listeners = $subscriber->getSubscribedEvents();
        foreach ($listeners as $eventName => $listener) {
            // Add the subscribed function as an event
            $this->listen($eventName, array($subscriber, $listener));
        }
        return $this;
    }

    /**
     * Removes an event subscriber.
     *
     * @param SubscriberInterface $subscriber The subscriber
     */
    public function removeSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($eventName, array($subscriber, $listener[0]));
                }
            } else {
                $this->removeListener($eventName, array($subscriber, is_string($params) ? $params : $params[0]));
            }
        }
    }

    /**
     * Sorts the internal list of listeners for the given event by priority.
     *
     * @param string $eventName The name of the event.
     */
    private function sortListeners($eventName)
    {
        $this->sorted[$eventName] = array();

        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
    }
    
    
    
}