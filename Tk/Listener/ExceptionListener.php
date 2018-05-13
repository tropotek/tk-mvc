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
    protected $withTrace = false;

    /**
     * ExceptionListener constructor.
     *
     * @param bool $withTrace
     */
    public function __construct($withTrace = false)
    {
        $this->withTrace = $withTrace;
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onException(ExceptionEvent $event)
    {
        // TODO: If in debug mode show trace if in Live/Test mode only show message...
        $html = self::getExceptionHtml($event->getException(), $this->withTrace);

        $response = Response::create($html);
        $event->setResponse($response);

    }

    /**
     * @param \Exception $e
     * @param bool $fullTrace
     * @return mixed|string
     */
    public static function getExceptionHtml($e, $fullTrace = false)
    {

        $config = \Tk\Config::getInstance();
        $class = get_class($e);
        $msg = $e->getMessage();
        // Color the error for giggles
        // Do not show in debug mode
        $str = '';
        $extra = '';
        $logHtml = '';

        if ($fullTrace) {
            $toString = trim($e->__toString());

            if (is_readable($config->get('log.session'))) {
                $sessionLog = file_get_contents($config->get('log.session'));

                //$theme = new \App\Util\AnsiTheme();
                $converter = new \SensioLabs\AnsiConverter\AnsiToHtmlConverter();
                $sessionLog = $converter->convert($sessionLog);

                $logHtml = sprintf('<div class="content"><p><b>System Log:</b></p>'.
                    '<pre class="console" style="color: #666666; background-color: #000; padding: 10px 15px; font-family: monospace;">%s</pre> <p>&#160;</p></div>', $sessionLog);
            }

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
  overflow: auto;
}
</style>
</head>
<body style="padding: 10px;">
<h1>$class</h1>
<p><strong>$msg $extra</strong></p>
<pre style="">$str</pre>
$logHtml
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