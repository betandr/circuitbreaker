<?php

namespace betandr\CircuitBreaker\Tests;

use betandr\CircuitBreaker\Breaker;

class BreakerTest extends \PHPUnit_Framework_TestCase
{

    public function testIsOpenOnConstruction()
    {
        $breaker = new Breaker();
        $this->assertTrue($breaker->isOpen(), 'Breaker should be open when constructed');
    }

}
