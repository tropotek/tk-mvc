<?php
namespace Tk\Listener;

use Tk\Event\Subscriber;
use Tk\Kernel\KernelEvents;

/**
 * This object helps cleanup the structure of the controller code
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class PageHandler implements Subscriber
{
    
    /**
     * @var null|\Tk\Controller\Iface
     */
    private $controller = null;
    /**
     * @var null|\Tk\Controller\Page
     */
    private $page = null;

    /**
     * @var null|\Tk\Event\Dispatcher
     */
    private $dispatcher = null;


    /**
     * constructor.
     * @param null $dispatcher
     */
    public function __construct($dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * kernel.controller
     * @param \Tk\Event\ControllerEvent $event
     */
    public function onController(\Tk\Event\ControllerEvent $event)
    {
        /** @var \Tk\Controller\Iface $controller */
        $controller = $event->getController();
        if ($controller instanceof \Tk\Controller\Iface) {
            $this->controller = $controller;
            if (!$controller->getPageTitle()) {     // Set a default page Title for the crumbs
                $controller->setPageTitle($controller->getDefaultTitle());
            }
            //$controller->getPage()->setTemplatePath($controller->getPageTemplatePath());

            if ($this->getDispatcher()) {
                $e = new \Tk\Event\Event();
                $e->set('controller', $this->getController());
                $this->getDispatcher()->dispatch(\Tk\PageEvents::PAGE_INIT, $e);
            }
        }
    }

    /**
     * kernel.view
     * @param \Tk\Event\ControllerResultEvent $event
     * @throws \Exception
     */
    public function onView(\Tk\Event\ControllerResultEvent $event)
    {
        // View called
        $result = $event->getControllerResult();
        if(!$result && $this->getController()) {
            if ($this->getDispatcher()) {
                $e = new \Tk\Event\Event();
                $e->set('controller', $this->getController());
                $this->getDispatcher()->dispatch(\Tk\PageEvents::CONTROLLER_INIT, $e);
            }

            // Controller::show()
            preg_match('/::do([A-Z][a-zA-Z0-9_]+)$/', $event->getRequest()->getAttribute('_controller'), $regs);
            $show = 'show'; 
            if (!empty($regs[1]) && method_exists($this->getController(), 'show'.$regs[1]))
                $show = 'show'.$regs[1];

            $this->getController()->$show();

            // Allow people to hook into the controller result.
            if ($this->getDispatcher()) {
                $e = new \Tk\Event\Event();
                $e->set('controller', $this->getController());
                $this->getDispatcher()->dispatch(\Tk\PageEvents::CONTROLLER_SHOW, $e);
            }

            // Page::show() This will also insert the controller template into the page
            $this->getController()->getPage()->show();

            if ($this->getDispatcher()) {
                $e = new \Tk\Event\Event();
                $e->set('controller', $this->getController());
                $this->getDispatcher()->dispatch(\Tk\PageEvents::PAGE_SHOW, $e);
            }

            $event->setControllerResult($this->getController()->getPage()->getTemplate());
        }
    }


    /**
     * @param \Tk\Event\Event $event
     * @throws \Dom\Exception
     */
    public function insertControllerContent(\Tk\Event\Event $event)
    {
        /** @var \Bs\Controller\Iface $controller */
        $controller = $event->get('controller');
        if (!$controller instanceof \Tk\Controller\Iface) return;

        $controllerTemplate = $controller->getTemplate();
        $pageTemplate = $controller->getPage()->getTemplate();
        $contentVar = $controller->getPage()->getContentVar();
        if (!$contentVar)
            $contentVar = 'content';

        if ($controllerTemplate instanceof \Dom\Template) {
            $pageTemplate->appendTemplate($contentVar, $controllerTemplate);
        } else if ($controllerTemplate instanceof \Dom\Renderer\RendererInterface) {
            $pageTemplate->appendTemplate($contentVar, $controllerTemplate->getTemplate());
        } else if ($controllerTemplate instanceof \DOMDocument) {
            $pageTemplate->insertDoc($contentVar, $controllerTemplate);
        } else if (is_string($controllerTemplate)) {
            $pageTemplate->insertHtml($contentVar, $controllerTemplate);
        }
        $event->set('page.template', $pageTemplate);

    }


    /**
     * @return null|\Tk\Event\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return null|\Tk\Controller\Iface
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * getSubscribedEvents
     * 
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER =>  array('onController', 10),
            KernelEvents::VIEW =>  array('onView', 0),
            \Tk\PageEvents::PAGE_SHOW => array('insertControllerContent', -10)
        );
    }
}