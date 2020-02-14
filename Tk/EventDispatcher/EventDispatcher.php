<?php
namespace Tk\EventDispatcher;

use Symfony\Component\EventDispatcher\Debug\WrappedListener;
use Symfony\Component\EventDispatcher\LegacyEventProxy;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\EventDispatcher\Event as ContractsEvent;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class EventDispatcher extends \Symfony\Component\EventDispatcher\EventDispatcher
{
    use \Psr\Log\LoggerAwareTrait;

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





    /**
     * {@inheritdoc}
     */
//    public function dispatch($event, $eventName = null)
//    {
//        if ($this->logger)
//            if (is_object($event))
//                $this->logger->debug('Dispatch: [' . get_class($event) . ']');
//            else
//                $this->logger->debug('Dispatch: [' . get_class($eventName) . ']');
//
//        $e = parent::dispatch($event, $eventName);
//        return $e;
//    }


    /**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[] $listeners The event listeners
     * @param string     $eventName The name of the event to dispatch
     * @param object     $event     The event object to pass to the event handlers/listeners
     */
    protected function callListeners(iterable $listeners, string $eventName, $event)
    {
        if ($event instanceof \Symfony\Component\EventDispatcher\Event) {
            $this->doDispatch($listeners, $eventName, $event);
            return;
        }
        $stoppable = $event instanceof ContractsEvent || $event instanceof StoppableEventInterface;
        foreach ($listeners as $listener) {
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }
            if ($this->logger)
                $this->logger->debug(' - [' . $eventName . ']  ' . get_class($listener[0]) . ']');
            // @deprecated: the ternary operator is part of a BC layer and should be removed in 5.0
            $listener($listener instanceof WrappedListener ? new LegacyEventProxy($event) : $event, $eventName, $this);
        }
    }

    /**
     * @deprecated since Symfony 4.3, use callListeners() instead
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }
            if ($this->logger)
                $this->logger->debug(' - [' . $eventName . ']  ' . get_class($listener[0]) . ']');
            $listener($event, $eventName, $this);
        }
    }



}