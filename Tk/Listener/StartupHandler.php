<?php
namespace Tk\Listener;

use Psr\Log\LoggerInterface;
use Tk\Event\Subscriber;
use Tk\Request;
use Tk\Session;


/**
 * Class StartupHandler
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class StartupHandler implements Subscriber
{

    /**
     * @var LoggerInterface
     */
    private $logger = null;

    /**
     * @var \Tk\Request
     */
    protected $request = null;

    /**
     * @var \Tk\Session
     */
    protected $session = null;


    /**
     * @param LoggerInterface $logger
     * @param Request $request
     * @param Session $session
     */
    function __construct(LoggerInterface $logger, Request $request = null, Session $session = null)
    {
        $this->logger = $logger;
        $this->request = $request;
        $this->session = $session;
    }

    public function onInit(\Tk\Event\KernelEvent $event)
    {
        if ($this->logger) {
            $this->out('------------------------------------------------');
            $prj = '';

            if (\TK\Config::getInstance()->getSystemProject()) {
                $prj = ' ['.\TK\Config::getInstance()->getSystemProject().']';
            }
            $this->out('- Project: ' . \TK\Config::getInstance()->getSiteTitle() . $prj);
            $this->out('- Date: ' . date('Y-m-d H:i:s'));
            if ($this->request) {
                $this->out('- ' . $this->request->getMethod() . ': ' . $this->request->getUri());
                $this->out('- ' . $this->request->getIp());
                $this->out('- ' . $this->request->getUserAgent());
            }
            if ($this->session) {
                $this->out('- Session ID: ' . $this->session->getId());
                $this->out('- Session Name: ' . $this->session->getName());
            }
            $this->out('- PHP: ' . \PHP_VERSION);
            $this->out('------------------------------------------------');
        }
    }

    private function out($str)
    {
        //$this->logger->info(\Tk\Color::getCliString($str, 'white'));
        $this->logger->info($str);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(\Tk\Kernel\KernelEvents::INIT  => 'onInit');
    }
}