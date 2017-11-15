<?php
namespace Tk\Controller;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Page extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{

    /**
     * The page template var name to place the controller content into
     * @var string
     */
    protected $contentVar = 'content';

    /**
     * @var \App\Controller\Iface
     */
    protected $controller = null;

    /**
     * @todo: refactor the template path into a class or remove...
     * @var string
     * @deprecated ??? still working out a better solution to template paths
     */
    protected $templatePath = '';



    /**
     * Set the page Content
     *
     * @param string|\Dom\Template|\Dom\Renderer\RendererInterface|\DOMDocument $content
     * @return Page
     * @see \App\Listener\ActionPanelHandler
     * @deprecated 
     */
    public function setPageContent($content)
    {
        // Allow people to hook into the controller result.
        $event = new \Tk\Event\Event();
        $event->set('content', $content);
        $event->set('controller', $this->getController());
        \App\Factory::getEventDispatcher()->dispatch(\Tk\PageEvents::CONTROLLER_SHOW, $event);

        if (!$content) return $this;
        if ($content instanceof \Dom\Template) {
            $this->getTemplate()->appendTemplate($this->getContentVar(), $content);
        } else if ($content instanceof \Dom\Renderer\RendererInterface) {
            $this->getTemplate()->appendTemplate($this->getContentVar(), $content->getTemplate());
        } else if ($content instanceof \DOMDocument) {
            $this->getTemplate()->insertDoc($this->getContentVar(), $content);
        } else if (is_string($content)) {
            $this->getTemplate()->insertHtml($this->getContentVar(), $content);
        }
        return $this;
    }


    /**
     * Init the page ????
     */
    public function init() { }

    /**
     * The default page show method
     * 
     * @return \Dom\Template
     */
    public function show()
    {
        /* @var \Dom\Template $template */
        $template = $this->getTemplate();

        if ($this->getConfig()->get('site.meta.keywords')) {
            $template->appendMetaTag('keywords', $this->getConfig()->get('site.meta.keywords'));
        }
        if ($this->getConfig()->get('site.meta.description')) {
            $template->appendMetaTag('description', $this->getConfig()->get('site.meta.description'));
        }

        if ($this->getConfig()->get('site.global.js')) {
            $template->appendJs($this->getConfig()->get('site.global.js'));
        }
        if ($this->getConfig()->get('site.global.css')) {
            $template->appendCss($this->getConfig()->get('site.global.css'));
        }

        $template->appendMetaTag('tk-author', 'http://www.tropotek.com/, http://www.phpdomtemplate.com/', $template->getTitleElement());
        $template->appendMetaTag('tk-project', 'tk2uni', $template->getTitleElement());
        $template->appendMetaTag('tk-version', '1.0', $template->getTitleElement());
        
        if ($this->getConfig()->get('site.title')) {
            $template->setAttr('siteName', 'title', $this->getConfig()->get('site.title'));
            $template->setAttr('siteTitle', 'title', $this->getConfig()->get('site.title'));
            $template->insertText('siteTitle', $this->getConfig()->get('site.title'));
            $template->setTitleText(trim($template->getTitleText() . ' - ' . $this->getConfig()->get('site.title'), '- '));
        }


        // TODO: create a listener for this????
        $siteUrl = $this->getConfig()->getSiteUrl();
        $dataUrl = $this->getConfig()->getDataUrl();
        $templateUrl = $this->getConfig()->getTemplateUrl();

        $js = <<<JS
var config = {
  siteUrl : '$siteUrl',
  dataUrl : '$dataUrl',
  templateUrl: '$templateUrl',
  jquery: {
    dateFormat: 'dd/mm/yy'    
  },
  bootstrap: {
    dateFormat: 'dd/mm/yyyy'    
  }
};
JS;
        $template->appendJs($js, array('data-jsl-priority' => -1000));
       
        // Set page title
        if ($this->getController() && $this->getController()->getPageTitle()) {
            $template->setTitleText(trim($this->getController()->getPageTitle() . ' - ' . $template->getTitleText(), '- '));
            $template->insertText('pageHeading', $this->getController()->getPageTitle());
            $template->setChoice('pageHeading');
        }
        if ($this->getConfig()->isDebug()) {
            $template->setTitleText(trim('DEBUG: ' . $template->getTitleText(), '- '));
        }
        
        return $template;
    }

    /**
     * @return string
     */
    public function getContentVar()
    {
        return $this->contentVar;
    }

    /**
     * @param string $contentVar
     */
    public function setContentVar($contentVar)
    {
        $this->contentVar = $contentVar;
    }
    
    /**
     * @return Iface
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param Iface $controller
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Get the global config object.
     *
     * @return \Tk\Config
     */
    public function getConfig()
    {
        return \Tk\Config::getInstance();
    }

    /**
     *
     * @return string
     * @todo refactor
     * @deprecated Thing of a better way to handle template/theme paths
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * DomTemplate magic method example
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $html = <<<HTML
<html>
<head>
  <title></title>
</head>
<body>
  <div var="content"></div>
</body>
</html>
HTML;
        return \Dom\Loader::load($html);
        // OR FOR A FILE
        //return \Dom\Loader::loadFile($this->getTemplatePath().'/public.xtpl');
    }

}