<?php
namespace Tk\Listener;

use Tk\Event\Subscriber;
use Tk\Response;


/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ResponseHandler implements Subscriber
{

    /**
     * @var \Dom\Modifier\Modifier
     */
    protected $domModifier = null;

    /**
     * ResponseHandler constructor.
     *
     * @param \Dom\Modifier\Modifier $domModifier
     */
    public function __construct($domModifier = null)
    {
        $this->domModifier = $domModifier;
    }

    
    /**
     * kernel.view
     * domModify 
     *
     * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
     */
    public function onDomModify($event)
    {
        if (!$this->domModifier) return;
        
        /* @var $template \Dom\Template */
        $result = $event->getControllerResult();
        if ($result instanceof \Dom\Renderer\RendererInterface) {
            $result = $result->getTemplate()->getDocument();
        }
        if ($result instanceof \Dom\Template) {
            $result = $result->getDocument();
        }
        if ($result instanceof \DOMDocument) {
            $this->domModifier->execute($result);
        }
    }

    /**
     * kernel.view
     * NOTE: if you want to modify the template using its API
     * you must add the listeners before this one its priority is set to -100
     * make sure your handlers have a priority > -100 so this is run last
     * 
     * Convert controller return types to a request
     * Once this event is fired and a response is set it will stop propagation, 
     * so other events using this name must be run with a priority > -100
     * 
     * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
     */
    public function onView($event)
    {
        $result = $event->getControllerResult();
        if ($result instanceof \Dom\Template) {
            $event->setResponse(new Response($result->toString()));
        } else if ($result instanceof \Dom\Renderer\RendererInterface) {
            $event->setResponse(new Response($result->getTemplate()->toString()));
        } else if (is_string($result)) {
            $event->setResponse(new Response($result));
        }
    }

    /**
     * kernel.response
     * Add any headers to the final response.
     * 
     * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
     */
    public function onResponse($event)
    {
        $response = $event->getResponse();
        
        // disable the browser cache as this is a dynamic site.
        $response->addHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->addHeader('Cache-Control', 'post-check=0, pre-check=0');
        $response->addHeader('Expires', 'Mon, 1 Jan 2000 00:00:00 GMT');
        $response->addHeader('Pragma', 'no-cache');
        $response->addHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        
    }

    /**
     * getSubscribedEvents
     * 
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Symfony\Component\HttpKernel\KernelEvents::VIEW => array(array('onDomModify', -80), array('onView', -100)),
            \Symfony\Component\HttpKernel\KernelEvents::RESPONSE => 'onResponse'
        );
    }
}