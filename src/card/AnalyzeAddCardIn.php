<?php

namespace Yosmy\Payment;

interface AnalyzeAddCardIn
{
    /**
     * @param User   $user
     * @param string $number
     * @param string $name
     * @param string $month
     * @param string $year
     * @param string $cvc
     * @param string $zip
     *
     * @throws Exception
     */
    public function analyze(
        User $user,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    );
}