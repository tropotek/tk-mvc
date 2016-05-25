<?php
namespace Tk\Routing;

/**
 * Class Exception
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class RequestMatcher implements MatcherInterface  
{

    /**
     * @var RouteCollection
     */
    protected $routeCollection = null;

    /**
     * Matche a route to the request using the path and 
     * 
     * @param RouteCollection $routeCollection
     */
    public function __construct(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }
    
    /**
     * Return true if a path matches a Route object
     *
     * @param \Tk\Request $request
     * @return null|Route
     */
    public function match($request)
    {
        /** @var Route $route */
        foreach($this->routeCollection as $name => $route) {
            // Match the methods allowed
            if (count($route->getValidMethods()) && !in_array($request->getMethod(), $route->getValidMethods())) {
                return null;
            }
            
            // Match request path to the route path
            $uri = $request->getUri();
            $routePath = $route->getPath();
            $requestPath = $uri->getRelativePath();
            // TODO: Should we cater for all home paths like '', '/' here?
            // Should this belong in the route match rules?
            if (!rtrim($requestPath,'/')) {
                $requestPath = '/index.html';
            }
            
            
            
            
            
            // TODO: normalise the paths for slashes, urlencoding, etc....
            if ($requestPath == $routePath) {
                $route->getAttributes()->set('name', $name);
                return $route;
            }
        }
    }
    
}
