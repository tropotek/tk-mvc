<?php
namespace Tk\Listener;

use Psr\Log\LoggerInterface;
use Tk\Event\Subscriber;
use Tk\Request;
use Tk\Session;
use Tk\Event\GetResponseEvent;


/**
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

        $this->out('');
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
                // Doing this live is a security risk
                if ($config->isDebug() && $this->request->getMethod() == 'POST' && strlen($config->getRequest()->getRawPostData()) <= 255) {
                    $this->logger->debug('- POST Data: ' . $config->getRequest()->getRawPostData());
                }
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


    /**
     * Set the global institution into the config as a central data access point
     * If no institution is set then we know we are either an admin or public user...
     *
     * @param GetResponseEvent $event
     * @throws \Tk\Exception
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     * @throws \Tk\Db\Exception
     */
    public function onRequest(GetResponseEvent $event)
    {
        $config = \Tk\Config::getInstance();
        if ($config->getRequest()->getAttribute('_route')) {
            $routeCollection = $config->getRouteCollection();
            if ($routeCollection) {
                $route = $routeCollection->get($config->getRequest()->getAttribute('_route'));
                if ($route)
                    $this->out('- Controller: ' . $route->getController());
            }
        }

    }

    /**
     * @param $str
     */
    private function out($str)
    {
        $this->logger->info($str);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Tk\Kernel\KernelEvents::INIT  => 'onInit',
            \Tk\Kernel\KernelEvents::REQUEST => array('onRequest', -1),
            \Symfony\Component\Console\ConsoleEvents::COMMAND  => 'onCommand'
        );
    }
}