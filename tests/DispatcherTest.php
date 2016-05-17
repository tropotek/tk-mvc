<?php
namespace tests;

use Tk\Dispatcher\Dispatcher;
use Tk\Dispatcher\Event;


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

    /**
     * @var Event
     */
    protected $event = null;


    public function __construct()
    {
        parent::__construct('Dispatcher Test');
    }

    public function setUp()
    {
        $this->dispatcher = new Dispatcher();

    }

    public function tearDown()
    {

    }

    
    
    public function testDispatcher()
    {
        $this->assertInstanceOf('Tk\Dispatcher\Dispatcher', $this->dispatcher);
    }
    
    
    public function testListener()
    {
        $this->event = new \Tk\Dispatcher\Event(array('param1' => 'value1'));
        $evName = 'event.kernel.request';
        // Add a listener
        $this->dispatcher->addListener($evName, function(\Tk\Dispatcher\Event $event) {
            $str = 'Listener 1';
            $event->set('status', $str);
            $this->assertEquals('value1', $event->get('param1'));
        }, 2);
        $this->dispatcher->addListener($evName, function(\Tk\Dispatcher\Event $event) {
            $str = 'Listener 2';
            $event->set('status', $str);
            $this->assertEquals('value1', $event->get('param1'));
        }, 1);
        $this->dispatcher->addListener($evName, function(\Tk\Dispatcher\Event $event) {
            $str = 'Listener 3';
            $event->set('status', $str);
            $this->assertEquals('value1', $event->get('param1'));
        }, 3);
        
        $this->dispatcher->dispatch($evName,  $this->event);
        
        $this->assertEquals('Listener 2', $this->event->get('status'));

        $listener = new DummyListener();
        $this->dispatcher->addSubscriber($listener);
        
        $this->dispatcher->dispatch($evName, $this->event);
        $this->assertEquals('Listener Dummy', $this->event->get('status'));
        
        // Test delete and exists functions
        $this->assertTrue($this->dispatcher->hasListeners($evName));
        $cnt = count($this->dispatcher->getListeners($evName));
        $this->dispatcher->removeSubscriber($listener);
        $this->assertEquals($cnt-1, count($this->dispatcher->getListeners($evName)));
        
        
    }
    
    public function testSubscriber()
    {
        $this->event = new \Tk\Dispatcher\Event(array('param2' => 'value2'));
        $this->dispatcher->addSubscriber(new DummyListener());

        $this->dispatcher->dispatch('event.kernel.request', $this->event);
        $this->assertEquals('Listener Dummy', $this->event->get('status'));
        
        $this->dispatcher->dispatch('event.subscriber', $this->event);
        $this->assertEquals('Listener Dummy1', $this->event->get('dummyListener'));
    }
}

class DummyListener implements \Tk\Dispatcher\SubscriberInterface
{
    public function doOne(Event $event)
    {
        $str = 'Listener Dummy';
        $event->set('status', $str);
    }

    public function doTwo(Event $event)
    {
        $str = 'Listener Dummy1';
        $event->set('dummyListener', $str);
    }
    
    /**
     * Returns an array of event names this subscriber wants to listen to.
     * NOTE: The higher priority number is run first.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'event.kernel.request' => array('doOne', -99),
            'event.subscriber' => 'doTwo'
        );
    }
    
}