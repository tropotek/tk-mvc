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
    
    protected $isDebug = false;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(LoggerInterface $logger = null, $isDebug = null)
    {
        $this->logger = $logger;
        
        if ($isDebug === null && class_exists('\Tk\Config'))
            $this->isDebug = \Tk\Config::getInstance()->isDebug();
    }


    /**
     * 
     * @param ExceptionEvent $event
     */
    public function onException(ExceptionEvent $event)
    {   
        // TODO: If in debug mode show trace if in Live/Test mode only show message...
        $class = get_class($event->getException());
        $e = $event->getException();
        $msg = $e->getMessage();
        
        if ($this->logger) {
            // TODO: Set the logger level based on the exception thrown
            if ($e instanceof \Tk\WarningException) {
                $this->logger->warning($event->getException()->__toString());
            } else {
                $this->logger->error($event->getException()->__toString());
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