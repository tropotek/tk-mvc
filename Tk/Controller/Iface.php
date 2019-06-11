<?php
namespace Tk\Controller;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
abstract class Iface extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{
    
    /**
     * @var \Tk\Controller\Page
     */
    protected $page = null;

    /**
     * @var string
     */
    protected $pageTitle = '';

    /**
     * @var bool
     */
    protected $showTitle = true;



    /**
     * Iface constructor.
     * @deprecated
     */
    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function getDefaultTitle()
    {
        /** @var \Tk\Request $request */
        $request = $this->getConfig()->getRequest();
        if ($request) {
            $routeName = $request->attributes->set('_route');
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
            // Create a default page
            $this->page = new Page($this->getConfig()->getSitePath() . $this->getConfig()->get('template.public'));
            $this->page->setController($this);
        }
        return $this->page;
    }

    /**
     * @param $page
     * @return $this
     * @deprecated
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
     * @return bool
     */
    public function isShowTitle()
    {
        return $this->showTitle;
    }

    /**
     * @param bool $showTitle
     * @return Iface
     */
    public function setShowTitle($showTitle)
    {
        $this->showTitle = $showTitle;
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

}