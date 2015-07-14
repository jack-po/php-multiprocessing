<?php

namespace JackPo\MultiProcessing;

use JackPo\MultiProcessing\Socket;

/**
 * Class WorkerPrototype
 * @package JackPo\MultiProcessing
 */
abstract class WorkerPrototype
{
    /**
     * @var Socket\Client socket client handler for output messages
     */
    private $_Socket;

    /**
     * Set socket client handler
     * @param Socket\Client $socket
     * @return bool
     */
    public function setSocket(Socket\Client $socket)
    {
        if (!isset($this->_Socket)) {
            $this->_Socket = $socket;

            return true;
        }

        return false;
    }

    /**
     * Get socket client handler
     *
     * @return Socket\Client
     */
    public function getSocket()
    {
        return $this->_Socket;
    }

    /**
     * Execute worker payload
     *
     * @return mixed
     */
    abstract public function execute();
}