<?php
namespace Tk\EventDispatcher;

/**
 * Class ResponseEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class ResponseEvent extends RequestEvent
{
    /**
     * @var \Tk\Response
     */
    protected $response = null;

    /**
     * __construct
     *
     * @param \Tk\Response $response
     * @param \Tk\Request $request
     * @param \Tk\Kernel\HttpKernel $kernel
     */
    public function __construct(\Tk\Response $response, \Tk\Request $request, $kernel = null)
    {
        parent::__construct($request, $kernel);
        $this->response = $response;
    }

    /**
     * Get the response object.
     * 
     * @return \Tk\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    
}