<?php

namespace JackPo\MultiProcessing\Socket;

use JackPo\MultiProcessing\MessageInterface;

/**
 * Socket client class
 *
 * @package JackPo\MultiProcessing\Socket
 */
class Client
{
    /**
     * @var string process identifier
     */
    protected $_pid;

    /**
     * @var string socket address with format tcp://127.0.0.1:1234
     */
    protected $_address;

    /**
     * @param $host host of socket address
     * @param $port port of socket address
     * @param $pid
     * @see self::$_address
     * @see self::$_pid
     */
    public function __construct($host, $port, $pid = null)
    {
        $this->_pid = $pid;
        $this->_address = sprintf('tcp://%s:%s', $host, $port);
    }

    /**
     * Setup new process identifier after unserialization
     *
     * @throws ClientException
     * @see self::$_pid
     */
    public function __wakeup()
    {
        $pid = getenv('WORKER_PID');

        if (false === $pid) {
            throw new ClientException("Worker pid doesn't exists");
        }

        $this->_pid = $pid;
    }

    /**
     * Send message to the server
     *
     * @param MessageInterface $Message
     * @throws ClientException
     */
    public function send(MessageInterface $Message)
    {
        $socket = @stream_socket_client($this->_address, $errno, $errstr, 15, STREAM_CLIENT_ASYNC_CONNECT|STREAM_CLIENT_CONNECT);

        if (!$socket) {
            throw new ClientException("Failed to connect socket. Error: $errstr($errno)");
        }

        $Container = new MessageContainer($this->_pid, $Message);

        fwrite($socket, serialize($Container));
        fclose($socket);
    }
}