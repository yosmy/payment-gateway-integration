<?php

namespace Yosmy\Payment;

interface AnalyzePostExecuteChargeSuccess
{
    /**
     * @param Charge $charge
     */
    public function analyze(
        Charge $charge
    );
}