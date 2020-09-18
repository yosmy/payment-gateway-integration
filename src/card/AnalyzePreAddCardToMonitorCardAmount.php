<?php

namespace Yosmy\Payment;

use Yosmy\Payment;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.pre_add_card',
 *     ]
 * })
 */
class AnalyzePreAddCardToMonitorCardAmount implements Payment\AnalyzePreAddCard
{
    /**
     * @var Payment\Card\CalculateFingerprint
     */
    private $calculateFingerprint;

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
     *     amount: "%yosmy_payment_card_amount%"
     * })
     *
     * @param Card\CalculateFingerprint $calculateFingerprint
     * @param ManageCardCollection $manageCollection
     * @param string $amount
     */
    public function __construct(
        Card\CalculateFingerprint $calculateFingerprint,
        ManageCardCollection $manageCollection,
        string $amount
    ) {
        $this->calculateFingerprint = $calculateFingerprint;
        $this->manageCollection = $manageCollection;
        $this->amount = $amount;
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(
        Customer $customer,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    ) {
        unset($name, $month, $year, $cvc, $zip);

        $fingerprint = $this->calculateFingerprint->calculate($number);

        $amount = $this->manageCollection->count([
            'user' => $customer->getUser(),
            'fingerprint' => ['$ne' => $fingerprint],
        ]);

        if ($amount >= $this->amount) {
            throw new Payment\KnownException('Has llegado al límite de tarjetas añadidas');
        }
    }
}