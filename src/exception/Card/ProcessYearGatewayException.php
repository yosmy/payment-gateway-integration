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
class ProcessYearGatewayException implements Payment\ProcessGatewayException
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

        if ($e->getField() != 'year') {
            return;
        }

        throw new Payment\KnownException('El año de expiración es incorrecto');
    }
}