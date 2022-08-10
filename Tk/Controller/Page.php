<?php
namespace Tk\Controller;

use Tk\ConfigTrait;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Page extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{
    use ConfigTrait;

    /**
     * @var bool
     */
    protected $templateLoaded = false;

    /**
     * @var \Tk\Controller\Iface
     * @deprecated Why ???? Maybe getController() no longer gets called anywhere
     */
    protected $controller = null;

    /**
     * @var string
     */
    protected $templatePath = '';


    /**
     * @param string $templatePath
     */
    public function __construct($templatePath = '')
    {
        $this->setTemplatePath($templatePath);
    }

    /**
     * Init the page ????
     * @deprecated Why ???? Maybe getController() no longer gets called anywhere
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
     * @param string $path
     * @return $this
     */
    protected function setTemplatePath($path)
    {
        if ($this->isTemplateLoaded()) {
            \Tk\Log::warning('Template already loaded, path ignored: ' . $path);
            return $this;
        }
        if (!is_file($path)) {
            \Tk\Log::warning('Page template not found: ' . $path);
        } else {
            $this->templatePath = $path;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isTemplateLoaded()
    {
        return $this->templateLoaded;
    }

    /**
     * DomTemplate magic method example
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $this->templateLoaded = true;
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