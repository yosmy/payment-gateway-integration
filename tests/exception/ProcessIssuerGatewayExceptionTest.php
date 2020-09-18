<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;
use LogicException;

class ProcessIssuerGatewayExceptionTest extends TestCase
{
    public function testProcessHavingDifferentException()
    {
        $processGatewayException = new Payment\ProcessIssuerGatewayException();

        try {
            $processGatewayException->process(
                new Payment\Gateway\FundsException(),
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
        $processGatewayException = new Payment\ProcessIssuerGatewayException();

        $this->expectExceptionObject(new Payment\KnownException('La tarjeta ha sido rechazada. Por favor consulta con tu banco'));

        $processGatewayException->process(
            new Payment\Gateway\IssuerException(),
            new Payment\BaseCustomer(
                '',
                '',
                '',
                new Payment\Gateway\Gids([])
            )
        );
    }
}