<?php

namespace Yosmy\Payment;

use Yosmy;
use LogicException;

/**
 * @di\service()
 */
class VoidCharge
{
    /**
     * @var ManageChargeCollection
     */
    private $manageCollection;

    /**
     * @var Gateway\Charge\Void\SelectGateway
     */
    private $selectGateway;

    /**
     * @var GatherCard
     */
    private $gatherCard;

    /**
     * @var GatherUser
     */
    private $gatherUser;

    /**
     * @var Yosmy\LogEvent
     */
    private $logEvent;

    /**
     * @param ManageChargeCollection            $manageCollection
     * @param Gateway\Charge\Void\SelectGateway $selectGateway
     * @param GatherCard                        $gatherCard
     * @param GatherUser                        $gatherUser
     * @param Yosmy\LogEvent                   $logEvent
     */
    public function __construct(
        ManageChargeCollection $manageCollection,
        Gateway\Charge\Void\SelectGateway $selectGateway,
        GatherCard $gatherCard,
        GatherUser $gatherUser,
        Yosmy\LogEvent $logEvent
    ) {
        $this->manageCollection = $manageCollection;
        $this->selectGateway = $selectGateway;
        $this->gatherCard = $gatherCard;
        $this->gatherUser = $gatherUser;
        $this->logEvent = $logEvent;
    }

    /**
     * @param string $id
     */
    public function void(
        string $id
    ) {
        /** @var Charge $charge */
        $charge = $this->manageCollection->findOne([
            '_id' => $id
        ]);

        try {
            $this->selectGateway->select($charge->getGid()->getGateway())->void(
                $charge->getGid()->getId()
            );
        } catch (Gateway\UnknownException $e) {
            throw new LogicException(null, null, $e);
        }

        $card = $this->gatherCard->gather(
            $charge->getCard(),
            $charge->getUser(),
            null
        );

        $user = $this->gatherUser->gather(
            $charge->getUser()
        );

        $this->logEvent->log(
            'yosmy.payment.void_charge',
            [
                'user' => $user,
                'fingerprint' => $card->getFingerprint(),
                'charge' => $charge->getId()
            ],
            []
        );
    }
}