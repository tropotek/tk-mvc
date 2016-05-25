<?php
namespace Tk\Event;

use Tk\EventDispatcher\Event;

/**
 * Class KernelEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
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
        parent::__construct();
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