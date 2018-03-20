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
class Event extends \Symfony\Component\EventDispatcher\Event
{
    use \Tk\CollectionTrait;


}