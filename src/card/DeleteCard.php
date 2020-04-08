<?php

namespace Yosmy\Payment;

use Yosmy\Payment\Gateway;
use Yosmy;

/**
 * @di\service()
 */
class DeleteCard
{
    /**
     * @var GatherUser
     */
    private $gatherUser;

    /**
     * @var GatherCard
     */
    private $gatherCard;

    /**
     * @var ManageCardCollection
     */
    private $manageCollection;

    /**
     * @var Gateway\Card\Delete\SelectGateway
     */
    private $selectGateway;

    /**
     * @var AnalyzeDeleteCardSuccessedOut[]
     */
    private $analyzeDeleteCardSuccessedOut;

    /**
     * @di\arguments({
     *     analyzeDeleteCardSuccessedOut: '#yosmy.payment.delete_card.successed_out'
     * })
     *
     * @param GatherUser                        $gatherUser
     * @param GatherCard                        $gatherCard
     * @param ManageCardCollection              $manageCollection
     * @param Gateway\Card\Delete\SelectGateway $selectGateway
     * @param AnalyzeDeleteCardSuccessedOut[]   $analyzeDeleteCardSuccessedOut
     */
    public function __construct(
        GatherUser $gatherUser,
        GatherCard $gatherCard,
        ManageCardCollection $manageCollection,
        Gateway\Card\Delete\SelectGateway $selectGateway,
        ?array $analyzeDeleteCardSuccessedOut
    ) {
        $this->gatherUser = $gatherUser;
        $this->gatherCard = $gatherCard;
        $this->manageCollection = $manageCollection;
        $this->selectGateway = $selectGateway;
        $this->analyzeDeleteCardSuccessedOut = $analyzeDeleteCardSuccessedOut;
    }

    /**
     * @param string $card
     * @param string $user
     */
    public function delete(
        string $card,
        string $user
    ) {
        $user = $this->gatherUser->gather($user);

        $card = $this->gatherCard->gather(
            $card,
            $user->getId(),
            null
        );

        // Raw field empty, means card deleted

        $this->manageCollection->updateOne(
            [
                '_id' => $card->getId()
            ],
            [
                '$set' => [
                    'raw' => []
                ]
            ]
        );

        foreach ($card->getGids()->all() as $gid) {
            $gateway = $gid->getGateway();

            try {
                $this->selectGateway->select($gateway)->delete(
                    $user->getGids()->get($gateway),
                    $card->getGids()->get($gateway)
                );
            } catch (Gateway\UnknownException $e) {
                continue;
            }

            $this->manageCollection->updateOne(
                [
                    '_id' => $card->getId()
                ],
                [
                    '$pull' => [
                        'gids' => ['gateway' => $gateway]
                    ]
                ]
            );
        }

        foreach ($this->analyzeDeleteCardSuccessedOut as $analyzeDeleteCardSuccessedOut) {
            $analyzeDeleteCardSuccessedOut->analyze(
                $card
            );
        }
    }
}
