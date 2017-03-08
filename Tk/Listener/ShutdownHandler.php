<?php

namespace Tk\Listener;

use Psr\Log\LoggerInterface;
use Tk\EventDispatcher\SubscriberInterface;

/**
 * Class ShutdownHandler
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ShutdownHandler implements SubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger = null;

    /**
     * @var LoggerInterface
     */
    protected $scriptStartTime = 0;

    /**
     * @var \Dom\Modifier\Filter\PageBytes
     */
    protected $pageBytes = null;

    /**
     * @param LoggerInterface $logger
     * @param int $scriptStartTime
     */
    function __construct(LoggerInterface $logger, $scriptStartTime = 0)
    {
        $this->logger = $logger;
        $this->scriptStartTime = $scriptStartTime;
    }

    /**
     * @param \Dom\Modifier\Filter\PageBytes $pageBytes
     */
    public function setPageBytes($pageBytes)
    {
        $this->pageBytes = $pageBytes;
    }

    /**
     * @param \Tk\Event\ResponseEvent $event
     */
    public function onTerminate(\Tk\Event\ResponseEvent $event)
    {
        if ($this->logger) {
            $this->out('------------------------------------------------');
            if ($this->scriptStartTime > 0)
                $this->out('Load Time: ' . round($this->scriptDuration(), 4) . ' sec');
            $this->out('Peek Mem:  ' . \Tk\File::bytes2String(memory_get_peak_usage(), 4));

            if ($this->pageBytes) {
                foreach (explode("\n", $this->pageBytesToString()) as $line) {
                    $this->out($line);
                }
            }

            //$this->out('------------------------------------------------');
            $this->out('Response Headers:');
            $this->out('  HTTP Code: ' . http_response_code() . ' ');
            foreach (headers_list() as $line) {
                $this->out('  ' . $line);
            }
            //$this->log->debug($this->toString());
            $this->out('------------------------------------------------' . \PHP_EOL);
        }
    }

    private function out($str)
    {
        //$this->logger->info(\Tk\Color::getCliString($str, 'white'));
        $this->logger->info($str);
    }

    /**
     * @return string
     */
    private function pageBytesToString()
    {
        $str = '';
        $j = $this->pageBytes->getJsBytes();
        $c = $this->pageBytes->getCssBytes();
        $h = $this->pageBytes->getHtmlBytes();
        $t = $j + $c +$h;

        $str .= 'Page Sizes:' . \PHP_EOL;
        $str .= sprintf('  JS:      %6s', \Tk\File::bytes2String($j)) . \PHP_EOL;
        $str .= sprintf('  CSS:     %6s', \Tk\File::bytes2String($c)) . \PHP_EOL;
        $str .= sprintf('  HTML:    %6s', \Tk\File::bytes2String($h)) . \PHP_EOL;
        $str .= sprintf('  TOTAL:   %6s', \Tk\File::bytes2String($t));
        return $str;
    }

    /**
     * Get the current script running time in seconds
     *
     * @return string
     */
    protected function scriptDuration()
    {
        return (string)(microtime(true) - $this->scriptStartTime);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(\Tk\Kernel\KernelEvents::TERMINATE => 'onTerminate');
    }

}