<?php
namespace Tk\EventDispatcher;

/**
 * Class RequestEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class RequestEvent extends Event
{
    /**
     * @var \Tk\Request
     */
    protected $request = null;

    /**
     * 
     * 
     * @param \Tk\Request $request
     */
    public function __construct(\Tk\Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the request object.
     * 
     * @return \Tk\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
}