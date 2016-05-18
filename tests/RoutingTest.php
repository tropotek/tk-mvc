<?php
namespace tests;



/**
 * Class DispatcherTest
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class RoutingTest extends \PHPUnit_Framework_TestCase
{
    


    public function __construct()
    {
        parent::__construct('Routing Test');
    }

    public function setUp()
    {
        

    }

    public function tearDown()
    {

    }



    public function testRoute()
    {
        $route = new \Tk\Routing\Route('/index.html', '\App\Controller\Index::doDefault', array());
        $this->assertInstanceOf('\Tk\Routing\Route', $route);
        
        
    }
    
    
    public function testRoutCollection()
    {
        $collection = new \Tk\Routing\RouteCollection();
        $this->assertInstanceOf('\Tk\Routing\RouteCollection', $collection);
        
        
    }
    
    
}

