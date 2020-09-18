<?php

namespace Yosmy\Payment;

interface AnalyzePreAddCard
{
    /**
     * @param Customer $customer
     * @param string       $number
     * @param string       $name
     * @param string       $month
     * @param string       $year
     * @param string       $cvc
     * @param string       $zip
     *
     * @throws Exception
     */
    public function analyze(
        Customer $customer,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    );
}