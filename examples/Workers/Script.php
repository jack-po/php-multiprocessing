<?php

namespace JackPo\MultiProcessingExamples\Workers;

use JackPo\MultiProcessing\WorkerPrototype;

/**
 * Worker implementation for custom php script execution
 *
 * @package JackPo\MultiProcessingExamples\Workers
 */
class Script extends WorkerPrototype
{
    /**
     * @var path to php script
     */
    private $_path;

    /**
     * @param $path
     * @see self::$_path
     */
    public function __construct($path)
    {
        $this->_path = $path;
    }

    /**
     * Execute script
     *
     * @see: self::$_path
     */
    public function execute()
    {
        include $this->_path;
    }
}