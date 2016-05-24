<?php
namespace Tk\Routing;

/**
 * Class Exception
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
interface MatcherInterface  
{
    /**
     * Return true if a path matches a Route object
     *
     * @param \Tk\Request $request
     * @return Route
     */
    public function match($request);
    
}
