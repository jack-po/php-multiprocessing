<?php

namespace JackPo\MultiProcessing;

/**
 * Class ListenerInterface
 *
 * @package JackPo\MultiProcessing
 */
interface ListenerInterface
{
    /**
     * Process message from worker
     *
     * @param MessageInterface $Message
     * @return mixed
     * @see: WorkerPrototype
     */
    public function receive(MessageInterface $Message);
}