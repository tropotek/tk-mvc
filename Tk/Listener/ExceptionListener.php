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
    
    private $isDebug = false;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        // TODO: This param should be passed into the constructor.
        $this->isDebug = \Tk\Config::getInstance()->isDebug();
    }


    /**
     * 
     * @param ExceptionEvent $event
     */
    public function onException(ExceptionEvent $event)
    {   
        // TODO: If in debug mode show trace if in Live/Test mode only show message...
        $class = get_class($event->getException());
        $e = $event->getException();
        $msg = $e->getMessage();

        // Color the error for giggles
        // Do not show in debug mode
        $str = '';
        if ($this->isDebug) {
            $str = str_replace(array("&lt;?php&nbsp;<br />", 'color: #FF8000'), array('', 'color: #666'), highlight_string("<?php \n" . str_replace('Stack trace:', "\n--Stack Trace:-- \n", $event->getException()->__toString()), true));
        }
        
        
        $html = <<<HTML
<html>
<head>
  <title>$class</title>
<style>
code, pre {
  line-height: 1.4em;
  padding: 0;margin: 0;
}
</style>
</head>
<body style="padding: 10px;">
<h1>$class</h1>
<p>$msg</p>
<pre style="">$str</pre>
</body>
</html>
HTML;
        
        $response = new Response($html);
        $event->setResponse($response);
        
        if ($this->logger) {
            // TODO: Set the logger level based on the exception thrown
            if ($e instanceof \Tk\WarningException) {
                $this->logger->warning($event->getException()->__toString());
            } else {
                $this->logger->error($event->getException()->__toString());
            }
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