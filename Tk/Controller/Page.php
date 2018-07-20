<?php
namespace Tk\Controller;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Page extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{

    /**
     * @var \Tk\Controller\Iface
     * @deprecated
     */
    protected $controller = null;

    /**
     * @var string
     */
    protected $templatePath = '';



    /**
     * Init the page ????
     * @deprecated
     */
    public function init() { }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();
        return $template;
    }

    /**
     * Get the Dom\Template var to insert the controller template into
     *
     * @return string
     */
    public function getContentVar()
    {
        return $this->getConfig()->get('template.var.page.content');
    }

    /**
     * @param string $contentVar
     * @return Page
     * @deprecated
     */
    public function setContentVar($contentVar)
    {
        return $this;
    }
    
    /**
     * @return Iface
     * @deprecated
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param Iface $controller
     * @return $this
     * @deprecated
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
     * Get the page theme template path
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Set the page theme template path
     *
     * @param $path
     * @return $this
     */
    public function setTemplatePath($path)
    {
        if (!is_file($path)) {
            \Tk\Log::warning('Page template not found: ' . $path);
        } else {
            $this->templatePath = $path;
        }
        return $this;
    }

    /**
     * DomTemplate magic method example
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        if (!$this->getTemplatePath()) {
            // Default template if no template path set
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
        } else {
            return \Dom\Loader::loadFile($this->getTemplatePath());
        }
    }

}