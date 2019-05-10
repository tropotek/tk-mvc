<?php
namespace Tk\Event;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Event extends \Symfony\Contracts\EventDispatcher\Event
{
    use \Tk\CollectionTrait;

    /**
     * Try to get a \Tk\Controller\Iface object from an event if we can.
     *
     * @param \Symfony\Contracts\EventDispatcher\Event $event
     * @return null|\Tk\Controller\Iface
     */
    public static function findControllerObject($event)
    {
        $controller = null;
        if (method_exists($event, 'getController')) {
            $controller = $event->getController();
        } else  if ($event instanceof Event && $event->has('controller')) {
            $controller = $event->get('controller');
        }
        if ($controller && !$controller instanceof \Tk\Controller\Iface) {
            if (is_array($controller))
                $controller = $controller[0];
        }
        //if (is_callable($controller)) $controller = null;

        return $controller;
    }

}