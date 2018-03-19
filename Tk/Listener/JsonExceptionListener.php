<?php
namespace Tk\Listener;

use Tk\Event\ExceptionEvent;
use Tk\Event\Subscriber;
use Psr\Log\LoggerInterface;
use Tk\Response;
use Tk\ResponseJson;


/**
 * Class RouteListener
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class JsonExceptionListener implements Subscriber
{
    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * JsonExceptionListener constructor.
     * @param bool $isDebug
     */
    public function __construct($isDebug = false)
    {
        $this->isDebug = $isDebug;
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
        
        $err = array(
            'status' => 'err',
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ); 
        if ($this->isDebug) {
            $err['debug'] = $e->__toString();
        }
        
        $response = \Tk\ResponseJson::createJson($err, 500);
        $event->setResponse($response);
        
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