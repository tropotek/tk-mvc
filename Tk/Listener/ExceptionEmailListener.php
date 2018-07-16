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

        $config = \Tk\Config::getInstance();
        try {
            if (count($this->emailList)) {
                foreach ($this->emailList as $email) {
                    $message = $config->createMessage();
                    $message->setFrom($email);
                    $message->addTo($email);
                    $subject = $this->siteTitle . ' Error `' . $event->getException()->getMessage() . '`';
                    $message->setSubject($subject);
                    $message->set('content', ExceptionListener::getExceptionHtml($event->getException(), true));
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
    
}