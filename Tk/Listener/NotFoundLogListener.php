<?php
namespace Tk\Listener;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;
use Tk\Config;
use Tk\Event\Subscriber;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;


/**
 * Class RouteListener
 *
 * Example:
 *
 *  $notFoundLogPath = dirname($config->getLogPath()) . '/404.log';
 *  if (!is_file($notFoundLogPath)) {
 *    file_put_contents($notFoundLogPath, '');
 *  }
 *  $log = new Logger('notfound');
 *  $handler = new StreamHandler($notFoundLogPath, Logger::NOTICE);
 *  $log->pushHandler($handler);
 *  $dispatcher->addSubscriber(new NotFoundLogListener($log));
 *
 *
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class NotFoundLogListener implements Subscriber
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
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
    public function onException($event)
    {
        if (!$this->logger) return;
        $e = $event->getException();
        if (!$e instanceof \Tk\NotFoundHttpException && !$e instanceof ResourceNotFoundException && !$e instanceof NotFoundHttpException) return;
        $this->logger->notice(self::getCallerLine($e) . $e->getMessage());

    }


    /**
     * @param Throwable $e
     * @return string
     */
    private static function getCallerLine($e)
    {
        $str = '';
        if ($e) {
            $config = Config::getInstance();
            $line = $e->getLine();
            $file = str_replace($config->getSitePath(), '', $e->getFile());
            $str = sprintf('[%s:%s] ', $file, $line);
        }
        return $str;
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