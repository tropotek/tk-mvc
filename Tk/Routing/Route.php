<?php
namespace Tk\Routing;

use Tk\Collection;

/**
 * Class Route
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
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
     * @var array
     */
    protected $validMethods = array();


    /**
     * construct
     * Default valid methods are 'GET','POST','HEAD'
     *
     * @param string $path
     * @param object|callable|string $controller A string, callable or object
     * @param array $attributes
     * @param array $validMethods
     */
    public function __construct($path, $controller, $attributes = [], $validMethods = ['GET','POST','HEAD'])
    {
        $this->path = $path;
        $this->controller = $controller;
        $this->attributes = new Collection();
        $this->validMethods = $validMethods;
    }

    /**
     * Set the valid methods for this route
     * Valid Methods:
     *  - 'CONNECT'
     *  - 'DELETE'
     *  - 'GET'
     *  - 'HEAD'
     *  - 'OPTIONS'
     *  - 'PATCH'
     *  - 'POST'
     *  - 'PUT'
     *  - 'TRACE'
     * 
     * @param array $methods
     */
    public function setValidMethods($methods)
    {
        $this->validMethods = is_array($methods) ? $methods : [$methods];
    }

    /**
     * @return array
     */
    public function getValidMethods()
    {
        return $this->validMethods;
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
