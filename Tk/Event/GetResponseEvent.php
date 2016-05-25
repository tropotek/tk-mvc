<?php
namespace Tk\Event;

use \Tk\Response;

/**
 * Class RequestEvent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony by Fabien Potencier <fabien@symfony.com>
 */
class GetResponseEvent extends RequestEvent
{

    /**
     * @var Response
     */
    private $response;

    
    
    /**
     * Returns the response object.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets a response and stops event propagation.
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        $this->stopPropagation();
    }

    /**
     * Returns whether a response was set.
     *
     * @return bool Whether a response was set
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }
    
}