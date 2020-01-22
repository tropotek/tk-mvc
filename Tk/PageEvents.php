<?php
namespace Tk;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class PageEvents
{

    /**
     * Called after the controller Controller/Iface::doDefault() method has been called
     * Use this to modify the controller content.
     *
     * You will need to check what the controller class is to know where you are.
     *
     * <code>
     *     if ($event->get('controller') instanceof \App\Controller\Index) { ... }
     * </code>
     *
     * @event \Tk\Event\Event
     */
    const CONTROLLER_INIT = 'controller.init';

    /**
     * Called after the controller Controller/Iface::show() method has been called
     * Use this to modify the controller content.
     *
     * You will need to check what the controller class is to know where you are.
     *
     * <code>
     *     if ($event->get('controller') instanceof \App\Controller\Index) { ... }
     * </code>
     *
     * @event \Tk\Event\Event
     */
    const CONTROLLER_SHOW = 'controller.show';

    /**
     * Called at the end the Page/Iface::doPageInit() method
     * Use this modify the main page template before the controller is rendered to it
     *
     * @event \Tk\Event\Event
     */
    const PAGE_INIT = 'page.init';

    /**
     * Called at the end the Page/Iface::doPageInit() method
     * Use this modify the main page template before the controller is rendered to it
     *
     * @event \Tk\Event\Event
     */
    const PAGE_SHOW = 'page.show';


}