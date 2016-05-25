<?php
namespace Tk\Event;

use Tk\EventDispatcher\Event;

/**
 * Class RequestEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class RequestEvent extends KernelEvent
{
    /**
     * @var \Tk\Request
     */
    protected $request = null;

    /**
     * __construct
     * 
     * @param \Tk\Request $request
     * @param \Tk\Kernel\HttpKernel $kernel
     */
    public function __construct(\Tk\Request $request, $kernel = null)
    {
        parent::__construct($kernel);
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