<?php

namespace Yosmy\Payment\Card;

/**
 * @di\service({
 *     private: true
 * })
 */
class CalculateLast4
{
    /**
     * @param string $number
     *
     * @return string
     */
    public function calculate(
        string $number
    ): string {
        return substr($number, -4);
    }
}