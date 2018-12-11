<?php
namespace Tk\Kernel;

use Tk\Event\Dispatcher;
use Tk\Request;
use Tk\Response;
use Tk\Event\KernelEvent;
use Tk\Event\RequestEvent;
use Tk\Event\ResponseEvent;
use Tk\Event\GetResponseEvent;
use Tk\Event\ControllerResultEvent;
use Tk\Event\ControllerEvent;
use Tk\Event\ExceptionEvent;
use Tk\Event\FilterResponseEvent;
use Tk\Controller\Resolver;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class HttpKernel
{
    
    /**
     * @var Dispatcher
     */
    protected $dispatcher = null;

    /**
     * @var Resolver
     */
    protected $resolver = null;

    /**
     * @var Request
     */
    protected $request = null;

    
    
    /**
     * Constructor.
     *
     * @param Dispatcher  $dispatcher
     * @param Resolver $resolver
     */
    public function __construct(Dispatcher $dispatcher, Resolver $resolver)
    {
        $this->dispatcher = $dispatcher;
        $this->resolver = $resolver;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return Resolver
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * @param Request $request A Request instance
     * @return Response A Response instance
     * @throws \Exception When an Exception occurs during processing
     */
    public function handle(Request $request)
    {
        try {
            $this->request = $request;
            return $this->handleRaw($request);
        } catch (\Exception $e) {
            return $this->handleException($e, $request);
        }
    }

    /**
     * Handles a request to convert it to a response.
     * Exceptions are not caught.
     *
     * @param Request $request A Request instance
     * @return Response A Response instance
     *
     * @throws \ReflectionException
     */
    private function handleRaw(Request $request)
    {
        // Trigger a kernel init event
        // Here for the future updates to the kernel
        $this->getDispatcher()->dispatch(KernelEvents::INIT, new KernelEvent($this));
        
        // request
        $event = new GetResponseEvent($request, $this);
        $this->getDispatcher()->dispatch(KernelEvents::REQUEST, $event);
        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request);
        }
        
        // load controller
        $controller = $this->getResolver()->getController($request);
        if (false === $controller) {
            throw new \Tk\NotFoundHttpException(sprintf('Unable to find the controller for "%s". The route is wrongly configured.', $request->getUri()->getRelativePath()));
        }
        $request->setAttribute('controller', $controller);
        $event = new ControllerEvent($controller, $request, $this);
        $this->getDispatcher()->dispatch(KernelEvents::CONTROLLER, $event);

        $controller = $event->getController();
        // controller arguments
        $arguments = $this->getResolver()->getArguments($request, $controller);
        // call controller
        $response = call_user_func_array($controller, $arguments);

        // view
        if (!$response instanceof Response) {
            $event = new ControllerResultEvent($response, $request, $this);
            $this->getDispatcher()->dispatch(KernelEvents::VIEW, $event);
            if ($event->hasResponse()) {
                $response = $event->getResponse();
            }
            if (!$response instanceof Response) {
                //$msg = sprintf('The controller must return a response (%s given).', $this->varToString($response));
                $msg = sprintf('The controller must return a response (%s given).', get_class($response));
                // the coder may have forgotten to return something
                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }
                throw new \LogicException($msg);
            }
        }
        return $this->filterResponse($response, $request);
    }
    
    /**
     * Terminates a request/response cycle.
     * Should be called after sending the response and before shutting down the kernel.
     *
     * @param Request  $request  A Request instance
     * @param Response $response A Response instance
     */
    public function terminate(Request $request, Response $response)
    {
        $this->getDispatcher()->dispatch(KernelEvents::TERMINATE, new ResponseEvent($response, $request, $this));
    }
    
    /**
     * Publishes the finish request event,
     *
     * @param Request $request
     */
    private function finishRequest(Request $request)
    {
        $this->getDispatcher()->dispatch(KernelEvents::FINISH_REQUEST, new RequestEvent($request));
    }

    /**
     * Call this if you want to stop the kernel execution
     * and manually send an exception.
     * TODO: see how this goes using a new request instance instead
     *
     * @param \Exception $exception
     * @throws \Exception
     */
    public function terminateWithException(\Exception $exception)
    {
        $request = \Tk\Request::create();
        $response = $this->handleException($exception, $request);
        $response->send();
        $this->terminate($request, $response);
    }

    /**
     * Filters a response object.
     *
     * @param Response $response
     * @param Request  $request
     * @return Response The filtered Response instance
     * @throws \RuntimeException if the passed object is not a Response instance
     */
    private function filterResponse(Response $response, Request $request)
    {
        $event = new FilterResponseEvent($response, $request, $this);
        $this->getDispatcher()->dispatch(KernelEvents::RESPONSE, $event);
        $this->finishRequest($request);
        return $event->getResponse();
    }
    
    /**
     * Handles an exception by trying to convert it to a Response.
     *
     * @param \Exception $e
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    private function handleException(\Exception $e, $request)
    {
        $event = new ExceptionEvent($e, $request, $this);
        $this->getDispatcher()->dispatch(KernelEvents::EXCEPTION, $event);
        // a listener might have replaced the exception
        $e = $event->getException();
        if (!$event->hasResponse()) {
            $this->finishRequest($request);
            throw $e;
        }

        $response = $event->getResponse();
        // TODO: Check the exiting response is not clientError, serverError or a redirect...???
        // ensure that we actually have an error response
        if ($e instanceof \Tk\HttpException) {
            // keep the HTTP status code and headers
            $response->setStatusCode($e->getStatusCode());
            $response->getHeaderCollection()->replace($e->getHeaders());
        } else {
            $response->setStatusCode(500);
        }
        
        try {
            return $this->filterResponse($response, $request);
        } catch (\Exception $e) {
            return $response;
        }
    }
    
}