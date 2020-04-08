<?php

namespace Yosmy\Payment;

use Yosmy\Payment;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.add_card.in',
 *     ]
 * })
 */
class AnalyzeAddCardInToMonitorCardAmount implements Payment\AnalyzeAddCardIn
{
    /**
     * @var Payment\ManageCardCollection
     */
    private $manageCollection;

    /**
     * @var string
     */
    private $amount;

    /**
     * @di\arguments({
     *     amount: "%payment_card_amount%"
     * })
     *
     * @param Payment\ManageCardCollection $manageCollection
     * @param string                       $amount
     */
    public function __construct(
        Payment\ManageCardCollection $manageCollection,
        string $amount
    ) {
        $this->manageCollection = $manageCollection;
        $this->amount = $amount;
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(
        User $user,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    ) {
        unset($number, $name, $month, $year, $cvc, $zip);

        $amount = $this->manageCollection->count([
            'user' => $user->getId()
        ]);

        if ($amount >= $this->amount) {
            throw new Payment\KnownException(sprintf('Solo se permite añadir %s tarjetas', $this->amount));
        }
    }
}