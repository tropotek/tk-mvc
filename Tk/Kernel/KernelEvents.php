<?php
namespace Tk\Kernel;

/**
 * Contains all events thrown in the HttpKernel component.
 *
 * @notes Adapted from Symphony
 */
final class KernelEvents
{
    
    /**
     * The INIT event occurs at the very beginning of kernel construction.
     *
     * This event allows you to create a response for a request before any
     * other code in the framework is executed. 
     *
     * @event \Tk\Event\KernelEvent
     */
    const INIT = 'kernel.init';
    
    /**
     * The REQUEST event occurs at the very beginning of request
     * dispatching.
     *
     * This event allows you to create a response for a request before any
     * other code in the framework is executed.
     *
     * @event \Tk\Event\GetResponseEvent
     */
    const REQUEST = 'kernel.request';

    /**
     * The CONTROLLER event occurs once a controller was found for
     * handling a request.
     *
     * This event allows you to change the controller that will handle the
     * request.
     *
     * @event \Tk\Event\ControllerEvent
     */
    const CONTROLLER = 'kernel.controller';

    /**
     * The VIEW event occurs when the return value of a controller
     * is not a Response instance.
     *
     * This event allows you to create a response for the return value of the
     * controller.
     *
     * @event \Tk\Event\ControllerResultEvent
     */
    const VIEW = 'kernel.view';

    /**
     * The RESPONSE event occurs once a response was created for
     * replying to a request.
     *
     * Generally after the VIEW event but can be called at anytime.
     *
     * This event allows you to modify or replace the response that will be
     * replied.
     *
     * @event \Tk\Event\FilterResponseEvent
     */
    const RESPONSE = 'kernel.response';

    /**
     * The FINISH_REQUEST event occurs when a response was generated for a request.
     *
     * This event allows you to reset the global and environmental state of
     * the application, when it was changed during the request.
     *
     * @event Tk\Event\RequestEvent
     */
    const FINISH_REQUEST = 'kernel.finish_request';

    /**
     * The TERMINATE event occurs once a response was sent.
     *
     * This event allows you to run expensive post-response jobs.
     *
     * @event \Tk\Event\ResponseEvent
     */
    const TERMINATE = 'kernel.terminate';

    /**
     * The EXCEPTION event occurs when an uncaught exception appears.
     *
     * This event allows you to create a response for a thrown exception or
     * to modify the thrown exception.
     *
     * @event \Tk\Event\ExceptionEvent
     */
    const EXCEPTION = 'kernel.exception';
    
    
}
