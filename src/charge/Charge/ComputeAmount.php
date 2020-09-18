<?php

namespace Yosmy\Payment\Charge;

use Yosmy\Mongo;
use Yosmy;
use Yosmy\Payment;

/**
 * @di\service({
 *     private: true
 * })
 */
class ComputeAmount
{
    /**
     * @var Payment\ManageChargeCollection
     */
    private $manageCollection;

    /**
     * @param Payment\ManageChargeCollection $manageCollection
     */
    public function __construct(
        Payment\ManageChargeCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param array|null $users
     * @param array|null $cards
     * @param int|null   $from
     * @param int|null   $to
     *
     * @return int
     */
    public function compute(
        ?array $users,
        ?array $cards,
        ?int $from,
        ?int $to
    ): int {
        $match = [];

        if ($users !== null) {
            $match['user'] = ['$in' => $users];
        }

        if ($cards !== null) {
            $match['cards'] = ['$in' => $cards];
        }

        if ($from !== null) {
            $match['date']['$gte'] = new Mongo\DateTime($from * 1000);
        }

        if ($to !== null) {
            $match['date']['$lt'] = new Mongo\Datetime($to * 1000);
        }

        $data = iterator_to_array($this->manageCollection->aggregate(
            [
                ['$match' => $match],
                ['$group' => [
                    '_id' => '',
                    'total' => ['$sum' => '$amount']
                ]]
            ],
            [
                'typeMap' => [
                    'root' => 'array',
                    'document' => 'array'
                ],
            ]
        ));

        $total = 0;

        if (
            isset($data[0])
            && isset($data[0]['total'])
        ) {
            $total = $data[0]['total'];
        }

        return $total;
    }
}