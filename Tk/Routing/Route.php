<?php
namespace Tk\Routing;

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
     * @var string
     */
    private $controllerClass = '';

    /**
     * @var string
     */
    private $controllerMethod = '';

    /**
     * @var \Tk\Collection
     */
    protected $attributes = null;

    
    
    /**
     * @param string $path
     * @param string $controllerClassMethod The controller class and method in the form of '\Namespace\ClassName::methodName()' (brackets optional)
     * @param array $attributes
     * @throws Exception
     */
    public function __construct($path, $controllerClassMethod, $attributes = array())
    {
        $this->path = $path;
        $this->attributes = new \Tk\Collection($attributes);
        
        list($class, $method) = explode('::', $controllerClassMethod);
        $method = str_replace('()', '', $method);
        
        if (!preg_match('|[a-z0-9_\\\]+|i', $class, $regs)) {
            throw new Exception('Invalid Controller class: ' . $class);
        }
        if (!preg_match('|[a-z0-9_]+|i', $method, $regs)) {
            throw new Exception('Invalid Controller method call: ' . $controllerClassMethod);
        }
        
        $this->controllerClass = $class;
        $this->controllerMethod = $method;
        
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * @return string
     */
    public function getControllerMethod()
    {
        return $this->controllerMethod;
    }

    /**
     * @return string
     */
    public function getControllerClassMethod()
    {
        return $this->controllerClass . '::' . $this->controllerMethod;
    }

    
    
    
    /**
     * @return \Tk\Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    
    
}
