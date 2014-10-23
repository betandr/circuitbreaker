<?php

namespace betandr\CircuitBreaker\Tests;

use betandr\CircuitBreaker\Breaker;
use betandr\CircuitBreaker\Persistence\ArrayPersistence;

class BreakerTest extends \PHPUnit_Framework_TestCase
{

    public function testIsClosedOnConstruction()
    {
        $breaker = Breaker::build('testBreaker', new ArrayPersistence);
        $this->assertTrue($breaker->isClosed(), 'Breaker should be closed when constructed');
    }

    public function testBreakerOpensWhenThresholdReached()
    {
        $breaker = Breaker::build('testBreaker', new ArrayPersistence);
        $breaker->setThreshold(1);

        $breaker->failure();

        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when threshold reached');
    }

    public function testBreakerDoesntOpenUntilThresholdReached()
    {
        $breaker = Breaker::build('testBreaker', new ArrayPersistence);
        $breaker->setThreshold(2);

        $breaker->failure();

        $this->assertTrue($breaker->isClosed(), 'Breaker should be closed before threshold reached');
    }

    public function testOpenBreakerClosesWhenSuccessRegistered()
    {
        $breaker = Breaker::build('testBreaker', new ArrayPersistence);
        $breaker->setThreshold(1);

        $breaker->failure();
        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when threshold reached');

        $breaker->success();
        $this->assertTrue($breaker->isClosed(), 'Breaker should re-close when success registered');
    }

    public function testOpeningBreaker()
    {
        $breaker = Breaker::build('testBreaker', new ArrayPersistence);

        $breaker->open();
        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when explicitly opened');
    }

    public function testReClosingBreaker()
    {
        $breaker = Breaker::build('testBreaker', new ArrayPersistence);

        $breaker->open();
        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when explicitly opened');

        $breaker->close();
        $this->assertTrue($breaker->isClosed(), 'Breaker should be closed when explicitly closed');
    }

    public function testResettingBreaker()
    {
        $breaker = Breaker::build('testBreaker', new ArrayPersistence);

        $breaker->open();
        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when explicitly opened');

        $breaker->reset();
        $this->assertTrue($breaker->isClosed(), 'Breaker should be closed when reset');
    }

    public function testDefaultThresholdValue()
    {
        $breaker = Breaker::build('testBreaker', new ArrayPersistence);

        $expectedThreshold = 25;
        $this->assertEquals($expectedThreshold, $breaker->getThreshold(), 'Threshold should be '.$expectedThreshold.' by default');
    }

    public function testDefaultTimeoutValue()
    {
        $breaker = Breaker::build('testBreaker', new ArrayPersistence);

        $expectedTimeout = 6000;
        $this->assertEquals($expectedTimeout, $breaker->getTimeout(), 'Timeout should be '.$expectedTimeout.' by default');
    }

    public function testSettingThresholdViaParams()
    {
        $threshold = 99;
        $breaker = Breaker::build('testBreaker', new ArrayPersistence, array('threshold' => $threshold));

        $this->assertEquals($threshold, $breaker->getThreshold(), 'Threshold should be set in params');
    }

    public function testSettingTimeoutViaParams()
    {
        $timeout = 999;
        $breaker = Breaker::build('testBreaker', new ArrayPersistence, array('timeout' => $timeout));

        $this->assertEquals($timeout, $breaker->getTimeout(), 'Timeout should be set in params');
    }

    public function testSettingTimeoutAndThresholdViaParams()
    {
        $threshold = 99;
        $timeout = 999;
        $breaker = Breaker::build('testBreaker', new ArrayPersistence, array('timeout' => $timeout, 'threshold' => $threshold));

        $this->assertEquals($timeout, $breaker->getTimeout(), 'Timeout should be set in params');
        $this->assertEquals($threshold, $breaker->getThreshold(), 'Threshold should be set in params');
    }
}
