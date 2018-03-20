<?php
namespace Tk\Event;

use Tk\Request;

/**
 * Class RequestEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
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
     * @param Request $request
     * @param \Tk\Kernel\HttpKernel $kernel
     */
    public function __construct(Request $request, $kernel = null)
    {
        parent::__construct($kernel);
        $this->request = $request;
    }

    /**
     * Get the request object.
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
}