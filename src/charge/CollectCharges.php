<?php

namespace Yosmy\Payment;

use MongoDB\BSON\UTCDateTime;

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
     * @param array  $ids
     * @param string $user
     * @param string $card
     * @param int    $from
     * @param int    $to
     *
     * @return Charges
     */
    public function collect(
        ?array $ids,
        ?string $user,
        ?string $card,
        ?int $from,
        ?int $to
    ) {
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
            $criteria['date']['$gte'] = new UTCDateTime($from * 1000);
        }

        if ($to !== null) {
            $criteria['date']['$lt'] = new UTCDatetime($to * 1000);
        }

        return $this->query($criteria);
    }

    /**
     * @param array $criteria
     *
     * @return Charges
     */
    private function query(
        array $criteria
    ) {
        $cursor = $this->manageCollection->find($criteria);

        return new Charges($cursor);
    }
}
