<?php
namespace Tk\Event;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Dispatcher
 * 
 * Dispatcher Pattern adapted from the article http://www.chrisbrand.co.za/2013/06/22/design-pattern-event-dispatcher/
 * Also with influence from the Symfony EventDispatcher objects.
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class Dispatcher
{
    
    /**
     * @var array
     */
    protected $listeners = array();
    
    /**
     * @var array
     */
    protected $sorted = array();

    /**
     * @var LoggerInterface|null
     */
    protected $logger = null;

    
    
    /**
     * Constructor.
     *
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
    
    /**
     * @param string $eventName
     * @param callable $callback
     */
    public function listen($eventName, $callback)
    {
        $this->listeners[$eventName][] = $callback;
    }

    /**
     * @param string $eventName
     * @param Event $event
     * @return Event
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }
        if (!isset($this->listeners[$eventName])) {
            return $event;
        }
        $this->doDispatch($this->getListeners($eventName), $eventName, $event);
        return $event;
    }

    /**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[]        $listeners The event listeners.
     * @param string            $eventName The name of the event to dispatch.
     * @param Event             $event     The event object to pass to the event handlers/listeners.
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            if (is_array($listener) && $this->logger) {
                $this->logger->debug('Dispatch: ' . $eventName . ' - ' . get_class($listener[0]) . '::' . $listener[1] . '(' . get_class($event) . ')');
            }
            call_user_func($listener, $event, $eventName, $this);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     * @param int      $priority  The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);
    }

    /**
     * Gets the listeners of a specific eventName or all listeners sorted by descending priority.
     *
     * @param string $eventName The name of the event
     * @return array The event listeners for the specified event, or all event listeners by event name
     */
    public function getListeners($eventName = null)
    {
        if (null !== $eventName) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
            return $this->sorted[$eventName];
        }
        foreach ($this->listeners as $eventName => $eventListeners) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
        }
        return array_filter($this->sorted);
    }

    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string $eventName The name of the event
     * @return bool true if the specified event has any listeners, false otherwise
     */
    public function hasListeners($eventName = null)
    {
        return (bool)count($this->getListeners($eventName));
    }

    /**
     * Removes an event listener from the specified events.
     *
     * @param string $eventName The event to remove a listener from
     * @param callable $listener The listener to remove
     * @return $this
     */
    public function removeListener($eventName, $listener)
    {
        if (!isset($this->listeners[$eventName])) {
            return $this;
        }
        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners, true))) {
                unset($this->listeners[$eventName][$priority][$key], $this->sorted[$eventName]);
            }
        }
        return $this;
    }
    
    /**
     * Adds an event subscriber.
     *
     * The subscriber is asked for all the events he is
     * interested in and added as a listener for these events.
     *
     * @param EventSubscriberInterface $subscriber The subscriber.
     * @return $this
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) { // array('eventName' => 'methodName')
                $this->addListener($eventName, array($subscriber, $params));
            } elseif (is_string($params[0])) {  // array('eventName' => array('methodName', $priority))
                $this->addListener($eventName, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);
            } else {
                foreach ($params as $listener) {    // array('eventName' => array(array('methodName1', $priority), array('methodName2'))
                    $this->addListener($eventName, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }
        return $this;
    }

    /**
     * Removes an event subscriber.
     *
     * @param EventSubscriberInterface $subscriber The subscriber
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($eventName, array($subscriber, $listener[0]));
                }
            } else {
                $this->removeListener($eventName, array($subscriber, is_string($params) ? $params : $params[0]));
            }
        }
    }

    /**
     * Sorts the internal list of listeners for the given event by priority.
     *
     * @param string $eventName The name of the event.
     */
    private function sortListeners($eventName)
    {
        $this->sorted[$eventName] = array();

        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
    }


    /**
     * Search the requested path for Event definition files
     * containing the Event constants.
     * This way we can list and document them automatically
     *
     * @param $searchPath
     * @param string $fileReg
     * @return array
     * @throws \Tk\Exception
     */
    public function getAvailableEvents($searchPath, $fileReg = '/.+Events.php$/')
    {
        if (!is_dir($searchPath)) {
            throw new \Tk\Exception('Cannot open file path: ' . $searchPath);
        }
        $directory = new \RecursiveDirectoryIterator($searchPath);
        $flattened = new \RecursiveIteratorIterator($directory);
        $files = new \RegexIterator($flattened, $fileReg);
        $eventData = array();
        foreach ($files as $file) {
            $arr = $this->getClassEvents(file_get_contents($file->getPathname()));
            $eventData = array_merge($eventData, $arr);
        }
        return $eventData;
    }


    /**
     * Parse a php file for all available event codes
     * so we can document them dynamically
     *
     * @param $phpcode
     * @return array
     */
    private function getClassEvents($phpcode)
    {
        $classes = array();

        $namespace = 0;
        $tokens = token_get_all($phpcode);
        $count = count($tokens);
        $dlm = false;

        $const = false;
        $name = '';
        $doc = '';
        $event = '';
        $className = '';

        for ($i = 2; $i < $count; $i++) {
            if ((isset($tokens[$i - 2][1]) && ($tokens[$i - 2][1] == "phpnamespace" || $tokens[$i - 2][1] == "namespace")) ||
                ($dlm && $tokens[$i - 1][0] == T_NS_SEPARATOR && $tokens[$i][0] == T_STRING)
            ) {
                if (!$dlm) $namespace = 0;
                if (isset($tokens[$i][1])) {
                    $namespace = $namespace ? $namespace . "\\" . $tokens[$i][1] : $tokens[$i][1];
                    $dlm = true;
                }
            } elseif ($dlm && ($tokens[$i][0] != T_NS_SEPARATOR) && ($tokens[$i][0] != T_STRING)) {
                $dlm = false;
            }
            if (($tokens[$i - 2][0] == T_CLASS || (isset($tokens[$i - 2][1]) && $tokens[$i - 2][1] == "phpclass"))
                && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING
            ) {
                $class_name = $tokens[$i][1];
                $className = $namespace . '\\' . $class_name;
                $classes[$className]['class'] = $className;
                $classes[$className]['const'] = array();
            }

            if (is_array($tokens[$i])) {
                if ($tokens[$i][0] != T_WHITESPACE) {
                    if ($tokens[$i][0] == T_CONST && $tokens[$i][1] == 'const') {
                        $const = true;
                        $name = '';
                        $event = '';
                        $doc = '';
                        if (isset($tokens[$i - 2][1])) {
                            $doc = $tokens[$i - 2][1];
                            // Parse out comment (NOTE: The doc is the first part wo we could look for the first @ and call that the end of the doc)
                            $doc = str_replace(array('@var string', '/*', '*/', '*'), '', $doc);
                            preg_match('/(.?)(@event .+)/i', $doc, $reg);
                            if (isset($reg[2])) {
                                $event = trim(trim(str_replace('@event', '', $reg[2])), '\\');
                                $doc = trim(str_replace($reg[2], '', $doc));
                                $doc = preg_replace('/\s+/', ' ', $doc);
                                $doc = preg_replace('/([\.:]) /', "$1\n", $doc);
                                // remove duplicate whitespace
                                $doc = preg_replace("/\s\s([\s]+)?/", " ", $doc);
                            }
                        }
                    } else if ($tokens[$i][0] == T_STRING && $const) {
                        $const = false;
                        $name = $tokens[$i][1];
                    } else if ($tokens[$i][0] == T_CONSTANT_ENCAPSED_STRING && $name && isset($classes[$className])) {
                        $classes[$className]['const'][$name] = array('value' => str_replace(array("'", '"') , '' , $tokens[$i][1]), 'doc' => $doc, 'event' => $event);
                        $doc = '';
                        $name = '';
                        $event = '';
                    }
                }
            } else if ($tokens[$i] != '=') {
                $const = false;
                $doc = '';
                $name = '';
                $event = '';
            }

        }
        return $classes;
    }

        
}