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
class ProcessNumberGatewayException implements Payment\ProcessGatewayException
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

        if ($e->getField() != 'number') {
            return;
        }

        throw new Payment\KnownException('El número de la tarjeta es incorrecto');
    }
}