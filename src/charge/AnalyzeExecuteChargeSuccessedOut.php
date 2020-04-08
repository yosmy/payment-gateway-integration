<?php

namespace Yosmy\Payment;

interface AnalyzeExecuteChargeSuccessedOut
{
    /**
     * @param Card $card
     * @param int  $amount
     * @param Charge $charge
     */
    public function analyze(
        Card $card,
        int $amount,
        Charge $charge
    );
}