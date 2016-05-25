<?php
namespace Tk\Event;


/**
 * Class ExceptionEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class ExceptionEvent extends GetResponseEvent
{
    /**
     * @var \Exception
     */
    protected $exception = null;

    
    /**
     * __construct
     *
     * @param \Exception $e
     * @param \Tk\Request $request
     * @param \Tk\Kernel\HttpKernel $kernel
     */
    public function __construct(\Exception $e, \Tk\Request $request, $kernel = null)
    {
        parent::__construct($request, $kernel);
        $this->exception =$e;
    }

    /**
     * Get the exception object.
     * 
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
    
}