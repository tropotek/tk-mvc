<?php
namespace Tk\Listener;

use Tk\Event\Subscriber;


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
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    public function onException(\Symfony\Component\HttpKernel\Event\ExceptionEvent $event)
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
            \Symfony\Component\HttpKernel\KernelEvents::EXCEPTION => 'onException'
        );
    }
    
    
}