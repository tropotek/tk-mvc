<?php
namespace Tk\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This object manages the Controller/Page Dom\Template rendering
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class PageHandler implements EventSubscriberInterface
{
    
    /**
     * @var null|\Tk\Controller\Iface
     */
    private $controller = null;

    /**
     * @var null|EventDispatcherInterface
     */
    private $dispatcher = null;


    /**
     * constructor.
     * @param null|EventDispatcherInterface $dispatcher
     */
    public function __construct($dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * kernel.controller
     * @param \Symfony\Component\HttpKernel\Event\ControllerEvent $event
     */
    public function onController(\Symfony\Component\HttpKernel\Event\ControllerEvent $event)
    {
        $controller = \Tk\Event\Event::findControllerObject($event);
        if ($controller instanceof \Tk\Controller\Iface) {
            $this->controller = $controller;
            if (!$controller->getPageTitle()) {     // Set a default page Title for the crumbs
                $controller->setPageTitle($controller->getDefaultTitle());
            }
            // Init the page, so the DomLoader knows the template path.
            $controller->getPage();
            if ($this->getDispatcher()) {
                $e = new \Tk\Event\Event();
                $e->set('controller', $this->getController());
                $this->getDispatcher()->dispatch($e,\Tk\PageEvents::PAGE_INIT);
            }
        }
    }

    /**
     * kernel.view
     * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
     * @throws \Exception
     */
    public function onView(\Symfony\Component\HttpKernel\Event\ViewEvent $event)
    {
        // View called
        $result = $event->getControllerResult();
        if(!$result && $this->getController()) {
            if ($this->getDispatcher()) {
                $e = new \Tk\Event\Event();
                $e->set('controller', $this->getController());
                $this->getDispatcher()->dispatch($e,\Tk\PageEvents::CONTROLLER_INIT);
            }

            // Controller::show()
            preg_match('/::do([A-Z][a-zA-Z0-9_]+)$/', $event->getRequest()->attributes->get('_controller'), $regs);
            $show = 'show'; 
            if (!empty($regs[1]) && method_exists($this->getController(), 'show'.$regs[1]))
                $show = 'show'.$regs[1];

            if (method_exists($this->getController(), $show)) {
                $this->getController()->$show();
            }
            
            // Allow people to hook into the controller result.
            if ($this->getDispatcher()) {
                $e = new \Tk\Event\Event();
                $e->set('controller', $this->getController());
                $this->getDispatcher()->dispatch($e, \Tk\PageEvents::CONTROLLER_SHOW);
            }

            // Page::show() This will also insert the controller template into the page
            $this->getController()->getPage()->show();

            if ($this->getDispatcher()) {
                $e = new \Tk\Event\Event();
                $e->set('controller', $this->getController());
                $this->getDispatcher()->dispatch($e, \Tk\PageEvents::PAGE_SHOW);
            }

            $event->setControllerResult($this->getController()->getPage()->getTemplate());
        }
    }


    /**
     * @param \Tk\Event\Event $event
     */
    public function insertControllerContent(\Tk\Event\Event $event)
    {
        /** @var \Bs\Controller\Iface $controller */
        $controller = \Tk\Event\Event::findControllerObject($event);
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
            $pageTemplate->appendDoc($contentVar, $controllerTemplate);
            //$pageTemplate->insertDoc($contentVar, $controllerTemplate);
        } else if (is_string($controllerTemplate)) {
            $pageTemplate->appendHtml($contentVar, $controllerTemplate);
            //$pageTemplate->insertHtml($contentVar, $controllerTemplate);
        }
        $event->set('page.template', $pageTemplate);

    }


    /**
     * @return null|EventDispatcherInterface
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
            \Symfony\Component\HttpKernel\KernelEvents::CONTROLLER =>  array('onController', 10),
            \Symfony\Component\HttpKernel\KernelEvents::VIEW =>  array('onView', 0),
            \Tk\PageEvents::PAGE_SHOW => array('insertControllerContent', -10)
        );
    }
}