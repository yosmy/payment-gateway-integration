<?php

namespace Yosmy\Payment;

/**
 * @di\service()
 */
class AddCustomer
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
     * @param string $country
     * @param string $gateway
     *
     * @return Customer
     */
    public function add(
        string $user,
        string $country,
        string $gateway
    ): Customer {
        $gids = [];

        $this->manageCollection->insertOne([
            '_id' => $user,
            'country' => $country,
            'gateway' => $gateway,
            'gids' => []
        ]);

        return new BaseCustomer(
            $user,
            $country,
            $gateway,
            new Gateway\Gids($gids)
        );
    }
}
