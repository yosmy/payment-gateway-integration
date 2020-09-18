<?php

namespace Yosmy\Payment\Test\Card;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;
use LogicException;

class ProcessCvcGatewayExceptionTest extends TestCase
{
    public function testProcessHavingDifferentException()
    {
        $processGatewayException = new Payment\Card\ProcessCvcGatewayException();

        try {
            $processGatewayException->process(
                new Payment\Gateway\IssuerException(),
                new Payment\BaseCustomer(
                    '',
                    '',
                    '',
                    new Payment\Gateway\Gids([])
                )
            );
        } catch (Payment\Exception $e) {
            throw new LogicException();
        }

        $this->assertTrue(true);
    }

    public function testProcessHavingDifferentField()
    {
        $processGatewayException = new Payment\Card\ProcessCvcGatewayException();

        try {
            $processGatewayException->process(
                new Payment\Gateway\FieldException('month'),
                new Payment\BaseCustomer(
                    '',
                    '',
                    '',
                    new Payment\Gateway\Gids([])
                )
            );
        } catch (Payment\Exception $e) {
            throw new LogicException();
        }

        $this->assertTrue(true);
    }

    /**
     * @throws Payment\Exception
     */
    public function testProcessHavingCorrectException()
    {
        $processGatewayException = new Payment\Card\ProcessCvcGatewayException();

        $this->expectExceptionObject(new Payment\KnownException('El código de seguridad es incorrecto'));

        $processGatewayException->process(
            new Payment\Gateway\FieldException('cvc'),
            new Payment\BaseCustomer(
                '',
                '',
                '',
                new Payment\Gateway\Gids([])
            )
        );
    }
}