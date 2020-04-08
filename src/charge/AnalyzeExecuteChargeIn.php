<?php

namespace Yosmy\Payment;

interface AnalyzeExecuteChargeIn
{
    /**
     * @param Card   $card
     * @param int    $amount
     * @param string $description
     * @param string $statement
     *
     * @throws Exception
     */
    public function analyze(
        Card $card,
        int $amount,
        string $description,
        string $statement
    );
}