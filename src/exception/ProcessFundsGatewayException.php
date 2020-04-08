<?php

namespace Yosmy\Payment;

use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.gateway_exception_throwed'
 *     ]
 * })
 */
class ProcessFundsGatewayException implements ProcessGatewayException
{
    /**
     * {@inheritDoc}
     */
    public function process(
        $e,
        User $user
    ) {
        if (!$e instanceof Gateway\FundsException) {
            return;
        }

        throw new KnownException('La tarjeta no tiene dinero suficiente');
    }
}