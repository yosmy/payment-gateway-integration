<?php

namespace Yosmy\Payment;

/**
 * @di\service()
 */
class GatherCustomer
{
    /**
     * @var ManageCustomerCollection
     */
    private $manageCollection;

    /**
     * @param ManageCustomerCollection $manageCollection
     */
    public function __construct(
        ManageCustomerCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string $user
     *
     * @return Customer
     */
    public function gather(
        string $user
    ): Customer {
        /** @var Customer $customer */
        $customer = $this->manageCollection->findOne([
            '_id' => $user
        ]);

        return $customer;
    }
}
