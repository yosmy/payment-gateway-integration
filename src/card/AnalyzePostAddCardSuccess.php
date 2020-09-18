<?php

namespace Yosmy\Payment;

interface AnalyzePostAddCardSuccess
{
    /**
     * @param Card $card
     */
    public function analyze(
        Card $card
    );
}