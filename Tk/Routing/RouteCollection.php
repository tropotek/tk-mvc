<?php
namespace Tk\Routing;


/**
 * Class Routing
 * 
 * Hold all the routes that are available to the site. 
 * 
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class RouteCollection extends \Tk\Collection
{


    /**
     * Add item to collection
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function add($key, $value)
    {
        return $this->set($key, $value);
    }

    
    // https://github.com/slimphp
}