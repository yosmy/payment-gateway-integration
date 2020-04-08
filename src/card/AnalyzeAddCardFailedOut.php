<?php

namespace Yosmy\Payment;

interface AnalyzeAddCardFailedOut
{
    /**
     * @param User   $user
     * @param string $number
     * @param string $name
     * @param string $month
     * @param string $year
     * @param string $cvc
     * @param string $zip
     * @param Exception $exception
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
        string $zip,
        Exception $exception
    );
}