<?php

namespace Yosmy\Payment;

interface AnalyzeAddCardSuccessedOut
{
    /**
     * @param Card $card
     */
    public function analyze(
        Card $card
    );
}