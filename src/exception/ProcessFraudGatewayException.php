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
class ProcessFraudGatewayException implements ProcessGatewayException
{
    /**
     * {@inheritDoc}
     */
    public function process(
        $e,
        Customer $customer
    ) {
        if (!$e instanceof Gateway\FraudException) {
            return;
        }

        throw new KnownException('La tarjeta ha sido bloqueada por motivos de fraude. Por favor consulta con tu banco');
    }
}