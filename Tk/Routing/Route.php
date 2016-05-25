<?php
namespace Tk\Routing;

use Tk\Collection;

/**
 * Class Route
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Route 
{

    /**
     * @var string
     */
    private $path = '';
    
    /**
     * Can be one of:
     *  - callable: array
     *  - callable: anon function
     *  - object: implementing __invoke method
     *  - string: class name implementing __invoke method
     *  - string: function name
     *  - string: class and method names in the format of `\Namespace\Classname::method`
     * 
     * @var callable|string
     */
    private $controller = null;

    /**
     * @var Collection
     */
    protected $attributes = null;


    /**
     * construct
     * 
     * @param string $path
     * @param object|callable|string $controller A string, callable or object
     * @param array $attributes
     */
    public function __construct($path, $controller, $attributes = array())
    {
        $this->path = $path;
        $this->controller = $controller;
        $this->attributes = new Collection($attributes);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     * @return string|callable
     */
    public function getController()
    {
        return $this->controller;
    }
    
    /**
     * @return Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
}
