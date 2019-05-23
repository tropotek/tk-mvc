<?php
namespace Tk\Listener;

use Exception;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Throwable;
use Tk\Config;
use Tk\Console\Console;
use Tk\Event\Subscriber;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Tk\Log;
use Tk\Mail\Gateway;


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
     * @var Gateway
     */
    protected $emailGateway = '';


    /**
     * Constructor.
     *
     * @param $emailGateway
     * @param string|array $email
     * @param string $siteTitle
     */
    public function __construct($emailGateway, $email, $siteTitle = '')
    {
        $this->emailGateway = $emailGateway;
        if (!$siteTitle)
            $siteTitle = Config::getInstance()->getSiteHost();
        if (!is_array($email)) $email = array($email);
        $this->emailList = $email;
        $this->siteTitle = $siteTitle;
    }


    /**
     * 
     * @param ExceptionEvent $event
     */
    public function onException($event)
    {
        $this->emailException($event->getException());
    }

    /**
     *
     * @param ConsoleErrorEvent $event
     */
    public function onConsoleError(ConsoleErrorEvent $event)
    {
        $this->emailException($event->getError());
    }

    /**
     * @param Throwable $e
     */
    protected function emailException($e)
    {
        // TODO: log all errors and send a compiled message periodically (IE: daily, weekly, monthly)
        // This would stop mass emails on major system failures and DOS attacks...

        // These errors are not required they can cause email loops

        if ($e instanceof \Tk\NotFoundHttpException || $e instanceof ResourceNotFoundException || $e instanceof NotFoundHttpException) return;

        // Stop console instance exists email errors they are not needed
        if ($e instanceof \Tk\Console\Exception && $e->getCode() == Console::ERROR_CODE_INSTANCE_EXISTS) return;

        $config = Config::getInstance();
        try {
            if (count($this->emailList)) {
                foreach ($this->emailList as $email) {
                    $message = $config->createMessage();
                    $message->setFrom($email);
                    $message->addTo($email);
                    $subject = $this->siteTitle . ' Error `' . $e->getMessage() . '`';
                    $message->setSubject($subject);
                    $message->setContent(ExceptionListener::getExceptionHtml($e, true));
                    $message->addHeader('X-Exception', get_class($e));
                    $message->set('sig', '');
                    $this->emailGateway->send($message);
                }
            }
        } catch (Exception $ee) { Log::warning($ee->__toString()); }

    }



    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'console.error' => 'onConsoleError',
            KernelEvents::EXCEPTION => 'onException'
        );
    }
    
}