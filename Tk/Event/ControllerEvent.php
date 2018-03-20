<?php
namespace Tk\Event;


/**
 * Class ControllerEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class ControllerEvent extends RequestEvent
{

    /**
     * @var mixed|callable|\Tk\Controller\Iface
     */
    private $controller = null;


    /**
     * ControllerEvent constructor.
     *
     * @param mixed|callable|\Tk\Controller\Iface $controller
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
        $controller = $this->controller;
        
        if ($controller instanceof \Tk\Controller\Iface && $this->getRequest()->getAttribute('_controller') && !is_callable($this->controller)) {
            $controller = explode('::', $this->getRequest()->getAttribute('_controller'));
        }
        if (is_array($controller) && isset($controller[1])) {
            return $controller[1];
        }
        return $controller;
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
        if (!is_callable($controller) && $controller instanceof \Tk\Controller\Iface) {
            throw new \LogicException(sprintf('The controller must be a callable (%s given).', \Tk\Str::varToString($controller)));
        }
        $this->controller = $controller;
    }

    
}