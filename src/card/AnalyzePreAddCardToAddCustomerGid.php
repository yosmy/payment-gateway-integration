<?php

namespace Yosmy\Payment;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.pre_add_card',
 *     ]
 * })
 */
class AnalyzePreAddCardToAddCustomerGid implements AnalyzePreAddCard
{
    /**
     * @var Customer\CreateGid
     */
    private $createGid;

    /**
     * @var Customer\AddGid
     */
    private $addGid;

    /**
     * @param Customer\CreateGid $createGid
     * @param Customer\AddGid    $addGid
     */
    public function __construct(
        Customer\CreateGid $createGid,
        Customer\AddGid $addGid
    ) {
        $this->createGid = $createGid;
        $this->addGid = $addGid;
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(
        Customer $customer,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    ) {
        if ($customer->getGids()->has($customer->getGateway())) {
            return;
        }

        try {
            $gid = $this->createGid->create(
                $customer->getGateway()
            );
        } catch (Exception $e) {
            throw $e;
        }

        $this->addGid->add(
            $customer,
            $gid
        );
    }
}