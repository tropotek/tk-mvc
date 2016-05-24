<?php
namespace Tk\EventDispatcher;

/**
 * Class Event
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
abstract class EventInterface extends \Tk\Collection
{
    
    /**
     * @var bool
     */
    private $propagationStopped = false;
    
    
    
    /**
     * Returns whether further event listeners should be triggered.
     *
     * @return bool Whether propagation was already stopped for this event.
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * If multiple event listeners are connected to the same event, no
     * further event listener will be triggered once any trigger calls
     * stopPropagation().
     * 
     * @return $this
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
        return $this;
    }
    
}
