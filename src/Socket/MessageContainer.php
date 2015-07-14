<?php

namespace JackPo\MultiProcessing\Socket;

use JackPo\MultiProcessing\MessageInterface;

/**
 * Message wrapper with saving process identifier
 *
 * @package JackPo\MultiProcessing
 */
class MessageContainer
{
    /**
     * @var string process identifier
     */
    private $_pid;

    /**
     * @var \JackPo\MultiProcessing\MessageInterface
     */
    private $_message;

    /**
     * @param $pid
     * @param MessageInterface $message
     */
    public function __construct($pid, MessageInterface $message)
    {
        $this->_pid = $pid;
        $this->_message = $message;
    }

    /**
     * Get process identifier
     *
     * @return mixed
     * @see self::$_pid
     */
    public function getPid()
    {
        return $this->_pid;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage()
    {
        return $this->_message;
    }
}