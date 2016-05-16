<?php
namespace Tk\Routing;
use Dom\Modifier\Exception;

/**
 * Class Route
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Route 
{

    private $path = '';
    
    private $controllerClass = '';
    
    private $controllerMethod = '';
    
    protected $paramList = array();

    /**
     * @param string $path
     * @param string $controllerClassMethod The controller class and method in the form of 'ClassName::methodName()' (brackets optional)
     * @param array $paramList
     * @throws Exception
     */
    public function __constructor($path, $controllerClassMethod, $paramList = array())
    {
        $this->path = $path;
        if (!preg_match('/^([a-z0-9_]+)::([a-z0-9_]+)(\(\))?$/i', $controllerClassMethod, $regs))
            throw new Exception('Invalid Controller class format use `ClassName::methodName`: ' . $controllerClassMethod);
        vd($controllerClassMethod);
        $this->paramList = $paramList;
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
        if (isset($this->paramList[$name]))
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
        if (isset($this->paramList[$name]))
            unset($this->paramList[$name]);
    }
    
    
    
}
