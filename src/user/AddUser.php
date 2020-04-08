<?php

namespace Yosmy\Payment;

/**
 * @di\service()
 */
class AddUser
{
    /**
     * @var ManageUserCollection
     */
    private $manageCollection;

    /**
     * @param ManageUserCollection $manageCollection
     */
    public function __construct(
        ManageUserCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string $id
     * @param string $country
     * @param string $gateway
     *
     * @return User
     */
    public function add(
        string $id,
        string $country,
        string $gateway
    ) {
        $gids = [];

        $this->manageCollection->insertOne([
            '_id' => $id,
            'country' => $country,
            'gateway' => $gateway,
            'gids' => $gids
        ]);

        return new User(
            $id,
            $country,
            $gateway,
            new Gateway\Gids($gids)
        );
    }
}
