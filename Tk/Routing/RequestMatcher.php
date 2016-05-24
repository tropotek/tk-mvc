<?php
namespace Tk\Routing;

/**
 * Class Exception
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
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
        foreach($this->routeCollection as $route) {
            // Match request path to the route path
            $uri = $request->getUri();
            $routePath = $route->getPath();
            
            // TODO: normalise the paths for slashes, urlencoding, etc....
            
            
            if ($uri->getRelativePath() == $routePath) {
                return $route;
            }
        }
        
    }
    
}
