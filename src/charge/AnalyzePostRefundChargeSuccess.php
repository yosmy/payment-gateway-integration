<?php

namespace Yosmy\Payment;

interface AnalyzePostRefundChargeSuccess
{
    /**
     * @param Charge $charge
     */
    public function analyze(
        Charge $charge
    );
}