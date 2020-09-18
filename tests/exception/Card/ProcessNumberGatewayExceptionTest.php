<?php

namespace Yosmy\Payment\Test\Card;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;
use LogicException;

class ProcessNumberGatewayExceptionTest extends TestCase
{
    public function testProcessHavingDifferentException()
    {
        $processGatewayException = new Payment\Card\ProcessNumberGatewayException();

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
        $processGatewayException = new Payment\Card\ProcessNumberGatewayException();

        try {
            $processGatewayException->process(
                new Payment\Gateway\FieldException('cvc'),
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
        $processGatewayException = new Payment\Card\ProcessNumberGatewayException();

        $this->expectExceptionObject(new Payment\KnownException('El número de la tarjeta es incorrecto'));

        $processGatewayException->process(
            new Payment\Gateway\FieldException('number'),
            new Payment\BaseCustomer(
                '',
                '',
                '',
                new Payment\Gateway\Gids([])
            )
        );
    }
}