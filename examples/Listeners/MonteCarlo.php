<?php

namespace JackPo\MultiProcessingExamples\Listeners;

use JackPo\MultiProcessing\ListenerInterface;
use JackPo\MultiProcessing\MessageInterface;

/**
 * Listener implementation for calculating Pi number by Monte Carlo method
 *
 * @package JackPo\MultiProcessingExamples\Listeners
 */
class MonteCarlo implements ListenerInterface
{
    /**
     * @var int
     */
    private $_countTotal = 0;

    /**
     * @var int
     */
    private $_countIn = 0;

    /**
     * Receive results from workers
     *
     * @param MessageInterface $Message
     * @return bool
     */
    public function receive(MessageInterface $Message)
    {
        if (! isset($Message->{'count_total'}) || ! isset($Message->{'count_in'})) {
            return false;
        }

        /*
         * @todo: remove after debug
         */
        echo "Received via socket: {$Message->{'count_in'}}, Total: {$Message->{'count_total'}}\n";

        $this->_countIn += $Message->{'count_in'};
        $this->_countTotal += $Message->{'count_total'};

        return true;
    }

    /**
     * Get result
     *
     * @return bool|float
     */
    public function getAnswer()
    {
        if (! $this->_countTotal) {
            return false;
        }

        return $this->_countIn / $this->_countTotal * 4;
    }
}