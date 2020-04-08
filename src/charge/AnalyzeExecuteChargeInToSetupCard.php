<?php

namespace Yosmy\Payment;

use Yosmy;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.execute_charge.in',
 *     ]
 * })
 */
class AnalyzeExecuteChargeInToSetupCard implements AnalyzeExecuteChargeIn
{
    /**
     * @var SetupCard
     */
    private $setupCard;

    /**
     * @param SetupCard $setupCard
     */
    public function __construct(
        SetupCard $setupCard
    ) {
        $this->setupCard = $setupCard;
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(
        Card $card,
        int $amount,
        string $description,
        string $statement
    ) {
        try {
            $this->setupCard->setup($card);
        } catch (Exception $e) {
            throw $e;
        }
    }
}