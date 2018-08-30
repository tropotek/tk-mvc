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
        $this->emailException($event->getException());
    }

    /**
     *
     * @param \Symfony\Component\Console\Event\ConsoleErrorEvent $event
     */
    public function onConsoleError(\Symfony\Component\Console\Event\ConsoleErrorEvent $event)
    {
        $this->emailException($event->getError());
    }

    /**
     * @param \Throwable $e
     */
    protected function emailException($e)
    {
        // TODO: log all errors and send a compiled message periodically (IE: daily, weekly, monthly)
        // This would stop mass emails on major system failures and DOS attacks...

        // These errors are not required they can cause email loops
        if ($e instanceof \Tk\NotFoundHttpException) return;
        // Stop console instance exsists email errors they are not needed
        if ($e instanceof \Tk\Console\Exception && $e->getCode() == \Tk\Console\Console::ERROR_CODE_INSTANCE_EXISTS) return;

        $config = \Tk\Config::getInstance();
        try {
            if (count($this->emailList)) {
                foreach ($this->emailList as $email) {
                    $message = $config->createMessage();
                    $message->setFrom($email);
                    $message->addTo($email);
                    $subject = $this->siteTitle . ' Error `' . $e->getMessage() . '`';
                    $message->setSubject($subject);
                    $message->set('content', ExceptionListener::getExceptionHtml($e, true));
                    $message->addHeader('X-Exception', get_class($e));
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
            'console.error' => 'onConsoleError',
            \Tk\Kernel\KernelEvents::EXCEPTION => 'onException'
        );
    }
    
}