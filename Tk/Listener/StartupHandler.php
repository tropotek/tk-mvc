<?php
namespace Tk\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Tk\Event\Subscriber;
use Tk\Request;
use Tk\Session;


/**
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
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

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onInit($event)
    {
        $this->init();
    }

    /**
     * @param \Symfony\Component\Console\Event\ConsoleCommandEvent $event
     */
    public function onCommand($event)
    {
        $this->init();
    }

    /**
     */
    private function init()
    {
        if (!$this->logger) return;
        $config = \Tk\Config::getInstance();

        //$this->out('');
        $this->out(self::$SCRIPT_START);
        $prj = '';
        if ($config->getSystemProject()) {
            $prj = sprintf(' [%s]', $config->getSystemProject());
        }
        $ver = '';
        if ($config->getVersion()) {
            $ver = sprintf(' [v%s]', $config->getVersion());
        }

        $this->out('- Project: ' . $config->getSiteTitle() . $prj . $ver);
        $this->out('- Date: ' . date('Y-m-d H:i:s'));
        if ($this->request) {
            if (!$config->isCli()) {
                $this->out('- Host: ' . $this->request->getTkUri()->getScheme() . '://' . $this->request->getTkUri()->getHost());
                $this->out('- ' . $this->request->getMethod() . ': ' . $this->request->getTkUri()->toString(false, false));
                // Doing this live is a security risk
                if ($config->isDebug() && $this->request->getMethod() == 'POST' && strlen($config->getRequest()->getRawPostData()) <= 255) {
                    $this->logger->debug('- POST Data: ' . $config->getRequest()->getRawPostData());
                }
                $this->out('- Client IP: ' . $this->request->getClientIp());
                $this->out('- User Agent: ' . $this->request->getUserAgent());
            } else {
                $this->out('- CLI: ' . implode(' ', $this->request->server->get('argv')));
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
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onRequest($event)
    {
        $config = \Bs\Config::getInstance();
        if ($config->getRequest()->attributes->has('_route')) {
            $routeCollection = $config->getRouteCollection();
            if ($routeCollection) {
                $route = $routeCollection->get($config->getRequest()->attributes->get('_route'));
                if ($route)
                    $this->out('- Controller: ' . $route->getDefault('_controller'));
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
            KernelEvents::REQUEST => array(array('onInit', 255), array('onRequest', 32)),
            \Symfony\Component\Console\ConsoleEvents::COMMAND  => 'onCommand'
        );
    }
}