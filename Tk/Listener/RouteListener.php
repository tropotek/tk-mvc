<?php
namespace Tk\Listener;


/**
 * Class RouteListener
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class RouteListener
{
    /**
     * @var \Tk\Routing\RouteCollection
     */
    protected $routeCollection = null;

    /**
     * @var \Tk\Routing\MatcherInterface
     */
    protected $matcher = null;

    
    
    /**
     * 
     * 
     * @param \Tk\Routing\RouteCollection $routeCollection
     * @param \Tk\Routing\MatcherInterface $matcher
     */
    public function __construct(\Tk\Routing\RouteCollection $routeCollection, \Tk\Routing\MatcherInterface $matcher)
    {
        $this->routeCollection = $routeCollection;
        $this->matcher = $matcher;
    }


    /**
     * 
     * @param \Tk\EventDispatcher\RequestEvent $event
     */
    public function onRequest(\Tk\EventDispatcher\RequestEvent $event)
    {
        $request = $event->getRequest();
        
        if ($request->hasAttribute('_controller')) {
            // Route found
            return;
        }
        
        $route = $this->matcher->match($request);
        if ($route) {
            $class = $route->getControllerClass();
            $method = $route->getControllerMethod();
            
            if (!class_exists($class)) {
                throw new \RuntimeException('Controller class does not exist. [', $route->getControllerClass().']');
            }
            if (!in_array($method, get_class_methods($class))) {
                throw new \RuntimeException('Controller action method does not exist. [', $route->getControllerClassMethod().']');
            }
            
            // TODO: Shoul we do this here?
            $controller = new $class();
            $request->setAttribute('_controller', $controller);
            $request->setAttribute('_controllerRoute', $route);
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