<?php

namespace Yosmy\Payment;

use Yosmy;
use Traversable;

/**
 * @di\service()
 */
class AuditExtraCharges
{
    /**
     * @var ManageChargeCollection
     */
    private $manageChargeCollection;

    /**
     * @var ManageCustomerCollection
     */
    private $manageCustomerCollection;

    /**
     * @param ManageChargeCollection   $manageChargeCollection
     * @param ManageCustomerCollection $manageCustomerCollection
     */
    public function __construct(
        ManageChargeCollection $manageChargeCollection,
        ManageCustomerCollection $manageCustomerCollection
    ) {
        $this->manageChargeCollection = $manageChargeCollection;
        $this->manageCustomerCollection = $manageCustomerCollection;
    }

    /**
     * @return Traversable
     */
    public function audit(): Traversable
    {
        return $this->manageChargeCollection->aggregate(
            [
                [
                    '$lookup' => [
                        'localField' => 'user',
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