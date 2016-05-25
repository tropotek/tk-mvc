<?php
namespace Tk\Controller;

use Psr\Log\LoggerInterface;
use Tk\Request;

/**
 * Class ControllerResolver
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony by Fabien Potencier <fabien@symfony.com>
 */
class ControllerResolver
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
    

    /**
     * Returns the Controller instance associated with a Request.
     *
     * As several resolvers can exist for a single application, a resolver must
     * return false when it is not able to determine the controller.
     *
     * The resolver must only throw an exception when it should be able to load
     * controller but cannot because of some errors made by the developer.
     *
     * This method looks for a '_controller' request attribute that represents
     * the controller name (a string like ClassName::MethodName).
     *
     * @param Request $request A Request instance
     * @return callable|false A PHP callable representing the Controller,
     *                        or false if this resolver is not able to determine the controller
     * @throws \LogicException If the controller can't be found
     */
    public function getController(Request $request)
    {
        /** @var \Tk\Routing\Route $route */
        $route = $request->getAttribute('_route');
        if (!$route) {
            if (null !== $this->logger) {
                $this->logger->warning('Unable to look for the controller as the "_route" parameter is missing');
            }
            return false;
        }
        
        $controller = $route->getController();
        // Already a callback
        if (is_array($controller)) {
            return $controller;
        }
        // Is an anon object with __invoke magic method
        if (is_object($controller)) {
            if (method_exists($controller, '__invoke')) {
                return $controller;
            }
            throw new \InvalidArgumentException(sprintf('Controller "%s" for URI "%s" is not callable.', get_class($controller), $request->getPathInfo()));
        }
        
        if (false === strpos($controller, ':')) {
            if (method_exists($controller, '__invoke')) {
                return $this->instantiateController($controller);
            } elseif (function_exists($controller)) {
                return $controller;
            }
        }
        $callable = $this->createController($controller);

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf('Controller "%s" for URI "%s" is not callable.', $controller, $request->getPathInfo()));
        }

        return $callable;
    }

    /**
     * Returns the arguments to pass to the controller.
     * Always have the request as the first arg and then any attributes from the route
     * are then added to the args array.
     *
     * @param Request  $request    A Request instance
     * @param callable $controller A PHP callable
     * @return array An array of arguments to pass to the controller
     * @todo This may need to be made a bit more clearer
     */
    public function getArguments(Request $request, $controller)
    {
        $args = ['request' => $request];
        /** @var \Tk\Routing\Route $route */
        $route = $request->getAttribute('_route');
        if ($route) {
            array_merge($args, $route->getAttributes()->all());
        }
        return $args;
    }

    /**
     * Returns a callable for the given controller.
     *
     * @param string $controller A Controller string
     * @return mixed A PHP callable
     * @throws \InvalidArgumentException
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }
        list($class, $method) = explode('::', $controller, 2);
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }
        return array($this->instantiateController($class), $method);
    }

    /**
     * Returns an instantiated controller
     *
     * @param string $class A class name
     * @return object
     */
    protected function instantiateController($class)
    {
        return new $class();
    }
    
}