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
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class StartupHandler implements Subscriber
{
    public static $SCRIPT_START_CHAR = '¤';
    public static $SCRIPT_END_CHAR = '×';
    public static $SCRIPT_START =  '¤--------------------- Start ----------------------';
    public static $SCRIPT_END   =  '--------------------- Shutdown -------------------×';
    public static $SCRIPT_LINE  =  '---------------------------------------------------';
//    public static $SCRIPT_START =  '┌──────────────────   Start  ────────────────┐';
//    public static $SCRIPT_END   =  '└────────────────── Shutdown ────────────────┘';
//    public static $SCRIPT_LINE  =  '├────────────────────────────────────────────┤';
    
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
        $this->init();
    }

    public function onCommand(\Symfony\Component\Console\Event\ConsoleCommandEvent $event)
    {
        $this->init();
    }

    /**
     * @throws \Tk\Exception
     */
    private function init()
    {
        if (!$this->logger) return;
        $config = \Tk\Config::getInstance();

        $this->out(self::$SCRIPT_START);
        $prj = '';

        if ($config->getSystemProject()) {
            $prj = ' ['.$config->getSystemProject().']';
        }
        $this->out('- Project: ' . $config->getSiteTitle() . $prj);
        $this->out('- Date: ' . date('Y-m-d H:i:s'));
        if ($this->request) {
            if (!$config->isCli()) {
                $this->out('- Host: ' . $this->request->getUri()->getScheme() . '://' . $this->request->getUri()->getHost());
                $this->out('- ' . $this->request->getMethod() . ': ' . $this->request->getUri()->toString(false, false));
                $this->out('- Client IP: ' . $this->request->getIp());
                $this->out('- User Agent: ' . $this->request->getUserAgent());
            } else {
                $this->out('- CLI: ' . implode(' ', $this->request->getServerParam('argv')));
            }
        }
        if ($this->session) {
            $this->out('- Session Name: ' . $this->session->getName());
            $this->out('- Session ID: ' . $this->session->getId());
        }
        $this->out('- PHP: ' . \PHP_VERSION);
        $this->out(self::$SCRIPT_LINE);

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
        return array(
            \Tk\Kernel\KernelEvents::INIT  => 'onInit',
            \Symfony\Component\Console\ConsoleEvents::COMMAND  => 'onCommand'
        );
    }
}