<?php

namespace Yosmy\Payment\Charge;

use Yosmy;

/**
 * @di\service()
 */
class NormalizeAmount
{
    /**
     * @param string $amount
     *
     * @return int
     */
    public function normalize(
        string $amount
    ) {
        // Remove characters like dot, leaving just numbers
        $amount = preg_replace('/[^0-9]/', '', $amount);

        return (int) $amount;
    }
}