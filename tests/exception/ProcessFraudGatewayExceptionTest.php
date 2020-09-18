<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;
use LogicException;

class ProcessFraudGatewayExceptionTest extends TestCase
{
    public function testProcessHavingDifferentException()
    {
        $processGatewayException = new Payment\ProcessFraudGatewayException();

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

    /**
     * @throws Payment\Exception
     */
    public function testProcessHavingCorrectException()
    {
        $processGatewayException = new Payment\ProcessFraudGatewayException();

        $this->expectExceptionObject(new Payment\KnownException('La tarjeta ha sido bloqueada por motivos de fraude. Por favor consulta con tu banco'));

        $processGatewayException->process(
            new Payment\Gateway\FraudException(),
            new Payment\BaseCustomer(
                '',
                '',
                '',
                new Payment\Gateway\Gids([])
            )
        );
    }
}