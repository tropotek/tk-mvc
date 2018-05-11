<?php
namespace Tk\Listener;

use Tk\Event\ExceptionEvent;
use Tk\Event\Subscriber;
use Psr\Log\LoggerInterface;
use Tk\Response;


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
     * @param ExceptionEvent $event
     */
    public function onException(ExceptionEvent $event)
    {
        if (!$this->logger) return;

        $e = $event->getException();

        if ($this->fullDump) {
            if ($e instanceof \Tk\WarningException) {
                $this->logger->warning(self::getCallerLine($e) . $event->getException()->__toString());
            } else {
                $this->logger->error(self::getCallerLine($e) . $event->getException()->__toString());
            }
        } else {
            if ($e instanceof \Tk\WarningException) {
                $this->logger->warning(self::getCallerLine($e) . $event->getException()->getMessage());
            } else {
                $this->logger->error(self::getCallerLine($e) . $event->getException()->getMessage());
            }
        }

    }

    /**
     * @param \Exception $e
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
            \Tk\Kernel\KernelEvents::EXCEPTION => 'onException'
        );
    }
    
    
}