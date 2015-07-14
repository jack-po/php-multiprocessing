<?php

namespace JackPo\MultiProcessing;
use JackPo\MultiProcessing\Socket\Client;

/**
 * Class Manager
 * @package JackPo\MultiProcessing
 */
class Manager
{
    /**
     * @var int process id
     */
    private static $_pid = 0;

    /**
     * Config
     * 'autoload_path'
     * 'php_cli'       path to php or shorthand command
     * 'channel_host'  socket channel host
     * 'channel_port'  socket channel port
     * 'max_clients'   maximum clients for socket channel
     *
     * @var array
     */
    protected $_config = array();

    /**
     * @var WorkerPrototype[] added workers handlers
     */
    protected $_workers = array();

    /**
     * @var array current running socket processed
     */
    protected $_processes = array();

    /**
     * @var Socket\Server socket server handler
     */
    protected $_Socket;

    /**
     * @param array $config
     * @see self::$_config for config params
     */
    public function __construct(array $config)
    {
        $defaults = array(
            'autoload_path' => __DIR__.'/../vendor/autoload.php',
            'php_cli'       => 'php',
            'channel_host'  => '127.0.0.1',
            'channel_port'  => 8395,
            'max_clients'   => 20
        );
        $this->_config = array_merge($defaults, $config);

        $this->_Socket = new Socket\Server(
            $this->_config['channel_host'],
            $this->_config['channel_port'],
            $this->_config['max_clients']
        );
    }

    /**
     * @param WorkerPrototype $Worker
     * @param int $num how many worker processes to start
     * @return bool
     */
    public function addWorker(WorkerPrototype $Worker, $num = 1)
    {
        if ($num < 1) {
            return false;
        }

        for ($i = 0; $i < $num; $i++) {
            $this->_workers[] = $Worker;
        }

        $Socket = new Socket\Client($this->_config['channel_host'], $this->_config['channel_port']);
        $Worker->setSocket($Socket);

        return true;
    }

    /**
     * @param ListenerInterface $Listener
     */
    public function addListener(ListenerInterface $Listener)
    {
        $this->_Socket->addListener($Listener);
    }

    /**
     * Execute workers in separate processes
     */
    public function run()
    {
        $this->_Socket->start();

        foreach ($this->_workers as $Worker) {
            $exec_string = '<?php require_once \'%s\'; unserialize(\'%s\')->execute(); ?>';
            $exec_string = sprintf($exec_string, $this->_config['autoload_path'], serialize($Worker));

            $pid = self::getPid();

            $descriptorspec = array(
                0 => array("pipe", "r"),
            );

            $env = array(
                'WORKER_PID' => $pid
            );

            $process = proc_open($this->_config['php_cli'], $descriptorspec, $pipes, $cwd = null, $env);

            if (is_resource($process)) {
                fwrite($pipes[0], $exec_string);
                fclose($pipes[0]);
                $this->_processes[] = $process;
            }
        }

        while (count($this->_processes)) {
            $this->_Socket->process();

            foreach ($this->_processes as $key => $process) {
                if (false === ($status = proc_get_status($process)) || false == $status['running']) {
                    proc_close($process);
                    unset($this->_processes[$key]);
                }
            }
        }

        $this->_Socket->stop();
    }

    /**
     * Get next process id (incremented)
     *
     * @return int
     */
    private function getPid()
    {
        return self::$_pid++;
    }
}