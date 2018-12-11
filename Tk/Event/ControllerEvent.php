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
     * @var callable|array
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
     * @return callable|array|null
     * @todo: This now returns the callback only not the Controller object use self::getControllerObject() if thats what you require
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return callable|array|null
     * @deprecated Use self::getController()
     */
    public function getControllerCallback()
    {
        return $this->controller;
    }

    /**
     * @return callable|array|null
     */
    public function getControllerObject()
    {
        if (is_array($this->controller) && isset($this->controller[0])) {
            return $this->controller[0];
        }
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
     * @param array|callable $controller
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