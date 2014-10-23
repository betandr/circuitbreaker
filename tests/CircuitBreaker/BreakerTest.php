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

}
