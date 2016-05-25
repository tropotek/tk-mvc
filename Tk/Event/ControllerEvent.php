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
     * @return mixed|callable
     */
    public function getController()
    {
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
            throw new \LogicException(sprintf('The controller must be a callable (%s given).', $this->varToString($controller)));
        }
        $this->setController($controller);
    }

    /**
     * varToString
     *  
     * @param $var
     * @return string
     */
    private function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }
        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }
            return sprintf('Array(%s)', implode(', ', $a));
        }
        if (is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }
        if (null === $var) {
            return 'null';
        }
        if (false === $var) {
            return 'false';
        }
        if (true === $var) {
            return 'true';
        }
        return (string) $var;
    }
    
}