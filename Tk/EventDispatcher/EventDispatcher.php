<?php
namespace Tk\EventDispatcher;

use Symfony\Component\EventDispatcher\Debug\WrappedListener;
use Symfony\Component\EventDispatcher\LegacyEventProxy;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\EventDispatcher\Event as ContractsEvent;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class EventDispatcher extends \Symfony\Component\EventDispatcher\EventDispatcher
{
    use \Psr\Log\LoggerAwareTrait;

    /**
     * {@inheritdoc}
     */
//    public function dispatch($event, $eventName = null)
//    {
//        if ($this->logger)
//            if (is_object($event))
//                $this->logger->debug('Dispatch: [' . get_class($event) . ']');
//            else
//                $this->logger->debug('Dispatch: [' . get_class($eventName) . ']');
//
//        $e = parent::dispatch($event, $eventName);
//        return $e;
//    }


    /**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[] $listeners The event listeners
     * @param string     $eventName The name of the event to dispatch
     * @param object     $event     The event object to pass to the event handlers/listeners
     */
    protected function callListeners(iterable $listeners, string $eventName, $event)
    {
        if ($event instanceof \Symfony\Component\EventDispatcher\Event) {
            $this->doDispatch($listeners, $eventName, $event);

            return;
        }

        $stoppable = $event instanceof ContractsEvent || $event instanceof StoppableEventInterface;

        foreach ($listeners as $listener) {
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }
            if ($this->logger)
                $this->logger->debug(' - [' . $eventName . ']  ' . get_class($listener[0]) . ']');
            // @deprecated: the ternary operator is part of a BC layer and should be removed in 5.0
            $listener($listener instanceof WrappedListener ? new LegacyEventProxy($event) : $event, $eventName, $this);
        }
    }

    /**
     * @deprecated since Symfony 4.3, use callListeners() instead
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }
            if ($this->logger)
                $this->logger->debug(' - [' . $eventName . ']  ' . get_class($listener[0]) . ']');
            $listener($event, $eventName, $this);
        }
    }



}