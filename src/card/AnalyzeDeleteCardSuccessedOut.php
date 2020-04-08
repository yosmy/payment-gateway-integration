<?php

namespace Yosmy\Payment;

interface AnalyzeDeleteCardSuccessedOut
{
    /**
     * @param Card $card
     */
    public function analyze(
        Card $card
    );
}