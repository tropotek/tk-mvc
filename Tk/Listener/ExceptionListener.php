<?php
namespace Tk\Listener;

use Dom\Modifier\Modifier;
use Dom\Template;
use Exception;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Tk\Config;
use Tk\Controller\Iface;
use Tk\Event\Subscriber;
use Tk\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;


/**
 * @author Michael Mifsud <http://www.tropotek.com/>
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
     * The controller to instantiate on errors
     * @var string
     */
    protected $controllerClass = '';


    /**
     * @param bool $withTrace
     * @param string $controllerClass
     */
    public function __construct($withTrace = false, $controllerClass = '')
    {
        $this->withTrace = $withTrace;
        $this->controllerClass = $controllerClass;
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onException($event)
    {
        $response = null;
        $result = null;
        if ($this->controllerClass && class_exists($this->controllerClass)) {
            /** @var Iface $con */
            $con = new $this->controllerClass();
            if (method_exists($con, 'doDefault')) {
                $con->doDefault($event->getRequest(), $event->getException(), $this->withTrace);
                $result = $con->show();
                if ($result instanceof Template && Config::getInstance()->get('dom.modifier')) {
                    if ($event->getException() instanceof \Tk\NotFoundHttpException || $event->getException() instanceof ResourceNotFoundException || $event->getException() instanceof NotFoundHttpException) {
                        $result->setVisible('404');
                        $result->insertText('code', '404');
                    } else {
                        $result->setVisible('default');
                    }

                    /** @var Modifier $domModifier */
                    $domModifier = Config::getInstance()->get('dom.modifier');
                    $doc = $result->getDocument();
                    $domModifier->execute($doc);
                    $response = new Response($result->toString());
                }
            }
        } else {
            $result = self::getExceptionHtml($event->getException(), $this->withTrace);
            $response = Response::create($result);
        }

        if ($response)
            $event->setResponse($response);

    }


    /**
     * @param Exception $e
     * @param bool $withTrace
     * @return mixed|string
     */
    public static function getExceptionHtml($e, $withTrace = false)
    {

        $config = Config::getInstance();
        $class = get_class($e);
        $msg = $e->getMessage();
        $str = '';
        $extra = '';
        $logHtml = '';

        if ($withTrace) {
            $toString = trim($e->__toString());
            if (is_readable($config->get('log.session'))) {
                $sessionLog = file_get_contents($config->get('log.session'));
                // Add to composer require: "sensiolabs/ansi-to-html": "~1.0",
                if (class_exists('SensioLabs\AnsiConverter\AnsiToHtmlConverter')) {
                    $converter = new AnsiToHtmlConverter();
                    $sessionLog = $converter->convert($sessionLog);
                }
                $logHtml = sprintf('<div class="content"><p><b>System Log:</b></p>'.
                    '<pre class="console" style="color: #666666; background-color: #000; padding: 10px 15px; font-family: monospace;">%s</pre> <p>&#160;</p></div>',
                    $sessionLog);
            }

            $str = str_replace(array("&lt;?php&nbsp;<br />", 'color: #FF8000'), array('', 'color: #666'),
                highlight_string("<?php \n" . $toString, true));
            $extra = sprintf('<br/>in <em>%s:%s</em>',  $e->getFile(), $e->getLine());
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

        $html = str_replace(Config::getInstance()->getSitePath(), '', $html);

        return $html;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onException'
        );
    }
    
    
}