<?php

namespace JackPo\MultiProcessing;

/**
 * Class Message based on stdClass
 *
 * @package JackPo\MultiProcessing
 * @see: \stdClass
 */
class Message implements MessageInterface
{
    /**
     * @var \stdClass message data object
     */
    private $_Data;

    /**
     * @param \stdClass $Data
     * @see: self:$_Data
     */
    public function __construct(\stdClass $Data)
    {
        $this->_Data = $Data;
    }

    /**
     * @param $name
     * @return mixed
     * @see: MessageInterface:__get()
     * @see: self:$_Data
     */
    public function __get($name)
    {
        return isset($this->_Data->{$name}) ? $this->_Data->{$name} : null;
    }

    /**
     * @param $name
     * @return bool
     * @see: MessageInterface:__get()
     * @see: self:$_Data
     */
    public function __isset($name)
    {
        return isset($this->_Data->{$name}) ? true : false;
    }
}