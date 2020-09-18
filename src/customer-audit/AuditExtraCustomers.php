<?php

namespace Yosmy\Payment;

use Yosmy;
use Traversable;

/**
 * @di\service()
 */
class AuditExtraCustomers
{
    /**
     * @var ManageCustomerCollection
     */
    private $manageCustomerCollection;

    /**
     * @param ManageCustomerCollection $manageCustomerCollection
     */
    public function __construct(
        ManageCustomerCollection $manageCustomerCollection
    ) {
        $this->manageCustomerCollection = $manageCustomerCollection;
    }

    /**
     * @param Yosmy\Mongo\ManageCollection $manageCollection
     *
     * @return Traversable
     */
    public function audit(
        Yosmy\Mongo\ManageCollection $manageCollection
    ): Traversable
    {
        return $this->manageCustomerCollection->aggregate(
            [
                [
                    '$lookup' => [
                        'localField' => '_id',
                        'from' => $manageCollection->getName(),
                        'as' => 'parent',
                        'foreignField' => '_id',
                    ]
                ],
                [
                    '$match' => [
                        'parent._id' => [
                            '$exists' => false
                        ]
                    ],
                ]
            ]
        );
    }
}