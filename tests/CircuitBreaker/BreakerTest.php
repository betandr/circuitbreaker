<?php

namespace betandr\CircuitBreaker\Tests;

use betandr\CircuitBreaker\Breaker;
use betandr\CircuitBreaker\Persistence\ArrayPersistence;

class BreakerTest extends \PHPUnit_Framework_TestCase
{

    public function testIsClosedOnConstruction()
    {
        $breaker = new Breaker(new ArrayPersistence);
        $this->assertTrue($breaker->isClosed(), 'Breaker should be closed when constructed');
    }

    public function testBreakerOpensWhenThresholdReached()
    {
        $breaker = new Breaker(new ArrayPersistence);
        $breaker->setThreshold(1);

        $breaker->failure();

        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when threshold reached');
    }

    public function testBreakerDoesntOpenUntilThresholdReached()
    {
        $breaker = new Breaker(new ArrayPersistence);
        $breaker->setThreshold(2);

        $breaker->failure();

        $this->assertTrue($breaker->isClosed(), 'Breaker should be closed before threshold reached');
    }

    public function testOpenBreakerClosesWhenSuccessRegistered()
    {
        $breaker = new Breaker(new ArrayPersistence);
        $breaker->setThreshold(1);

        $breaker->failure();
        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when threshold reached');

        $breaker->success();
        $this->assertTrue($breaker->isClosed(), 'Breaker should re-close when success registered');
    }

    public function testOpeningBreaker()
    {
        $breaker = new Breaker(new ArrayPersistence);

        $breaker->open();
        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when explicitly opened');
    }

    public function testReClosingBreaker()
    {
        $breaker = new Breaker(new ArrayPersistence);

        $breaker->open();
        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when explicitly opened');

        $breaker->close();
        $this->assertTrue($breaker->isClosed(), 'Breaker should be closed when explicitly closed');
    }

    public function testResettingBreaker()
    {
        $breaker = new Breaker(new ArrayPersistence);

        $breaker->open();
        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when explicitly opened');

        $breaker->reset();
        $this->assertTrue($breaker->isClosed(), 'Breaker should be closed when reset');
    }

}
