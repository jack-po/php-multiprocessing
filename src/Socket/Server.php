<?php

namespace JackPo\MultiProcessing\Socket;

use JackPo\MultiProcessing\ListenerInterface;

/**
 * Socket server class
 *
 * @package JackPo\MultiProcessing\Socket
 */
class Server
{
    /**
     * @var server host
     */
    protected $_host;

    /**
     * @var server port
     */
    protected $_port;

    /**
     * @var maximum accepted server clients
     */
    protected $_maxClients;

    /**
     * @var servers socket handler
     */
    protected $_serverSocket;

    /**
     * @var resource[] socket resources of accepted incoming connections
     */
    protected $_clientSockets = array();

    /**
     * @var resource[] all readable socket handlers
     * @see self:$_serverSocket
     * @see self:$_clientSockets
     */
    protected $_read = array();

    /**
     * @var JackPo\MultiProcessing\ListenerInterface[]
     */
    protected $_listeners = array();

    /**
     * @param $host
     * @param $port
     * @param $max_clients
     */
    public function __construct($host, $port, $max_clients)
    {
        $this->_host = $host;
        $this->_port = $port;
        $this->_maxClients = $max_clients;
    }

    /**
     * Start socket server
     *
     * @throws ServerException
     */
    public function start()
    {
        $this->_serverSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->_serverSocket, SOL_SOCKET, SO_REUSEADDR, 1);

        $result = true;

        $result &= @socket_bind($this->_serverSocket, $this->_host, $this->_port);
        $result &= @socket_listen($this->_serverSocket);

        if (!$result) {
            throw new ServerException(sprintf("Can't bind to address %s:%s", $this->_host, $this->_port));
        }

        $this->_read = array($this->_serverSocket);
    }

    /**
     * Socket processing iteration:
     * accepting client connections,
     * processing client messages
     */
    public function process()
    {
        $numChanged = socket_select($this->_read, $empty, $empty, 0, 10);

        if ($numChanged) {
            if (in_array($this->_serverSocket, $this->_read)) {
                if(count($this->_clientSockets) < $this->_maxClients)
                {
                    $this->_clientSockets[]= socket_accept($this->_serverSocket);
                }
            }

            foreach($this->_clientSockets as $key => $client) {
                if (in_array($client, $this->_read)) {
                    $input = socket_read($client, 1024);

                    if ($input === false) {
                        socket_shutdown($client);
                        unset($this->_clientSockets[$key]);
                    } else {
                        if ($input && false !== ($MessageContainer = unserialize($input))) {
                            foreach($this->_listeners as $Listener) {
                                $Listener->receive($MessageContainer->getMessage());
                            }
                        }

                        socket_close($client);
                        unset($this->_clientSockets[$key]);
                    }
                }
            }
        }

        $this->_read = $this->_clientSockets;
        $this->_read[] = $this->_serverSocket;
    }

    /**
     * Shutdown socket server
     */
    public function stop()
    {
        @socket_shutdown($this->_serverSocket);
    }

    /**
     * Stop server on destruction
     *
     * @uses: self::stop()
     */
    public function __destruct()
    {
        $this->stop();
    }

    /**
     * Add new listener to processing incoming messages
     *
     * @param ListenerInterface $Listener
     * @see: self::$_listeners
     * @see: self::process()
     */
    public function addListener(ListenerInterface $Listener)
    {
        $this->_listeners[] = $Listener;
    }
}