<?php

namespace JackPo\MultiProcessing;

/**
 * Class MessageInterface
 * @package JackPo\MultiProcessing
 */
interface MessageInterface
{

    /**
     * @param \stdClass $Data
     */
    public function __construct(\stdClass $Data);

    /**
     * Get param in message
     *
     * @param $name
     * @return mixed
     */
    public function __get($name);

    /**
     * Check existing param in message
     *
     * @param $name
     * @return mixed
     */
    public function __isset($name);
}