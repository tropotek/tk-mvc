<?php
namespace Tk\Listener;

use Tk\Event\RequestEvent;
use Tk\Routing\MatcherInterface;
use Tk\EventDispatcher\SubscriberInterface;


/**
 * Class RouteListener
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class RouteListener implements SubscriberInterface
{
    /**
     * @var MatcherInterface
     */
    protected $matcher = null;

    
    
    /**
     * 
     * @param MatcherInterface $matcher
     */
    public function __construct(MatcherInterface $matcher)
    {
        $this->matcher = $matcher;
    }


    /**
     * 
     * @param RequestEvent $event
     */
    public function onRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        
        if ($request->hasAttribute('_route')) {
            // Route found
            return;
        }
        
        $route = $this->matcher->match($request);
        if ($route) {
            $request->setAttribute('_route', $route);
        }
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'event.kernel.request' => 'onRequest'
        );
    }
    
    
}