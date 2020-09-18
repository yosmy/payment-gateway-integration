<?php

namespace Yosmy\Payment;

interface AnalyzePostDeleteCardSuccess
{
    /**
     * @param Card $card
     */
    public function analyze(
        Card $card
    );
}