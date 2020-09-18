<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class AddGid
{
    /**
     * @var Payment\ManageCardCollection
     */
    private $manageCollection;

    /**
     * @var Payment\GatherCard
     */
    private $gatherCard;

    /**
     * @param Payment\ManageCardCollection $manageCollection
     * @param Payment\GatherCard           $gatherCard
     */
    public function __construct(
        Payment\ManageCardCollection $manageCollection,
        Payment\GatherCard $gatherCard
    ) {
        $this->manageCollection = $manageCollection;
        $this->gatherCard = $gatherCard;
    }

    /**
     * @param Payment\Card         $card
     * @param Payment\Customer $customer
     * @param string               $gid
     *
     * @return Payment\Card
     */
    public function add(
        Payment\Card $card,
        Payment\Customer $customer,
        string $gid
    ): Payment\Card {
        $this->manageCollection->updateOne(
            [
                '_id' => $card->getId()
            ],
            [
                '$addToSet' => [
                    'gids' => [
                        'id' => $gid,
                        'gateway' => $customer->getGateway()
                    ]
                ]
            ]
        );

        return $this->gatherCard->gather(
            $card->getId(),
            null,
            null
        );
    }
}