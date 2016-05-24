<?php
namespace Tk\Kernel;

use \Tk\EventDispatcher\EventDispatcher;
use \Tk\Request;

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
     * {@inheritdoc}
     *
     * @api
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        try {
            return $this->handleRaw($request, $type);
        } catch (\Exception $e) {
            if (false === $catch) {
                $this->finishRequest($request, $type);

                throw $e;
            }

            return $this->handleException($e, $request, $type);
        }
    }
    
    
    
    
    
    
    
    
    
}