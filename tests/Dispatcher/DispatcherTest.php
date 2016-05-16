<?php
namespace tests\Dispatcher;

use \Tk\Dispatcher\Dispatcher;
use \Tk\Dispatcher\Event;

/**
 * Class DispatcherTest
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher = null;
    

    protected function setUp()
    {
        $this->dispatcher = new Dispatcher();
    }
    
    
    public function testDispatcher()
    {
        
    }
    
    
    
    
}
