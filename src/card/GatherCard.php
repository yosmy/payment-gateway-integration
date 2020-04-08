<?php

namespace Yosmy\Payment;

/**
 * @di\service()
 */
class GatherCard
{
    /**
     * @var ManageCardCollection
     */
    private $manageCollection;

    /**
     * @param ManageCardCollection $manageCollection
     */
    public function __construct(
        ManageCardCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string $id
     * @param string $user
     * @param string $fingerprint
     *
     * @return Card
     */
    public function gather(
        ?string $id,
        ?string $user,
        ?string $fingerprint
    ) {
        $criteria = [
            '_id' => $id
        ];

        if ($user !== null) {
            $criteria['user'] = $user;
        }

        if ($fingerprint !== null) {
            $criteria['fingerprint'] = $fingerprint;
        }

        /** @var Card $card */
        $card = $this->manageCollection->findOne($criteria);

        return $card;
    }
}
