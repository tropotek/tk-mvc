<?php
namespace Tk\Listener;

use Tk\Event\ExceptionEvent;
use Tk\Event\Subscriber;
use Psr\Log\LoggerInterface;
use Tk\Response;


/**
 * Class RouteListener
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class LogExceptionListener implements Subscriber
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger A LoggerInterface instance
     * @param bool $isDebug
     */
    public function __construct(LoggerInterface $logger = null, $isDebug = false)
    {
        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }


    /**
     * 
     * @param ExceptionEvent $event
     */
    public function onException(ExceptionEvent $event)
    {
        if (!$this->logger) return;

        $e = $event->getException();

        if ($this->isDebug) {
            if ($e instanceof \Tk\WarningException) {
                $this->logger->warning($event->getException()->__toString());
            } else {
                $this->logger->error($event->getException()->__toString());
            }
        } else {
            if ($e instanceof \Tk\WarningException) {
                $this->logger->warning($event->getException()->getMessage());
            } else {
                $this->logger->error($event->getException()->getMessage());
            }
        }

    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Tk\Kernel\KernelEvents::EXCEPTION => 'onException'
        );
    }
    
    
}