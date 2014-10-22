<?php
/**
 * Circuit Breaker implementation
 *
 * PHP version 5.4
 *
 * @package  CircuitBreaker
 * @author   Elisabeth Anderson <bet@andr.io>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     https://github.com/bbc-rmp/deploy-pipeline
 */

namespace betandr\CircuitBreaker;

use betandr\CircuitBreaker\Persistence\PersistenceInterface;

/**
 * Circuit Breaker
 *
 * PHP version 5.4
 *
 * @package  CircuitBreaker
 * @author   Elisabeth Anderson <bet@andr.io>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     https://github.com/bbc-rmp/deploy-pipeline
 */
class Breaker
{
    private $_threshold = 10;
    private $_persistence;
    private $_breakerClosed = true;

    public function __construct(PersistenceInterface $persistence = null)
    {
        $this->_persistence = $persistence;
    }

    public function setThreshold($threshold) {
        $this->_threshold = $threshold;
    }

    public function getThreshold() {
        return $this->_threshold;
    }

    /**
     * Check if circuit breaker is currently closed (an antonym for isOpen())
     *
     * @return a boolean
     */
    public function isClosed()
    {
        return $this->_breakerClosed;
    }

    /**
     * Check if circuit breaker is currently open
     *
     * @return a boolean
     */
    public function isOpen()
    {
        return !$this->isClosed();
    }

    /**
     * Log a successful transaction
     */
    public function success()
    {
        if ($this->_breakerClosed === false) {
            $this->_breakerClosed = true;
        }
    }

    /**
     * Log an unsuccessful transaction
     */
    public function failure()
    {
        $key = 'failure_transactions';
        $value = $this->_persistence->get($key);
        $value++;

        if ($value >= $this->_threshold) {
            $this->_breakerClosed = false;
        }

        $this->_persistence->set($key, $value);

    }

    /**
     * To string
     *
     * @return a string value representing the object
     */
    public function to_s()
    {
        return ($this->isClosed()) ? "CircuitBreaker [CLOSED]" : "CircuitBreaker [OPEN]";
    }
}
