<?php


namespace Tk\EventDispatcher;

/**
 * Class Event
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Event extends EventInterface implements \ArrayAccess, \IteratorAggregate
{

    /**
     * @var array
     */
    protected $paramList;

    
    /**
     * Encapsulate an event with params.
     *
     * @param array $paramList
     */
    public function __construct(array $paramList = array())
    {
        $this->paramList = $paramList;
    }

    /**
     * Get param by key.
     *
     * @param string $key
     * @return mixed
     * @throws Exception If key is not found.
     */
    public function get($key)
    {
        if ($this->exists($key)) {
            return $this->paramList[$key];
        }
        throw new Exception(sprintf('%s not found in %s', $key, get_class($this)));
    }

    /**
     * Add an param to event.
     *
     * @param string $key   Argument name.
     * @param mixed  $value Value.
     * @return Event
     */
    public function set($key, $value)
    {
        $this->paramList[$key] = $value;
        return $this;
    }

    /**
     * Delete a param from the list
     *
     * @param $key
     * @return Event
     */
    public function delete($key)
    {
        if ($this->exists($key)) {
            unset($this->paramList[$key]);
        }
        return $this;
    }

    /**
     * Getter for all params.
     *
     * @return array
     */
    public function getParamList()
    {
        return $this->paramList;
    }

    /**
     * Set args property.
     *
     * @param array $paramList Arguments.
     *
     * @return Event
     */
    public function setParamList(array $paramList = array())
    {
        $this->paramList = $paramList;
        return $this;
    }

    /**
     * Has param.
     *
     * @param string $key Key of params array.
     *
     * @return bool
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->paramList);
    }

    
    
    
    
    /**
     * ArrayAccess for param getter.
     *
     * @param string $key Array key.
     * @throws Exception If key does not exist in $this->paramList.
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * ArrayAccess for param setter.
     *
     * @param string $key   Array key to set.
     * @param mixed  $value Value.
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * ArrayAccess for unset param.
     *
     * @param string $key Array key.
     */
    public function offsetUnset($key)
    {
        $this->delete($key);
    }

    /**
     * ArrayAccess has param.
     *
     * @param string $key Array key.
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->exists($key);
    }

    /**
     * IteratorAggregate for iterating over the object like an array.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->paramList);
    }
}