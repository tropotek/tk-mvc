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
class FilterResponseEvent extends ResponseEvent
{
    /**
     * Sets a response and stops event propagation.
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
    
}