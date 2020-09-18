<?php

namespace Yosmy\Payment\Test\Card;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;
use LogicException;

class ProcessYearGatewayExceptionTest extends TestCase
{
    public function testProcessHavingDifferentException()
    {
        $processGatewayException = new Payment\Card\ProcessYearGatewayException();

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
        $processGatewayException = new Payment\Card\ProcessYearGatewayException();

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
        $processGatewayException = new Payment\Card\ProcessYearGatewayException();

        $this->expectExceptionObject(new Payment\KnownException('El año de expiración es incorrecto'));

        $processGatewayException->process(
            new Payment\Gateway\FieldException('year'),
            new Payment\BaseCustomer(
                '',
                '',
                '',
                new Payment\Gateway\Gids([])
            )
        );
    }
}