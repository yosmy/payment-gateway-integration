<?php

namespace Yosmy\Payment;

use Yosmy;
use Yosmy\Payment\Card\CalculateFingerprint;

/**
 * @di\service()
 */
class AddCard
{
    /**
     * @var AnalyzeAddCardIn[]
     */
    private $analyzeAddCardIn;

    /**
     * @var AnalyzeAddCardSuccessedOut[]
     */
    private $analyzeAddCardSuccessedOut;

    /**
     * @var AnalyzeAddCardFailedOut[]
     */
    private $analyzeAddCardFailedOut;

    /**
     * @var Card\CalculateFingerprint
     */
    private $calculateFingerprint;

    /**
     * @var ManageCardCollection
     */
    private $manageCollection;

    /**
     * @var SetupCard
     */
    private $setupCard;

    /**
     * @di\arguments({
     *     analyzeAddCardIn:           '#yosmy.payment.add_card.in',
     *     analyzeAddCardSuccessedOut: '#yosmy.payment.add_card.successed_out',
     *     analyzeAddCardFailedOut:    '#yosmy.payment.add_card.failed_out'
     * })
     *
     * @param AnalyzeAddCardIn[]           $analyzeAddCardIn
     * @param AnalyzeAddCardSuccessedOut[] $analyzeAddCardSuccessedOut
     * @param AnalyzeAddCardFailedOut[]    $analyzeAddCardFailedOut
     * @param CalculateFingerprint         $calculateFingerprint
     * @param ManageCardCollection         $manageCollection
     * @param SetupCard                    $setupCard
     */
    public function __construct(
        ?array $analyzeAddCardIn,
        ?array $analyzeAddCardSuccessedOut,
        ?array $analyzeAddCardFailedOut,
        CalculateFingerprint $calculateFingerprint,
        ManageCardCollection $manageCollection,
        SetupCard $setupCard
    ) {
        $this->analyzeAddCardIn = $analyzeAddCardIn;
        $this->analyzeAddCardSuccessedOut = $analyzeAddCardSuccessedOut;
        $this->analyzeAddCardFailedOut = $analyzeAddCardFailedOut;
        $this->calculateFingerprint = $calculateFingerprint;
        $this->manageCollection = $manageCollection;
        $this->setupCard = $setupCard;
    }

    /**
     * @param User   $user
     * @param string $number
     * @param string $name
     * @param string $month
     * @param string $year
     * @param string $cvc
     * @param string $zip
     *
     * @return Card
     *
     * @throws Exception
     */
    public function add(
        User $user,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    ) {
        foreach ($this->analyzeAddCardIn as $analyzeAddCardIn) {
            try {
                $analyzeAddCardIn->analyze(
                    $user,
                    $number,
                    $name,
                    $month,
                    $year,
                    $cvc,
                    $zip
                );
            } catch (Exception $e) {
                throw $e;
            }
        }

        $fingerprint = $this->calculateFingerprint->calculate($number);

        /** @var Card $card */
        $card = $this->manageCollection->findOne([
            'user' => $user,
            'fingerprint' => $fingerprint,
        ]);

        $raw = [
            'number' => $number,
            'name' => $name,
            'month' => $month,
            'year' => $year,
            'cvc' => $cvc,
            'zip' => $zip
        ];

        if (!$card) {
            $card = uniqid();

            // Create the card, but with no gids
            $this->manageCollection->insertOne([
                '_id' => $card,
                'user' => $user->getId(),
                'last4' => substr($number, -4),
                'fingerprint' => $fingerprint,
                'gids' => [],
                'raw' => $raw
            ]);

            /** @var Card $card */
            $card = $this->manageCollection->findOne([
                '_id' => $card
            ]);

            $new = true;
        } else {
            if (!$card->isDeleted()) {
                throw new KnownException('Esta tarjeta ya la tienes añadida.');
            }

            $this->manageCollection->updateOne(
                [
                    '_id' => $card->getId(),
                ],
                [
                    '$set' => [
                        'raw' => $raw
                    ]
                ]
            );

            $new = false;
        }

        try {
            $this->setupCard->setup(
                $card
            );
        } catch (Exception $e) {
            if ($new) {
                $this->manageCollection->deleteOne([
                    '_id' => $card->getId(),
                ]);
            } else {
                $this->manageCollection->updateOne(
                    [
                        '_id' => $card->getId(),
                    ],
                    [
                        '$set' => [
                            'raw' => []
                        ]
                    ]
                );
            }

            foreach ($this->analyzeAddCardFailedOut as $analyzeAddCardFailedOut) {
                try {
                    $analyzeAddCardFailedOut->analyze(
                        $user,
                        $number,
                        $name,
                        $month,
                        $year,
                        $cvc,
                        $zip,
                        $e
                    );
                } catch (Exception $e) {
                    throw $e;
                }
            }

            throw $e;
        }

        foreach ($this->analyzeAddCardSuccessedOut as $analyzeAddCardSuccessedOut) {
            $analyzeAddCardSuccessedOut->analyze(
                $card
            );
        }

        return $card;
    }
}