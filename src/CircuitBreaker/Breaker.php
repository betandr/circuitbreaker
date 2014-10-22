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
    /**
     * Check if circuit breaker is currently closed (an antonym for isOpen())
     *
     * @return a boolean
     */
    public function isClosed()
    {
        return !$this->isOpen();
    }

    /**
     * Check if circuit breaker is currently open
     *
     * @return a boolean
     */
    public function isOpen()
    {
        return true;
    }

    /**
     * Log a successful transaction
     */
    public function success()
    {

    }

    /**
     * Log an unsuccessful transaction
     */
    public function failure()
    {

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
