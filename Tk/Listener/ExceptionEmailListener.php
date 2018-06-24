<?php
namespace Tk\Listener;

use Tk\Event\Subscriber;
use Psr\Log\LoggerInterface;


/**
 * Class RouteListener
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class ExceptionEmailListener implements Subscriber
{

    /**
     * @var array
     */
    protected $emailList = '';

    /**
     * @var string
     */
    protected $siteTitle = '';

    /**
     * @var \Tk\Mail\Gateway
     */
    protected $emailGateway = '';


    /**
     * Constructor.
     *
     * @param $emailGateway
     * @param string|array $email
     * @param LoggerInterface $logger A LoggerInterface instance
     * @param string $siteTitle
     */
    public function __construct($emailGateway, $email, $siteTitle = '')
    {
        $this->emailGateway = $emailGateway;
        if (!$siteTitle)
            $siteTitle = \Tk\Config::getInstance()->getSiteHost();
        if (!is_array($email)) $email = array($email);
        $this->emailList = $email;
        $this->siteTitle = $siteTitle;
    }


    /**
     * 
     * @param \Tk\Event\ExceptionEvent $event
     */
    public function onException(\Tk\Event\ExceptionEvent $event)
    {
        // TODO: log all errors and send a compiled message periodically (IE: daily, weekly, monthly)
        // This would stop mass emails on major system failures and DOS attacks...

        // These errors are not required they can cause email loops
        if ($event->getException() instanceof \Tk\NotFoundHttpException) return;


        try {
            if (count($this->emailList)) {
                foreach ($this->emailList as $email) {
                    //$body = $this->createMailTemplate($event->getResponse()->getBody());
                    $body = $this->createMailTemplate(ExceptionListener::getExceptionHtml($event->getException(), true));
                    $subject = $this->siteTitle . ' Error `' . $event->getException()->getMessage() . '`';
                    $message = new \Tk\Mail\Message($body, $subject, $email, $email);
                    $message->addHeader('X-Exception', get_class($event->getException()));
                    $this->emailGateway->send($message);
                }
            }
        } catch (\Exception $ee) { \Tk\Log::warning($ee->__toString()); }

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


    /**
     * Helper Method
     * Make a default HTML template to create HTML emails
     * usage:
     *  $message->setBody($message->createHtmlTemplate($bodyStr));
     *
     * @param string $body
     * @param bool $showFooter
     * @return string
     * @todo: Probably not the best place for this..... Dependant on the App
     */
    protected function createMailTemplate($body, $showFooter = true)
    {
        $config = \Tk\Config::getInstance();
        $request = $config->getRequest();
        $foot = '';
        if (!$config->isCli() && $showFooter) {
            $foot .= sprintf('<i>Page:</i> <a href="%s">%s</a><br/>', $request->getUri()->toString(), $request->getUri()->toString());
            if ($request->getReferer()) {
                $foot .= sprintf('<i>Referer:</i> <span>%s</span><br/>', $request->getReferer()->toString());
            }
            $foot .= sprintf('<i>IP Address:</i> <span>%s</span><br/>', $request->getIp());
            $foot .= sprintf('<i>User Agent:</i> <span>%s</span>', $request->getUserAgent());
        }



        $defaultHtml = sprintf('
<html>
<head>
  <title>Email</title>

<style type="text/css">
body {
  font-family: arial,sans-serif;
  font-size: 80%%;
  padding: 5px;
  background-color: #FFF;
}
table {
  font-size: 0.9em;
}
th, td {
  vertical-align: top;
}
table {

}
th {
  text-align: left;
}
td {
  padding: 4px 5px;
}
.content {
  padding: 0px 0px 0px 20px;
}
p {
  margin: 0px 0px 10px 0px;
  padding: 0px;
}
</style>
</head>
<body>
  <div class="content">
  %s
  <p>&#160;</p>
  </div>
  <hr />
  <div class="footer">
    <p>
      %s
    </p>
  </div>
</body>
</html>', $body, $foot);

        return $defaultHtml;
    }
    
}