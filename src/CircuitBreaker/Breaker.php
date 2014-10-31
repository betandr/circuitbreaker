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
    protected $name;
    protected $threshold = 5;
    protected $timeout = 60;
    protected $persistence;
    protected $breakerClosed = true;
    protected $willRetry = true;
    protected $failureKey;
    protected $lastFailureTimeKey;

    /**
     * Construct a new Breaker instance.
     *
     * @param $name a string value to identify this breaker
     *
     * @return a boolean
     */
    public function __construct($name, $persistence, $params = null)
    {
        $this->persistence = $persistence;
        $this->name = $name;

        $this->failureKey = $name.'_failure_count_'.Breaker::generateSalt();
        $this->lastFailureTimeKey = $name.'_last_failure_time_'.Breaker::generateSalt();

        if (isset($params['threshold']) && is_int($params['threshold'])) {
            $this->threshold = $params['threshold'];
        }

        if (isset($params['timeout']) && is_int($params['timeout'])) {
            $this->timeout = $params['timeout'];
        }

        if (isset($params['retry']) && is_bool($params['retry'])) {
            $this->willRetry = $params['retry'];
        }
    }

    public function setThreshold($threshold) { $this->threshold = $threshold; }
    public function getThreshold() { return $this->threshold; }

    public function setName($name) { $this->name = $name; }
    public function getName() { return $this->name; }

    public function setTimeout($timeout) { $this->timeout = $timeout; }
    public function getTimeout() { return $this->timeout; }

    public function setWillRetryAfterTimeout($willRetry) { if ($willRetry) { $this->willRetry = true; } }
    public function getWillRetryAfterTimeout() { return $this->willRetry; }

    public function getLastFailureTime() { return $this->persistence->get($this->lastFailureTimeKey); }

    public function getNumFailures()
    {
        $numFails = $this->persistence->get($this->failureKey);
        return ($numFails == NULL) ? 0 : $numFails;
    }

    /**
     * Check if circuit breaker is currently closed. If 'will retry' is set to true,
     * any checks for isClosed can return a true if the timeout has expired since the
     * last failure. This is so systems are given the chance to recover and the
     * circuit breaker can re-close itself if upstream services begin working again.
     * In this 'half-open' mode, the client can try another request, if it fails then
     * the timeout will be reset. If successful, the client can register a success
     * which will close the breaker properly.
     *
     * @return a boolean
     */
    public function isClosed()
    {
        $lastFailureTime = $this->persistence->get($this->lastFailureTimeKey);

        if (!$this->breakerClosed && $this->willRetry && isset($lastFailureTime)) {
            // If threshold reached and we want to retry, return true
            // Don't reset breaker though, let a registered success do that.
            $now = time();
            $lastFailurePlusTimeout = $lastFailureTime + $this->timeout;

            if ($now >= $lastFailurePlusTimeout) {
                return true;
            }
        }

        return $this->breakerClosed;
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
        if ($this->breakerClosed === false) {
            $this->breakerClosed = true;
        }

        $numFails = $this->persistence->get($this->failureKey);

        if ($this->threshold > 0) { $numFails--; }

        $this->persistence->set($this->failureKey, $numFails);
    }

    /**
     * Log an unsuccessful transaction
     */
    public function failure()
    {
        $numFails = $this->persistence->get($this->failureKey);

        if ($numFails === NULL) { $numFails = 0; }

        if ($numFails < $this->threshold) { $numFails++; }

        if ($numFails >= $this->threshold) {
            $this->breakerClosed = false;
            $this->persistence->set($this->lastFailureTimeKey, time());
        }

        $this->persistence->set($this->failureKey, $numFails);

    }

    /**
     * Immediately open the circuit breaker
     */
    public function open()
    {
        $this->breakerClosed = false;
    }

    /**
     * Immediately open the circuit breaker
     */
    public function close()
    {
        $this->breakerClosed = true;
    }

    /**
     * Close the circuit breaker and reset the failure count
     */
    public function reset()
    {
        $this->close();
        $this->persistence->set($this->failureKey, 0);
    }

    /**
     * To string
     *
     * @return a string value representing the object
     */
    public function __toString()
    {
        return ($this->isClosed()) ? "CircuitBreaker [CLOSED]" : "CircuitBreaker [OPEN]";
    }

    private static function generateSalt()
    {
        return str_shuffle(MD5(microtime()));
    }
}
