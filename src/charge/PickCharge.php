<?php

namespace Yosmy\Payment;

/**
 * @di\service()
 */
class PickCharge
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
     * @param string $id
     * @param string $user
     *
     * @return Charge
     */
    public function pick(
        string $id,
        ?string $user
    ) {
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

    /**
     * @param string $sid
     *
     * @return Charge
     *
     * @throws NonexistentChargeException
     */
    public function pickBySid(
        string $sid
    ) {
        /** @var Charge $charge */
        $charge = $this->manageCollection->findOne([
            'sid' => $sid
        ]);

        if (!$charge) {
            throw new NonexistentChargeException();
        }

        return $charge;
    }
}
