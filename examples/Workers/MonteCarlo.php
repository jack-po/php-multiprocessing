<?php

namespace JackPo\MultiProcessingExamples\Workers;

use JackPo\MultiProcessing\Socket;
use JackPo\MultiProcessing\WorkerPrototype;
use JackPo\MultiProcessing\Message;

/**
 * Worker implementation for calculating Pi number by Monte Carlo method
 *
 * @package JackPo\MultiProcessingExamples\Workers
 */
class MonteCarlo extends WorkerPrototype
{
    /**
     * @var int current iteration
     */
    private $_count;

    /**
     * @var int number of iterations
     */
    private $_precision = 100000;

    /**
     * @param $count
     * @see self::$_count
     */
    public function __construct($count)
    {
        $this->_count = $count;
    }

    /**
     * Calculate Pi number with set precision and send result to Socket
     */
    public function execute()
    {
        $countInner = 0;
        $countTotal = 0;

        if ($this->_count > 0) {
            while ($countTotal < $this->_count) {
                $x = rand(0, $this->_precision) / $this->_precision;
                $y = rand(0, $this->_precision) / $this->_precision;

                if ($y * $y <= $this->circle($x)) {
                    $countInner++;
                }
                $countTotal++;
            }
        }

        $data = new \stdClass();
        $data->{'count_total'} = $countTotal;
        $data->{'count_in'} = $countInner;

        $Message = new Message($data);
        $this->getSocket()->send($Message);
    }

    /**
     * @param $x
     * @return float
     */
    protected function circle($x)
    {
        return 1.0 - $x  * $x;
    }
}