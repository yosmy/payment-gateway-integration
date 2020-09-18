<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;

class UnknownExceptionTest extends TestCase
{
    public function testSerialize()
    {
        $exception = new Payment\UnknownException();

        $this->assertEquals(
            [
                'message' => 'Se produjo con error con tu tarjeta. Intenta más tarde o contacta con tu banco'
            ],
            $exception->jsonSerialize()
        );
    }
}