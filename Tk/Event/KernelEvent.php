<?php
namespace Tk\Event;


/**
 * Class KernelEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class KernelEvent extends Event
{
    /**
     * @var \Tk\Kernel\HttpKernel
     */
    protected $kernel = null;

    /**
     * 
     * 
     * @param \Tk\Kernel\HttpKernel $kernel
     */
    public function __construct($kernel = null)
    {
        $this->kernel = $kernel;
    }

    /**
     * Get the kernel object.
     * 
     * @return \Tk\Kernel\HttpKernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }
    
}