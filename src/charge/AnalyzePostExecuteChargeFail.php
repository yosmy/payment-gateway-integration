<?php

namespace Yosmy\Payment;

interface AnalyzePostExecuteChargeFail
{
    /**
     * @param Card      $card
     * @param int       $amount
     * @param Exception $exception
     */
    public function analyze(
        Card $card,
        int $amount,
        Exception $exception
    );
}