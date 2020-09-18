<?php

namespace Yosmy\Payment;

use Yosmy;
use Traversable;

/**
 * @di\service()
 */
class AuditMissingCustomers
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
        return $manageCollection->aggregate(
            [
                [
                    '$lookup' => [
                        'localField' => '_id',
                        'from' => $this->manageCustomerCollection->getName(),
                        'as' => 'customers',
                        'foreignField' => '_id',
                    ]
                ],
                [
                    '$match' => [
                        'customers._id' => [
                            '$exists' => false
                        ]
                    ],
                ]
            ]
        );
    }
}