<?php

namespace Yosmy\Payment;

use Yosmy;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.pre_execute_charge',
 *     ]
 * })
 */
class AnalyzePreExecuteChargeToAddCardGid implements AnalyzePreExecuteCharge
{
    /**
     * @var GatherCustomer
     */
    private $gatherCustomer;

    /**
     * @var Card\CreateGid
     */
    private $createGid;

    /**
     * @var Card\AddGid
     */
    private $addGid;

    /**
     * @param GatherCustomer $gatherCustomer
     * @param Card\CreateGid $createGid
     * @param Card\AddGid    $addGid
     */
    public function __construct(
        GatherCustomer $gatherCustomer,
        Card\CreateGid $createGid,
        Card\AddGid $addGid
    ) {
        $this->gatherCustomer = $gatherCustomer;
        $this->createGid = $createGid;
        $this->addGid = $addGid;
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(
        Card $card,
        int $amount,
        string $description,
        string $statement
    ) {
        $customer = $this->gatherCustomer->gather(
            $card->getUser()
        );

        if ($card->getGids()->has($customer->getGateway())) {
            return;
        }

        $raw = $card->getRaw();

        try {
            $gid = $this->createGid->create(
                $customer,
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

        $this->addGid->add(
            $card,
            $customer,
            $gid
        );
    }
}