<?php

namespace Yosmy\Payment;

use Yosmy;

/**
 * @di\service({
 *     private: true
 * })
 */
class SetupUser
{
    /**
     * @var User\CreateGid
     */
    private $createGid;

    /**
     * @var ManageUserCollection
     */
    private $manageCollection;

    /**
     * @param User\CreateGid       $createGid
     * @param ManageUserCollection $manageCollection
     */
    public function __construct(
        User\CreateGid $createGid,
        ManageUserCollection $manageCollection
    ) {
        $this->createGid = $createGid;
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param User $user
     *
     * @throws Exception
     */
    public function setup(
        User $user
    ) {
        if ($user->getGids()->has($user->getGateway())) {
            return;
        }

        try {
            $gid = $this->createGid->create($user->getGateway());
        } catch (Exception $e) {
            throw $e;
        }

        $this->manageCollection->updateOne(
            [
                '_id' => $user->getId()
            ],
            [
                '$addToSet' => [
                    'gids' => [
                        'id' => $gid,
                        'gateway' => $user->getGateway()
                    ]
                ]
            ]
        );
    }
}