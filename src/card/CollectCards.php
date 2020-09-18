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
     * @param array|null  $ids
     * @param string|null $user
     * @param string|null $fingerprint
     * @param bool|null   $deleted
     * @param int|null    $skip
     * @param int|null     $limit
     *
     * @return Cards
     */
    public function collect(
        ?array $ids,
        ?string $user,
        ?string $fingerprint,
        ?bool $deleted,
        ?int $skip,
        ?int $limit
    ): Cards {
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

        $options = [];

        if ($skip !== null) {
            $options['skip'] = $skip;
        }

        if ($limit !== null) {
            $options['limit'] = $limit;
        }

        $cursor = $this->manageCollection->find($criteria, $options);

        return new Cards($cursor);
    }
}
