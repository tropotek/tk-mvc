<?php
namespace Tk\Routing;

use Traversable;

/**
 * Class Routing
 * 
 * Hold all the routes that are available to the site. 
 * 
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class RouteCollection implements \IteratorAggregate, \Countable
{

    /**
     * @var array
     */
    protected $routeList = array();

    /**
     * 
     */
    public function __construct()
    {
        
    }
    
    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if ($this->exists($name))
            return $this->routeList[$name];
    }

    /**
     * @param $name
     * @param $route
     * @return $this
     */
    public function add($name, $route) 
    {
        $this->routeList[$name] = $route;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function exists($name)
    {
        return isset($this->routeList[$name]);
    }

    /**
     * @param $name
     * @return $this
     */
    public function delete($name)
    {
        if ($this->exists($name))
            unset($this->routeList[$name]);
        return $this;
    }

    /**
     * Gets the number of Routes.
     *
     * @return int
     */
    public function count()
    {
        return count($this->routeList);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routeList);
    }
}