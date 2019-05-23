<?php
namespace Tk\Listener;

use Tk\Event\Subscriber;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;


/**
 * Class RouteListener
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class LogExceptionListener implements Subscriber
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $fullDump = false;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger A LoggerInterface instance
     * @param bool $fullDump
     */
    public function __construct(LoggerInterface $logger = null, $fullDump = false)
    {
        $this->logger = $logger;
        $this->fullDump = $fullDump;
    }


    /**
     *
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    public function onException($event)
    {
        if (!$this->logger) return;
        $this->logException($event->getException());
    }


    /**
     *
     * @param \Symfony\Component\Console\Event\ConsoleErrorEvent $event
     */
    public function onConsoleError($event)
    {
        if (!$this->logger) return;
        $this->logException($event->getError());
    }

    /**
     * @param \Throwable $e
     */
    protected function logException($e)
    {

        if ($e instanceof \Tk\NotFoundHttpException || $e instanceof ResourceNotFoundException || $e instanceof NotFoundHttpException) {
            $this->logger->error(self::getCallerLine($e) . $e->getMessage());
        } else {

            if ($this->fullDump) {
                if ($e instanceof \Tk\WarningException) {
                    $this->logger->warning(self::getCallerLine($e) . $e->__toString());
//            } else if ($e instanceof \Tk\NotFoundHttpException) {
//                $this->logger->error(self::getCallerLine($e) . $e->getMessage());
                } else {
                    $this->logger->error(self::getCallerLine($e) . $e->__toString());
                }
            } else {
                if ($e instanceof \Tk\WarningException) {
                    $this->logger->warning(self::getCallerLine($e) . $e->getMessage());
                } else {
                    $this->logger->error(self::getCallerLine($e) . $e->getMessage());
                }
            }
        }
    }


    /**
     * @param \Throwable $e
     * @return string
     */
    private static function getCallerLine($e)
    {
        $str = '';
        if ($e) {
            $config = \Tk\Config::getInstance();
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
            'console.error' => 'onConsoleError',
            \Symfony\Component\HttpKernel\KernelEvents::EXCEPTION => 'onException'
        );
    }
    
    
}