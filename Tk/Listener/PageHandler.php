<?php
namespace Tk\Listener;

use Tk\Event\Subscriber;
use Tk\Kernel\KernelEvents;

/**
 * This object helps cleanup the structure of the controller code
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
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
     * kernel.request
     * @param \Tk\Event\GetResponseEvent $event
     */
    public function onRequest(\Tk\Event\GetResponseEvent $event)
    {
        // Controller not created yet
        //vd($event->getRequest()->getAttributes());
    }

    /**
     * kernel.controller
     * @param \Tk\Event\ControllerEvent $event
     */
    public function onController(\Tk\Event\ControllerEvent $event)
    {
        // Controller created before this call
        
        /** @var \Tk\Controller\Iface $controller */
        $controller = $event->getController();
        if ($controller instanceof \Tk\Controller\Iface) {
            $this->controller = $controller;
            // Page::__construct()
            $this->createPage($this->getController());
            
            // Page::init()
            $this->getPage()->init();
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
     */
    public function onView(\Tk\Event\ControllerResultEvent $event)
    {
        // View called
        $result = $event->getControllerResult();
        if(!$result && $this->getController()) {
            // Controller::show()
            preg_match('/::do([A-Z][a-zA-Z0-9_]+)$/', $event->getRequest()->getAttribute('_controller'), $regs);
            $show = 'show'; 
            if (!empty($regs[1]) && method_exists($this->getController(), 'show'.$regs[1]))
                $show = 'show'.$regs[1];
            $this->getController()->$show();
            
            $this->setPageContent($this->getController());
            
            // Page::show()
            $this->getPage()->show();
            
            if ($this->getDispatcher()) {
                $e = new \Tk\Event\Event();
                $e->set('controller', $this->getController());
                $this->getDispatcher()->dispatch(\Tk\PageEvents::PAGE_SHOW, $e);
            }

            // Send the template to the final response handler for processing
            $event->setControllerResult($this->getController()->getPage()->getTemplate());
        }
    }

    /**
     * Set the page Content
     *
     * @param \Tk\Controller\Iface $controller
     * @see \App\Listener\ActionPanelHandler
     */
    public function setPageContent($controller)
    {
        $page = $controller->getPage();
        $content = $controller->getTemplate();
        if (!$page) return;
        // Allow people to hook into the controller result.
        if ($this->getDispatcher()) {
            $e = new \Tk\Event\Event();
            $e->set('content', $content);
            $e->set('controller', $page->getController());
            $this->getDispatcher()->dispatch(\Tk\PageEvents::CONTROLLER_SHOW, $e);
            $content = $e->get('content');
        }
        if (!$content) return;
        
        $pageTemplate = $page->getTemplate();
        if ($content instanceof \Dom\Template) {
            $pageTemplate->appendTemplate($page->getContentVar(), $content);
        } else if ($content instanceof \Dom\Renderer\RendererInterface) {
            $pageTemplate->appendTemplate($page->getContentVar(), $content->getTemplate());
        } else if ($content instanceof \DOMDocument) {
            $pageTemplate->insertDoc($page->getContentVar(), $content);
        } else if (is_string($content)) {
            $pageTemplate->insertHtml($page->getContentVar(), $content);
        }
    }


    /**
     * @param \Tk\Event\Event $event
     */
    public function onShow(\Tk\Event\Event $event)
    {
        // App only
//        if ($this->controller instanceof \App\Controller\AdminIface && $this->controller->getActionPanel()) {
//            $this->controller->getTemplate()->prependTemplate($this->controller->getTemplate()->getRootElement(), $this->controller->getActionPanel()->show());
//        }
    }


    /**
     *
     * @param \Tk\Controller\Iface $controller
     * @return \Tk\Controller\Page
     * @throws \Tk\Exception
     */
    public function createPage($controller)
    {
        $this->page = $controller->getPage();     // TODO: should this be the way we handle this (How do we override it)
        if (!$this->page) throw new \Tk\Exception('Page cannot be created.');
        return $this->page;
    }

    /**
     * @return null|\Tk\Event\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
    
    /**
     * @return null|\Tk\Controller\Page
     */
    public function getPage()
    {
        return $this->page;
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
            KernelEvents::REQUEST =>  array('onRequest', 0),
            KernelEvents::CONTROLLER =>  array('onController', 10),
            \App\AppEvents::SHOW =>  array('onShow', 0),
            KernelEvents::VIEW =>  array('onView', 0)
        );
    }
}