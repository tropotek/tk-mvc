<?php
namespace Tk\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * This object manages the Controller/Page Dom\Template rendering
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
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
    public function onController($event)
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
                $this->getDispatcher()->dispatch($e, \Tk\PageEvents::PAGE_INIT);
            }
        }
    }

    /**
     * kernel.view
     * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
     * @throws \Exception
     */
    public function onView($event)
    {
        // View called
        $result = $event->getControllerResult();
        if(!$result && $this->getController()) {
            if ($this->getDispatcher()) {
                $e = new \Tk\Event\Event();
                $e->set('controller', $this->getController());
                $this->getDispatcher()->dispatch($e, \Tk\PageEvents::CONTROLLER_INIT);
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
    public function insertControllerContent($event)
    {
        /** @var \Bs\Controller\Iface $controller */
        $controller = \Tk\Event\Event::findControllerObject($event);
        if (!$controller instanceof \Tk\Controller\Iface) return;

        $controllerTemplate = $controller->getTemplate();
        $pageTemplate = $controller->getPage()->getTemplate();
        $contentVar = $controller->getPage()->getContentVar();
        if (!$contentVar)
            $contentVar = 'content';

        $node = $pageTemplate->getVar($contentVar);
        if (!$node) {
            \Tk\Log::warning('Not content node var=`'.$contentVar.'` found in page template');
            return;
        }

        $mode = 'append';
        if ($node->hasAttribute('data-insert-mode')) {
            if ($node->getAttribute('data-insert-mode') == 'prepend') {
                $mode = 'prepend';
            }
            if ($node->getAttribute('data-insert-mode') == 'replace') {
                $mode = 'replace';
            }
            if ($node->getAttribute('data-insert-mode') == 'insert') {
                $mode = 'insert';
            }
        }


        if ($controllerTemplate instanceof \Dom\Template) {
            $method = $mode.'Template';
            $pageTemplate->$method($contentVar, $controllerTemplate);
        } else if ($controllerTemplate instanceof \Dom\Renderer\RendererInterface) {
            $method = $mode.'Template';
            $pageTemplate->$method($contentVar, $controllerTemplate->getTemplate());
        } else if ($controllerTemplate instanceof \DOMDocument) {
            $method = $mode.'Doc';
            $pageTemplate->$method($contentVar, $controllerTemplate);
        } else if (is_string($controllerTemplate)) {
            $method = $mode.'Html';
            $pageTemplate->$method($contentVar, $controllerTemplate);
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
            KernelEvents::CONTROLLER =>  array('onController', 10),
            KernelEvents::VIEW =>  array('onView', 0),
            \Tk\PageEvents::PAGE_SHOW => array('insertControllerContent', -10)
        );
    }
}