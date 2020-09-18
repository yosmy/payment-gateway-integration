<?php

namespace Yosmy\Payment;

use Yosmy\Mongo;

/**
 * @di\service()
 */
class CollectCharges
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
     * @param array|null  $ids
     * @param string|null $user
     * @param string|null $card
     * @param int|null    $from
     * @param int|null    $to
     * @param int|null    $skip
     * @param int|null    $limit
     *
     * @return Charges
     */
    public function collect(
        ?array $ids,
        ?string $user,
        ?string $card,
        ?int $from,
        ?int $to,
        ?int $skip,
        ?int $limit
    ): Charges {
        $criteria = [];

        if ($ids != null) {
            $criteria['_id'] = ['$in' => $ids];
        }

        if ($user != null) {
            $criteria['user'] = $user;
        }

        if ($card != null) {
            $criteria['card'] = $card;
        }

        if ($from !== null) {
            $criteria['date']['$gte'] = new Mongo\DateTime($from * 1000);
        }

        if ($to !== null) {
            $criteria['date']['$lt'] = new Mongo\DateTime($to * 1000);
        }

        $options = [];

        if ($skip !== null) {
            $options['skip'] = $skip;
        }

        if ($limit !== null) {
            $options['limit'] = $limit;
        }

        $options['sort'] = [
            'date' => -1,
            '_id' => -1
        ];

        return $this->query($criteria, $options);
    }

    /**
     * @param array $criteria
     * @param array $options
     *
     * @return Charges
     */
    private function query(
        array $criteria,
        array $options
    ): Charges {
        $cursor = $this->manageCollection->find($criteria, $options);

        return new Charges($cursor);
    }
}
