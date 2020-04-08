<?php

namespace Yosmy\Payment;

use Yosmy\Payment\Gateway;

interface ProcessGatewayException
{
    /**
     * @param Gateway\FieldException|Gateway\FundsException|Gateway\IssuerException|Gateway\RiskException|Gateway\FraudException $e
     * @param User                                                                                                               $user
     *
     * @throws Exception
     */
    public function process(
        $e,
        User $user
    );
}
