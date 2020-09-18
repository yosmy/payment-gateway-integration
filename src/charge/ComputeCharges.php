<?php

namespace Yosmy\Payment;

use Yosmy\Mongo;

/**
 * @di\service()
 */
class ComputeCharges
{
    const GROUP_BY_DAY = 'by-day';
    const GROUP_BY_MONTH = 'by-month';
    const GROUP_BY_YEAR = 'by-year';

    /**
     * @var ManageChargeCollection
     */
    private $manageCollection;

    /**
     * @var Mongo\PrepareAggregation
     */
    private $prepareAggregation;

    /**
     * @param ManageChargeCollection   $manageCollection
     * @param Mongo\PrepareAggregation $prepareAggregation
     */
    public function __construct(
        ManageChargeCollection $manageCollection,
        Mongo\PrepareAggregation $prepareAggregation
    ) {
        $this->manageCollection = $manageCollection;
        $this->prepareAggregation = $prepareAggregation;
    }

    /**
     * @param string|null $user
     * @param int|null    $from
     * @param int|null    $to
     * @param string|null $timezone
     * @param string|null $group
     *
     * @return array
     */
    public function compute(
        ?string $user,
        ?int $from,
        ?int $to,
        string $timezone,
        ?string $group
    ): array {
        $criteria = [];

        if ($user !== null) {
            $criteria['user'] = $user;
        }

        if ($from !== null) {
            $criteria['date']['$gte'] = new Mongo\DateTime($from * 1000);
        }

        if ($to !== null) {
            $criteria['date']['$lt'] = new Mongo\DateTime($to * 1000);
        }

        switch ($group) {
            case self::GROUP_BY_DAY:
                $date = [
                    'year' => ['$year' => '$date'],
                    'month' => ['$month' => '$date'],
                    'day' => ['$dayOfMonth' => '$date']
                ];

                break;
            case self::GROUP_BY_MONTH:
                $date = [
                    'year' => ['$year' => '$date'],
                    'month' => ['$month' => '$date']
                ];

                break;
            case self::GROUP_BY_YEAR:
                $date = [
                    'year' => ['$year' => '$date']
                ];

                break;
            default:
                $date = [
                    'year' => ['$year' => '$date'],
                    'month' => ['$month' => '$date'],
                    'day' => ['$dayOfMonth' => '$date']
                ];
        }

        $response = $this->manageCollection->aggregate(
            [
                ['$project' => [
                    '_id' => 1,
                    'date' => 1,
                ]],
                ['$match' => $criteria],
                ['$group' => [
                    '_id' => $date,
                    'total' => [
                        '$sum' => 1
                    ]
                ]],
                ['$sort' => ['_id' => 1]]
            ],
            [
                'typeMap' => [
                    'root' => 'array',
                    'document' => 'array'
                ],
            ]
        );

        $response = iterator_to_array($response);

        if (!$response) {
            switch ($group) {
                case self::GROUP_BY_DAY:
                    $id = [
                        'year' => date('Y', $from),
                        'month' => date('n', $from),
                        'day' => date('j', $from),
                    ];

                    break;
                case self::GROUP_BY_MONTH:
                    $id = [
                        'year' => date('Y', $from),
                        'month' => date('n', $from),
                    ];

                    break;
                case self::GROUP_BY_YEAR:
                    $id = [
                        'year' => date('Y', $from),
                    ];

                    break;
                default:
                    $id = [
                        'year' => date('Y', $from),
                        'month' => date('m', $from),
                        'day' => date('j', $from),
                    ];
            }

            $response = [
                [
                    '_id' => $id,
                    'total' => 0,
                ]
            ];
        }

        return $this->prepareAggregation->prepare(
            $from,
            $to,
            $timezone,
            $group,
            $response
        );
    }
}
