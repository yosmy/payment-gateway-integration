<?php

namespace Yosmy\Payment;

use Yosmy;

/**
 * @di\service()
 */
class SetupCard
{
    /**
     * @var GatherUser
     */
    private $gatherUser;

    /**
     * @var Card\CreateGid
     */
    private $createGid;

    /**
     * @var ManageCardCollection
     */
    private $manageCollection;

    /**
     * @param GatherUser             $gatherUser
     * @param Card\CreateGid       $createGid
     * @param ManageCardCollection $manageCollection
     */
    public function __construct(
        GatherUser $gatherUser,
        Card\CreateGid $createGid,
        ManageCardCollection $manageCollection
    ) {
        $this->gatherUser = $gatherUser;
        $this->createGid = $createGid;
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param Card $card
     *
     * @throws Exception
     */
    public function setup(
        Card $card
    ) {
        $user = $this->gatherUser->gather($card->getUser());

        if ($card->getGids()->has($user->getGateway())) {
            return;
        }

        $raw = $card->getRaw();

        try {
            $gid = $this->createGid->create(
                $user,
                $raw['number'],
                $raw['name'],
                $raw['month'],
                $raw['year'],
                $raw['cvc'],
                $raw['zip']
            );
        } catch (Exception $e) {
            throw $e;
        }

        $this->manageCollection->updateOne(
            [
                '_id' => $card->getId()
            ],
            [
                '$addToSet' => [
                    'gids' => [
                        'id' => $gid,
                        'gateway' => $user->getGateway()
                    ]
                ]
            ]
        );
    }
}