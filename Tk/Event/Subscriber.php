<?php
namespace Tk\Event;

/**
 * Interface SubscriberInterface
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
interface Subscriber extends \Symfony\Component\EventDispatcher\EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     * NOTE: The higher priority number is run first.
     *
     *                 [1    2    3    4   5]       - order of execution
     * Priority Order: [100, 1,   0,  -1, -100]     - priority order
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
//    public static function getSubscribedEvents();

}