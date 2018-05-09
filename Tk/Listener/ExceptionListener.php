<?php
namespace Tk\Listener;

use Tk\Event\ExceptionEvent;
use Tk\Event\Subscriber;
use Tk\Response;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
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
        $html = self::getExceptionHtml($event->getException(), $this->isDebug);

        $response = Response::create($html);
        $event->setResponse($response);
        
    }

    /**
     * @param \Exception $e
     * @param bool $isDebug
     * @param bool $fullTrace
     * @return mixed|string
     */
    public static function getExceptionHtml($e, $isDebug = false, $fullTrace = false)
    {

        $class = get_class($e);
        $msg = $e->getMessage();
        // Color the error for giggles
        // Do not show in debug mode
        $str = '';
        $extra = '';

        if ($isDebug || $fullTrace) {
            $toString = trim($e->__toString());

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

        return $html;
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