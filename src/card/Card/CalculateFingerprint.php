<?php

namespace Yosmy\Payment\Card;

/**
 * @di\service({
 *     private: true
 * })
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
    ): string {
        return md5($number);
    }
}