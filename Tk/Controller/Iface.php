<?php

namespace Tk\Controller;

use Dom\Template;
use Tk\ConfigTrait;
use Tk\Request;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
abstract class Iface extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{
    use ConfigTrait;

    /**
     * @var Page
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
     * @return string
     */
    public function getDefaultTitle()
    {
        /** @var Request $request */
        $request = $this->getConfig()->getRequest();
        if ($request) {
            $routeName = $request->attributes->get('_route');
            return ucwords(trim(str_replace('-', ' ', $routeName)));
        }
        return '';
    }

    /**
     * Get a new instance of the page to display the content in.
     * NOTE: Override to load your own page objects
     *
     * @return Page
     */
    public function getPage()
    {
        if (!$this->page) {
            $this->page = new Page($this->getConfig()->getSitePath() . $this->getConfig()->get('template.public'));
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
     * @return Template
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

}