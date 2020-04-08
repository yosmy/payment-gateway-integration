<?php

namespace Yosmy\Payment\Card;

/**
 * @di\service()
 */
class CalculateFingerprint
{
    /**
     * @param string $number
     *
     * @return string
     */
    public function calculate(
        string $number
    ) {
        return md5($number);
    }
}