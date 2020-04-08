<?php

namespace Yosmy\Payment;

/**
 * @di\service()
 */
class GatherUser
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
     *
     * @return User
     */
    public function gather(
        string $id
    ) {
        /** @var User $user */
        $user = $this->manageCollection->findOne([
            '_id' => $id
        ]);

        return $user;
    }
}
