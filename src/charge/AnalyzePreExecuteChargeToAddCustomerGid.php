<?php

namespace Yosmy\Payment;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.pre_execute_charge',
 *     ]
 * })
 */
class AnalyzePreExecuteChargeToAddCustomerGid implements AnalyzePreExecuteCharge
{
    /**
     * @var GatherCustomer
     */
    private $gatherCustomer;

    /**
     * @var Customer\CreateGid
     */
    private $createGid;

    /**
     * @var Customer\AddGid
     */
    private $addGid;

    /**
     * @param GatherCustomer     $gatherCustomer
     * @param Customer\CreateGid $createGid
     * @param Customer\AddGid    $addGid
     */
    public function __construct(
        GatherCustomer $gatherCustomer,
        Customer\CreateGid $createGid,
        Customer\AddGid $addGid
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

        if ($customer->getGids()->has($customer->getGateway())) {
            return;
        }

        try {
            $gid = $this->createGid->create($customer);
        } catch (Exception $e) {
            throw $e;
        }

        $this->addGid->add(
            $customer,
            $gid
        );
    }
}