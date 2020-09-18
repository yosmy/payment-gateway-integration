<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;

class KnownExceptionTest extends TestCase
{
    public function testSerialize()
    {
        $message = 'message-x';

        $exception = new Payment\KnownException(
            $message
        );

        $this->assertEquals(
            [
                'message' => $message
            ],
            $exception->jsonSerialize()
        );
    }
}