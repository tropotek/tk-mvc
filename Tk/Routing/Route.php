<?php
namespace Tk\Routing;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Route extends \Symfony\Component\Routing\Route
{

    /**
     * Route constructor.
     *
     * @param string $path
     * @param array|string $defaults  (if sting then use Tk lib compatability untill we change all route calls)
     * @param array $requirements
     * @param array $options
     * @param string|null $host
     * @param array $schemes
     * @param array $methods
     * @param string|null $condition
     */
    public function __construct(string $path, $defaults = [], array $requirements = [], array $options = [], ?string $host = '', $schemes = [], $methods = [], ?string $condition = '')
    {
        if (!is_array($defaults)) {     // Use this to fix \Tk\Routing\Route params compatability
            $defaults = array_merge(array('_controller' => $defaults), $requirements);
            $requirements = [];
            $methods = count($options) ? $options : array('GET','POST','HEAD');
            // $methods = $options;
            $options = [];
        }
        parent::__construct($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
    }

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
//    public function __construct($path, $controller, $attributes = array(), $validMethods = array('GET','POST','HEAD'))
//    {
//        $defaults = array_merge(array('_controller' => $controller), $attributes);
//        parent::__construct($path, $defaults, [], [], '', [], $validMethods);
//    }


    /**
     * @param string $path
     * @param array|string $defaults  (if sting then use Tk lib compatability untill we change all route calls)
     * @param array $requirements
     * @param array $options
     * @param string|null $host
     * @param array $schemes
     * @param array $methods
     * @param string|null $condition
     * @return \Symfony\Component\Routing\Route
     */
    public static function create(string $path, $defaults = [], array $requirements = [], array $options = [], ?string $host = '', $schemes = [], $methods = [], ?string $condition = '')
    {
        return new self($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
    }

}
