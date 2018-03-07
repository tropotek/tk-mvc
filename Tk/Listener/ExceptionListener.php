<?php
namespace Tk\Listener;

use Tk\Event\ExceptionEvent;
use Tk\Event\Subscriber;
use Tk\Response;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class ExceptionListener implements Subscriber
{
    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * ExceptionListener constructor.
     * 
     * @param bool $isDebug
     */
    public function __construct($isDebug = false)
    {
        $this->isDebug = $isDebug;
    }

    /**
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
        $extra = '';
        if ($this->isDebug) {
            $toString = trim($event->getException()->__toString());

// Commented out due to issues with dump strings before the trace, see \Dom\Exception
//            $pos = strpos($toString, "Stack trace:");
//            $preStr = substr($toString, 0, $pos-1);
//            $toString = substr($toString, $pos);

            $str = str_replace(array("&lt;?php&nbsp;<br />", 'color: #FF8000'), array('', 'color: #666'), highlight_string("<?php \n" . $toString, true));
            $extra = sprintf('in <em>%s:%s</em>',  $e->getFile(), $e->getLine());
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
<p><strong>$msg $extra</strong></p>
<pre style="">$str</pre>
</body>
</html>
HTML;

        $html = str_replace(\Tk\Config::getInstance()->getSitePath(), '', $html);
        $response = Response::create($html);
        $event->setResponse($response);
        
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