<?php
namespace Tk\Listener;

use Tk\Event\ExceptionEvent;
use Tk\EventDispatcher\SubscriberInterface;
use Psr\Log\LoggerInterface;
use Tk\Response;


/**
 * Class RouteListener
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class ExceptionListener implements SubscriberInterface
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
     * 
     * @param ExceptionEvent $event
     */
    public function onException(ExceptionEvent $event)
    {   
        // TODO: If in debug mode show trace if in Live/Test mode only show message...
        $class = get_class($event->getException());
        $msg = $event->getException()->getMessage();
        $str = htmlentities($event->getException()->__toString());

        $html = <<<HTML
<html>
<head>
  <title>$class</title>
</head>
<body style="padding: 10px;">
<h1>$class</h1>
<p>$msg</p>
<pre style="margin: 10px 0px;padding: 10px 0px;">$str</pre>
</body>
</html>
HTML;
        
        $response = new Response($html);
        $event->setResponse($response);
        
        if ($this->logger) {
            $this->logger->warning($event->getException()->__toString());
        }

    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Tk\Kernel\KernelEvents::EXCEPTION => 'onException'
        );
    }
    
    
}