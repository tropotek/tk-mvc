<?php
namespace Tk\Listener;

use Tk\Event\Subscriber;
use Tk\Kernel\KernelEvents;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class CrumbsHandler implements Subscriber
{
    /**
     * @var bool
     */
    protected $renderEnabled = true;

    /**
     * @param bool $renderEnabled Set this to false to create your own onShow() Handlers
     */
    public function __construct($renderEnabled = true)
    {
        $this->renderEnabled = $renderEnabled;
    }

    /**
     * @param \Tk\Event\ControllerEvent $event
     */
    public function onController(\Tk\Event\ControllerEvent $event)
    {
        $crumbs = \Tk\Ui\Crumbs::getInstance();
        if (!$crumbs) throw new \Tk\Exception('Error creating crumb instance.');

        /** @var \Tk\Controller\Iface $controller */
        $controller = $event->getController();
        if ($controller instanceof \Tk\Controller\Iface) {
            // ignore adding crumbs if param in request URL
            if ($controller->getRequest()->has(\Tk\Ui\Crumbs::CRUMB_IGNORE)) {
                return;
            }
            $title = $controller->getPageTitle();
            $crumbs->trimByTitle($title);
            $crumbs->addCrumb($title, \Tk\Uri::create());
        }
    }

    /**
     * @param \Tk\Event\Event $event
     */
    public function onShow(\Tk\Event\Event $event)
    {
        if (!$this->renderEnabled) return;
        $controller = $event->get('controller');
        /** @var \Tk\Controller\Page $page */
        $page = $controller->getPage();

        if ($page instanceof \Tk\Controller\Page) {
            $crumbs = \Tk\Ui\Crumbs::getInstance();
            if (!$crumbs) return;

            $template = $page->getTemplate();
            $backUrl = $crumbs->getBackUrl();
            $js = <<<JS
config.backUrl = '$backUrl';
JS;
            $template->appendjs($js, array('data-jsl-priority' => '-999'));

            $js = <<<JS
jQuery(function($) {
  $('a.btn.back').attr('href', config.backUrl);
});
JS;
            $template->appendjs($js);
            if ($template->keyExists('var', 'breadcrumb')) {
                $template->replaceTemplate('breadcrumb', $crumbs->show());
                $template->setChoice('breadcrumb');
            }
        }
    }

    /**
     * @param \Tk\Event\RequestEvent $event
     */
    public function onFinishRequest(\Tk\Event\RequestEvent $event)
    {
        $crumbs = \Tk\Ui\Crumbs::getInstance();
        $crumbs->save();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array('onController', 11),
            \Tk\PageEvents::CONTROLLER_SHOW =>  array('onShow', 0),
            KernelEvents::FINISH_REQUEST => 'onFinishRequest'
        );
    }

}