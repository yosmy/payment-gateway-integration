<?php

namespace Yosmy\Payment;

/**
 * @di\service()
 */
class GatherCharge
{
    /**
     * @var ManageChargeCollection
     */
    private $manageCollection;

    /**
     * @param ManageChargeCollection $manageCollection
     */
    public function __construct(
        ManageChargeCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string      $id
     * @param string|null $user
     *
     * @return Charge
     */
    public function gather(
        string $id,
        ?string $user
    ): Charge {
        $criteria = [
            '_id' => $id
        ];

        if ($user) {
            $criteria['user'] = $user;
        }

        /** @var Charge $charge */
        $charge = $this->manageCollection->findOne($criteria);

        return $charge;
    }
}
