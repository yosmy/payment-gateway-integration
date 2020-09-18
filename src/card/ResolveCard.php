<?php

namespace Yosmy\Payment;

use Yosmy;

/**
 * @di\service({
 *     private: true
 * })
 */
class ResolveCard
{
    /**
     * @var Card\CalculateLast4
     */
    private $calculateLast4;

    /**
     * @var Card\CalculateFingerprint
     */
    private $calculateFingerprint;

    /**
     * @var ManageCardCollection
     */
    private $manageCollection;

    /**
     * @param Card\CalculateLast4            $calculateLast4
     * @param Card\CalculateFingerprint      $calculateFingerprint
     * @param ManageCardCollection         $manageCollection
     */
    public function __construct(
        Card\CalculateLast4 $calculateLast4,
        Card\CalculateFingerprint $calculateFingerprint,
        ManageCardCollection $manageCollection
    ) {
        $this->calculateLast4 = $calculateLast4;
        $this->calculateFingerprint = $calculateFingerprint;
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param Customer $customer
     * @param string       $number
     * @param string       $name
     * @param string       $month
     * @param string       $year
     * @param string       $cvc
     * @param string       $zip
     *
     * @return Card
     */
    public function resolve(
        Customer $customer,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    ): Card {
        $fingerprint = $this->calculateFingerprint->calculate($number);

        /** @var Card $card */
        $card = $this->manageCollection->findOne([
            'user' => $customer->getUser(),
            'fingerprint' => $fingerprint,
        ]);

        $raw = [
            'number' => $number,
            'name' => $name,
            'month' => $month,
            'year' => $year,
            'cvc' => $cvc,
            'zip' => $zip,
        ];

        // New?
        if (!$card) {
            $last4 = $this->calculateLast4->calculate($number);

            $this->manageCollection->insertOne([
                '_id' => uniqid(),
                'user' => $customer->getUser(),
                'last4' => $last4,
                'fingerprint' => $fingerprint,
                'gids' => [],
                'raw' => $raw
            ]);

            return $this->resolve(
                $customer,
                $number,
                $name,
                $month,
                $year,
                $cvc,
                $zip
            );
        }
        // Deleted?
        if ($card->isDeleted()) {
            $this->manageCollection->updateOne(
                [
                    '_id' => $card->getId()
                ],
                [
                    '$set' => [
                        'raw' => $raw
                    ]
                ]
            );

            return $this->resolve(
                $customer,
                $number,
                $name,
                $month,
                $year,
                $cvc,
                $zip
            );
        }

        return $card;
    }
}