<?php
namespace Tk\Event;

use \Tk\Response;

/**
 * Class ControllerEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class ControllerEvent extends RequestEvent
{

    /**
     * @var mixed|callable
     */
    private $controller = null;


    /**
     * ControllerEvent constructor.
     *
     * @param mixed|callable $controller
     * @param \Tk\Request $request
     * @param mixed $kernel
     */
    public function __construct($controller, \Tk\Request $request, $kernel = null)
    {
        parent::__construct($request, $kernel);
        $this->controller = $controller;
        
    }
    
    /**
     * Returns the current controller.
     *
     * @return mixed|callable|array
     */
    public function getController()
    {
        if (is_array($this->controller) && isset($this->controller[0])) {
            return $this->controller[0];
        }
        return $this->controller;
    }

    /**
     * getControllerMethod
     * 
     * @return string|array
     */
    public function getControllerMethod()
    {
        if (is_array($this->controller) && isset($this->controller[1])) {
            return $this->controller[1];
        }
        return $this->controller;
    }
    
    
    /**
     * Sets a new controller.
     *
     * @param mixed|callable $controller
     * @throws \LogicException
     */
    public function setController($controller)
    {
        // controller must be a callable
        if (!is_callable($controller)) {
            throw new \LogicException(sprintf('The controller must be a callable (%s given).', \Tk\Str::varToString($controller)));
        }
        $this->setController($controller);
    }

    
}