<?php

namespace Yosmy\Payment;

use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.gateway_exception_throwed',
 *     ]
 * })
 */
class ProcessIssuerGatewayException implements ProcessGatewayException
{
    /**
     * {@inheritDoc}
     */
    public function process(
        $e,
        User $user
    ) {
        if (!$e instanceof Gateway\IssuerException) {
            return;
        }

        throw new KnownException('La tarjeta ha sido rechazada. Por favor consulta con tu banco');
    }
}