<?php

namespace Yosmy\Payment\Customer;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class AddGid
{
    /**
     * @var Payment\ManageCustomerCollection
     */
    private $manageCollection;

    /**
     * @param Payment\ManageCustomerCollection $manageCollection
     */
    public function __construct(
        Payment\ManageCustomerCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param Payment\Customer $customer
     * @param string               $gid
     */
    public function add(
        Payment\Customer $customer,
        string $gid
    ) {
        $this->manageCollection->updateOne(
            [
                '_id' => $customer->getUser()
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
    }
}