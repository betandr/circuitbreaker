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
    private $_name;
    private $_threshold = 25;
    private $_timeout = 6000;
    private $_persistence;
    private $_breakerClosed = true;
    private $_willRetry = true;

    protected function __construct($name, $persistence, $params = null)
    {
        $this->_persistence = $persistence;
        $this->_name = $name;

        if (isset($params['threshold']) && is_int($params['threshold'])) {
            $this->_threshold = $params['threshold'];
        }

        if (isset($params['timeout']) && is_int($params['timeout'])) {
            $this->_timeout = $params['timeout'];
        }

        if (isset($params['will_retry']) && is_int($params['will_retry'])) {
            $this->_retry = $params['will_retry'];
        }
    }

    /**
     * Construct a new Breaker instance.
     *
     * @param $name a string value to identify this breaker
     *
     * @return a boolean
     */
    public static function build($name, PersistenceInterface $persistence = null, $params = null)
    {
        return new Breaker($name, $persistence, $params);
    }

    public function setThreshold($threshold) { $this->_threshold = $threshold; }
    public function getThreshold() { return $this->_threshold; }

    public function setName($name) { $this->_name = $name; }
    public function getName() { return $this->_name; }

    public function setTimeout($timeout) { $this->_timeout = $timeout; }
    public function getTimeout() { return $this->_timeout; }

    public function setWillRetryAfterTimeout($willRetry) { if ($willRetry) { $this->_willRetry = true; } }
    public function getWillRetryAfterTimeout() { return $this->_willRetry; }

    /**
     * Check if circuit breaker is currently closed
     *
     * @return a boolean
     */
    public function isClosed()
    {
        return $this->_breakerClosed;
    }

    /**
     * Check if circuit breaker is currently open. This is an antonym for isClosed()
     * and always has the opposite boolean value.
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
        if ($value === NULL) { $value = 0; }
        $value++;

        if ($value >= $this->_threshold) {
            $this->_breakerClosed = false;
        }

        $this->_persistence->set($key, $value);
    }

    /**
     * Immediately open the circuit breaker
     */
    public function open()
    {
        $this->_breakerClosed = false;
    }

    /**
     * Immediately open the circuit breaker
     */
    public function close()
    {
        $this->_breakerClosed = true;
    }

    /**
     * Close the circuit breaker and reset the failure count
     */
    public function reset()
    {
        $this->close();
        $key = 'failure_transactions';
        $this->_persistence->set($key, 0);
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
