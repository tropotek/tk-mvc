<?php
namespace Tk\Routing;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 * @deprecated Use: \Symfony\Component\Routing\Route() This will be removed once the symfony Routing lib is used by all projects
 */
class Route extends \Symfony\Component\Routing\Route
{

    /**
     * Left for compatability
     * will be deleted once all project routes.php files are updated
     *
     * @param string $path
     * @param object|callable|string $controller A string, callable or object
     * @param array $attributes
     * @param array $validMethods
     * @deprecated Use: new \Symfony\Component\Routing\Route()
     */
    public function __construct($path, $controller, $attributes = array(), $validMethods = array('GET','POST','HEAD'))
    {
        $defaults = array_merge(array('_controller' => $controller), $attributes);
        parent::__construct($path, $defaults, [], [], '', [], $validMethods);
    }
}
