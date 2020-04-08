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
class AnalyzeAddCardInToDetectAlreadyUsedCard implements Payment\AnalyzeAddCardIn
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
     * @param Card\CalculateFingerprint    $calculateFingerprint
     * @param Payment\ManageCardCollection $manageCollection
     */
    public function __construct(
        Card\CalculateFingerprint $calculateFingerprint,
        Payment\ManageCardCollection $manageCollection
    ) {
        $this->calculateFingerprint = $calculateFingerprint;
        $this->manageCollection = $manageCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(
        Payment\User $user,
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
            'user' => ['$ne' => $user->getId()],
            'fingerprint' => $fingerprint,
        ]);

        if ($card) {
            throw new Payment\KnownException("Esta tarjeta ya es usada por otro usuario");
        }
    }
}