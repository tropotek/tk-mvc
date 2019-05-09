<?php
namespace Tk\EventDispatcher;

use Symfony\Contracts\EventDispatcher\Event;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Contracts\EventDispatcher\Event as ContractsEvent;

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

    protected function callListeners(iterable $listeners, string $eventName, $event)
    {
        $stoppable = $event instanceof ContractsEvent || $event instanceof StoppableEventInterface;
        foreach ($listeners as $listener) {
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }
            if ($this->logger)
                $this->logger->debug(' - [' . $eventName . ']  ' . get_class($listener[0]) . ']');
        }
        parent::callListeners($listeners, $eventName, $event);
    }

    /**
     * @deprecated since Symfony 4.3, use callListeners() instead
     */
    protected function doDispatch($listeners, $eventName, \Symfony\Component\EventDispatcher\Event $event)
    {
        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }
            if ($this->logger)
                $this->logger->debug(' - [' . $eventName . ']  ' . get_class($listener[0]) . ']');
        }
        parent::doDispatch($listeners, $eventName, $event);
    }





}