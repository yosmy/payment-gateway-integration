<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;
use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.gateway_exception_throwed'
 *     ]
 * })
 */
class ProcessMonthGatewayException implements Payment\ProcessGatewayException
{
    /**
     * {@inheritDoc}
     */
    public function process(
        $e,
        Payment\User $user
    ) {
        if (!$e instanceof Gateway\FieldException) {
            return;
        }

        if ($e->getField() != 'month') {
            return;
        }

        throw new Payment\KnownException('El mes de expiración es incorrecto');
    }
}