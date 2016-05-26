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
        
        $html = <<<HTML
<html>
<head>
  <title>{$event->getException()->getMessage()}</title>
</head>
<body>
<h1>Exception: {$event->getException()->getMessage()}</h1>
<pre>{$event->getException()->__toString()}</pre>
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