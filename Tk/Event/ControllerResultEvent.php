<?php
namespace Tk\Event;

use \Tk\Response;

/**
 * Class ControllerEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class ControllerResultEvent extends GetResponseEvent
{


    /**
     * @var mixed
     */
    private $controllerResult = null;


    /**
     * ControllerEvent constructor.
     *
     * @param mixed $controllerResult
     * @param \Tk\Request $request
     * @param mixed $kernel
     */
    public function __construct($controllerResult, \Tk\Request $request, $kernel = null)
    {
        parent::__construct($request, $kernel);
        $this->controllerResult = $controllerResult;
    }

    /**
     * Returns the return value of the controller.
     *
     * @return mixed The controller return value
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
    }

    /**
     * Assigns the return value of the controller.
     *
     * @param mixed $controllerResult The controller return value
     */
    public function setControllerResult($controllerResult)
    {
        $this->controllerResult = $controllerResult;
    }
    
}