<?php

namespace Yosmy\Payment;

interface AnalyzeExecuteChargeFailedOut
{
    /**
     * @param Card $card
     * @param int  $amount
     * @param Gateway\FieldException|Gateway\FundsException|Gateway\IssuerException|Gateway\RiskException|Gateway\FraudException $exception
     *
     * @throws Exception
     */
    public function analyze(
        Card $card,
        int $amount,
        $exception
    );
}