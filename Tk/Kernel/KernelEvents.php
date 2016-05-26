<?php
namespace Tk\Kernel;

/**
 * Contains all events thrown in the HttpKernel component.
 *
 * @notes Adapted from Symfony
 */
final class KernelEvents
{
    
    /**
     * The INIT event occurs at the very beginning of kernel construction.
     *
     * This event allows you to create a response for a request before any
     * other code in the framework is executed. 
     *
     * @event Tk\Event\KernelEvent
     * @var string
     */
    const INIT = 'kernel.init';
    
    /**
     * The REQUEST event occurs at the very beginning of request
     * dispatching.
     *
     * This event allows you to create a response for a request before any
     * other code in the framework is executed.
     *
     * @event Tk\Event\GetResponseEvent
     * @var string
     */
    const REQUEST = 'kernel.request';

    /**
     * The EXCEPTION event occurs when an uncaught exception appears.
     *
     * This event allows you to create a response for a thrown exception or
     * to modify the thrown exception.
     *
     * @event Tk\Event\ExceptionEvent
     * @var string
     */
    const EXCEPTION = 'kernel.exception';

    /**
     * The VIEW event occurs when the return value of a controller
     * is not a Response instance.
     *
     * This event allows you to create a response for the return value of the
     * controller.
     *
     * @event Tk\Event\ControllerResultEvent
     * @var string
     */
    const VIEW = 'kernel.view';

    /**
     * The CONTROLLER event occurs once a controller was found for
     * handling a request.
     *
     * This event allows you to change the controller that will handle the
     * request.
     *
     * @event Tk\Event\ControllerEvent
     * @var string
     */
    const CONTROLLER = 'kernel.controller';

    /**
     * The RESPONSE event occurs once a response was created for
     * replying to a request.
     *
     * This event allows you to modify or replace the response that will be
     * replied.
     *
     * @event Tk\Event\FilterResponseEvent
     * @var string
     */
    const RESPONSE = 'kernel.response';

    /**
     * The TERMINATE event occurs once a response was sent.
     *
     * This event allows you to run expensive post-response jobs.
     *
     * @event Tk\Event\ResponseEvent
     * @var string
     */
    const TERMINATE = 'kernel.terminate';

    /**
     * The FINISH_REQUEST event occurs when a response was generated for a request.
     *
     * This event allows you to reset the global and environmental state of
     * the application, when it was changed during the request.
     *
     * @event Tk\Event\RequestEvent
     * @var string
     */
    const FINISH_REQUEST = 'kernel.finish_request';
    
    
}
