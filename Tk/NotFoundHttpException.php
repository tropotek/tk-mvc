<?php
namespace Tk;

/**
 * Class UserDeprecatedException
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class NotFoundHttpException extends HttpException {
    
    
    /**
     * Constructor.
     *
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param int        $code     The internal exception code
     */
    public function __construct($message = null, \Exception $previous = null, $code = 404)
    {
        parent::__construct(404, $message, $previous, array(), $code);
    }
}

