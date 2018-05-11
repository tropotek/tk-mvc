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
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class JsonExceptionListener implements Subscriber
{
    /**
     * @var bool
     */
    protected $fullDump = false;

    /**
     * JsonExceptionListener constructor.
     * @param bool $fullDump
     */
    public function __construct($fullDump = false)
    {
        $this->fullDump = $fullDump;
    }


    /**
     * 
     * @param ExceptionEvent $event
     */
    public function onException(ExceptionEvent $event)
    {   
        // TODO: If in debug mode show trace if in Live/Test mode only show message...
        $e = $event->getException();
        
        $err = array(
            'status' => 'err',
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ); 
        if ($this->fullDump) {
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