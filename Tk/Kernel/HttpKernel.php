<?php
namespace Tk\Kernel;

use Tk\EventDispatcher\EventDispatcher;
use Tk\Request;
use Tk\Response;
use Tk\EventDispatcher\RequestEvent;
use Tk\EventDispatcher\ResponseEvent;

/**
 * Class HttpKernel
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from the Symfony HttpKernal by Fabien Potencier <fabien@symfony.com>
 */
class HttpKernel
{
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var Request
     */
    protected $request;

    
    
    /**
     * Constructor.
     *
     * @param EventDispatcher  $dispatcher
     * @param Request          $request
     */
    public function __construct(EventDispatcher $dispatcher, Request $request)
    {
        $this->dispatcher = $dispatcher;
        $this->request = $request;
    }



    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param Request $request A Request instance
     * @return Response A Response instance
     * @throws \Exception When an Exception occurs during processing
     */
    public function handle(Request $request)
    {
        try {
            return $this->handleRaw($request);
        } catch (\Exception $e) {
            $this->finishRequest($request);
            throw $e;
        }
    }


    /**
     * Handles a request to convert it to a response.
     * Exceptions are not caught.
     *
     * @param Request $request A Request instance
     * @return Response A Response instance
     *
     * @throws \LogicException       If one of the listeners does not behave as expected
     * @throws NotFoundHttpException When controller cannot be found
     */
    private function handleRaw(Request $request)
    {
        $this->requestStack->push($request);

        // request
        $event = new GetResponseEvent($this, $request, $type);
        $this->dispatcher->dispatch(KernelEvents::REQUEST, $event);

        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request, $type);
        }

        // load controller
        if (false === $controller = $this->resolver->getController($request)) {
            throw new NotFoundHttpException(sprintf('Unable to find the controller for path "%s". The route is wrongly configured.', $request->getPathInfo()));
        }

        $event = new FilterControllerEvent($this, $controller, $request, $type);
        $this->dispatcher->dispatch(KernelEvents::CONTROLLER, $event);
        $controller = $event->getController();

        // controller arguments
        $arguments = $this->resolver->getArguments($request, $controller);

        // call controller
        $response = call_user_func_array($controller, $arguments);

        // view
        if (!$response instanceof Response) {
            $event = new GetResponseForControllerResultEvent($this, $request, $type, $response);
            $this->dispatcher->dispatch(KernelEvents::VIEW, $event);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            }

            if (!$response instanceof Response) {
                $msg = sprintf('The controller must return a response (%s given).', $this->varToString($response));

                // the user may have forgotten to return something
                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }
                throw new \LogicException($msg);
            }
        }

        return $this->filterResponse($response, $request, $type);
    }
    
    
    
    
    
    /**
     * Terminates a request/response cycle.
     *
     * Should be called after sending the response and before shutting down the kernel.
     *
     * @param Request  $request  A Request instance
     * @param Response $response A Response instance
     */
    public function terminate(Request $request, Response $response)
    {
        $this->dispatcher->dispatch(KernelEvents::TERMINATE, new ResponseEvent($response, $request, $this));
    }
    
    /**
     * Publishes the finish request event,
     *
     * @param Request $request
     */
    private function finishRequest(Request $request)
    {
        $this->dispatcher->dispatch(KernelEvents::FINISH_REQUEST, new RequestEvent($request));
        
    }
    
    
    
    
    
    
    
    
    
}