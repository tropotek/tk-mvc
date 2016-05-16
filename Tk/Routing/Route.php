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
     * @var mixed|string
     */
    private $controllerMethod = '';

    /**
     * @var array
     */
    protected $paramList = array();

    /**
     * @param string $path
     * @param string $controllerClassMethod The controller class and method in the form of '\Namespace\ClassName::methodName()' (brackets optional)
     * @param array $paramList
     * @throws Exception
     */
    public function __construct($path, $controllerClassMethod, $paramList = array())
    {
        $this->path = $path;
        $this->paramList = $paramList;
        
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
     * @return array
     */
    public function getParamList()
    {
        return $this->paramList;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getParam($name)
    {
        if ($this->hasParam($name))
            return $this->paramList[$name];
    }

    /**
     * @param $name
     * @param $value
     */
    public function setParam($name, $value)
    {
        $this->paramList[$name] = $value;
    }

    /**
     * @param $name
     */
    public function deleteParam($name)
    {
        if ($this->hasParam($name))
            unset($this->paramList[$name]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function hasParam($name)
    {
        return isset($this->paramList[$name]);
    }
    
    
    
}
