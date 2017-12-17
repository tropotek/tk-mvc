<?php
namespace Tk\Controller;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
abstract class Iface extends \Dom\Renderer\Renderer
{
    
    /**
     * @var \App\Page\Iface
     */
    protected $page = null;

    /**
     * @var string
     */
    protected $pageTitle = '';



    /**
     * @return string
     */
    public function getDefaultTitle()
    {
        /** @var \Tk\Request $request */
        $request = $this->getConfig()->getRequest();
        if ($request) {
            $routeName = $request->getAttribute('_route');
            return ucwords(trim(str_replace('-', ' ', $routeName)));
        }
        return '';
    }

    /**
     * Get a new instance of the page to display the content in.
     *
     * NOTE: This is the default, override to load your own page objects
     *
     * @return \Tk\Controller\Page
     */
    public function getPage()
    {
        if (!$this->page) {
            $this->page = new \Tk\Controller\Page();
            $this->page->setController($this);
        }
        return $this->page;
    }

    /**
     * @param $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * For compatibility
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();


        return $template;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setPageTitle($title)
    {
        $this->pageTitle = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
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
     * @return \Tk\Request
     */
    public function getRequest()
    {
        return $this->getConfig()->getRequest();
    }

    /**
     * @return \Tk\Session
     */
    public function getSession()
    {
        return $this->getConfig()->getSession();
    }


    /**
     * DomTemplate magic method example
     *
     * @return \Dom\Template
     */
//    public function __makeTemplate()
//    {
//        $html = <<<HTML
//<div></div>
//HTML;
//        return \Dom\Loader::load($html);
//        // OR FOR A FILE
//        //return \Dom\Loader::loadFile($this->getTemplatePath().'/public.xtpl');
//    }



    /**
     * @return string
     * @todo Refactor
     * @deprecated
     */
//    public function getTemplateUrl()
//    {
//        return $this->getConfig()->getSitePath() . $this->getConfig()->get('template.public');
//    }

    /**
     * @return string
     * @todo Refactor
     * @deprecated
     */
//    public function getTemplatePath()
//    {
//        if ($this->getPage()) {
//            return $this->getPage()->getTemplatePath();
//        }
//    }

//    /**
//     * @return string
//     * @deprecated
//     */
//    public function getXtplPath()
//    {
//        vd($this->getPage()->getTemplate()->getTemplatePath());
//        return $this->getConfig()->getSitePath() . $this->getConfig()->get('template.xtpl.path');
//    }
}