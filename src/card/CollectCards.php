<?php

namespace Yosmy\Payment;

/**
 * @di\service()
 */
class CollectCards
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
     * @param array  $ids
     * @param string $user
     * @param string $fingerprint
     * @param bool   $deleted
     *
     * @return Cards
     */
    public function collect(
        ?array $ids,
        ?string $user,
        ?string $fingerprint,
        ?bool $deleted
    ) {
        $criteria = [];

        if ($ids != null) {
            $criteria['_id'] = ['$in' => $ids];
        }

        if ($user !== null) {
            $criteria['user'] = $user;
        }

        if ($fingerprint !== null) {
            $criteria['fingerprint'] = $fingerprint;
        }

        if ($deleted === false) {
            $criteria['raw'] = ['$ne' => []];
        }

        $cursor = $this->manageCollection->find($criteria);

        return new Cards($cursor);
    }
}
