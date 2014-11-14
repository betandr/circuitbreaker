<?php
/**
* Mock Logger implementation
*
* PHP version 5.4
*
* @package  CircuitBreaker
* @author   Elisabeth Anderson <bet@andr.io>
* @license  http://www.php.net/license/3_01.txt  PHP License 3.01
* @link     https://github.com/bbc-rmp/deploy-pipeline
*/

namespace betandr\CircuitBreaker;

use Psr\Log\LoggerInterface;
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
class MockLogger implements LoggerInterface
{
	protected $count = 0;

	public function numberOfResponses()
	{
		return $this->count;
	}

	public function info($message, array $context = array())
	{
		$this->count++;
	}

	public function emergency($message, array $context = array())
	{
	  $this->count++;
	}

    public function alert($message, array $context = array())
	{
	  $this->count++;
	}

    public function critical($message, array $context = array())
	{
	  $this->count++;
	}

    public function error($message, array $context = array())
	{
	  $this->count++;
	}

    public function warning($message, array $context = array())
	{
	  $this->count++;
	}

    public function notice($message, array $context = array())
	{
	  $this->count++;
	}

    public function debug($message, array $context = array())
	{
	  $this->count++;
	}

    public function log($level, $message, array $context = array())
	{
	  $this->count++;
	}
}
