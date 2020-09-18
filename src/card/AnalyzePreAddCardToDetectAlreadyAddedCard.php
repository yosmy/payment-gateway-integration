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
class AnalyzePreAddCardToDetectAlreadyAddedCard implements Payment\AnalyzePreAddCard
{
    /**
     * @var Card\CalculateFingerprint
     */
    private $calculateFingerprint;

    /**
     * @var Payment\ManageCardCollection
     */
    private $manageCollection;

    /**
     * @param Card\CalculateFingerprint $calculateFingerprint
     * @param ManageCardCollection      $manageCollection
     */
    public function __construct(
        Card\CalculateFingerprint $calculateFingerprint,
        ManageCardCollection $manageCollection
    ) {
        $this->calculateFingerprint = $calculateFingerprint;
        $this->manageCollection = $manageCollection;
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
        $fingerprint = $this->calculateFingerprint->calculate($number);

        /** @var Payment\Card $card */
        $card = $this->manageCollection->findOne([
            'user' => $customer->getUser(),
            'fingerprint' => $fingerprint,
        ]);

        if ($card && !$card->isDeleted()) {
            throw new KnownException('Esta tarjeta ya la tienes añadida');
        }
    }
}