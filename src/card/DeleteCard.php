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
     * @var GatherCustomer
     */
    private $gatherCustomer;

    /**
     * @var ManageCardCollection
     */
    private $manageCollection;

    /**
     * @var Gateway\Card\Delete\SelectGateway
     */
    private $selectGateway;

    /**
     * @var AnalyzePostDeleteCardSuccess[]
     */
    private $analyzePostDeleteCardSuccessServices;

    /**
     * @di\arguments({
     *     analyzePostDeleteCardSuccessServices: '#yosmy.payment.post_delete_card_success'
     * })
     *
     * @param GatherCustomer                    $gatherCustomer
     * @param ManageCardCollection              $manageCollection
     * @param Gateway\Card\Delete\SelectGateway $selectGateway
     * @param AnalyzePostDeleteCardSuccess[]    $analyzePostDeleteCardSuccessServices
     */
    public function __construct(
        GatherCustomer $gatherCustomer,
        ManageCardCollection $manageCollection,
        Gateway\Card\Delete\SelectGateway $selectGateway,
        ?array $analyzePostDeleteCardSuccessServices
    ) {
        $this->gatherCustomer = $gatherCustomer;
        $this->manageCollection = $manageCollection;
        $this->selectGateway = $selectGateway;
        $this->analyzePostDeleteCardSuccessServices = $analyzePostDeleteCardSuccessServices;
    }

    /**
     * @param Card $card
     */
    public function delete(
        Card $card
    ) {
        $customer = $this->gatherCustomer->gather(
            $card->getUser()
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
                    $customer->getGids()->get($gateway),
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

        foreach ($this->analyzePostDeleteCardSuccessServices as $analyzePostDeleteCardSuccess) {
            $analyzePostDeleteCardSuccess->analyze(
                $card
            );
        }
    }
}
