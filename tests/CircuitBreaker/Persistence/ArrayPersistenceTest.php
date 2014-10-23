<?php

namespace betandr\CircuitBreaker\Tests\Persistence;

use betandr\CircuitBreaker\Persistence\ArrayPersistence;

class ArrayPersistenceTest extends \PHPUnit_Framework_TestCase
{
    public function testValueIsStored()
    {
        $value = 'VALUE'.time();
        $key = 'KEY'.time();
        $persistence = new ArrayPersistence;

        $persistence->set($key, $value);

        $this->assertEquals($value, $persistence->get($key), 'Value from persistence should be same as was stored');
    }
}
