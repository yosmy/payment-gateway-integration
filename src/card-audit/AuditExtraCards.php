<?php

namespace Yosmy\Payment;

use Yosmy;
use Traversable;

/**
 * @di\service()
 */
class AuditExtraCards
{
    /**
     * @var ManageCardCollection
     */
    private $manageCardCollection;

    /**
     * @var ManageCustomerCollection
     */
    private $manageCustomerCollection;

    /**
     * @param ManageCardCollection $manageCardCollection
     * @param ManageCustomerCollection $manageCustomerCollection
     */
    public function __construct(
        ManageCardCollection $manageCardCollection,
        ManageCustomerCollection $manageCustomerCollection
    ) {
        $this->manageCardCollection = $manageCardCollection;
        $this->manageCustomerCollection = $manageCustomerCollection;
    }

    /**
     * @return Traversable
     */
    public function audit(): Traversable
    {
        return $this->manageCardCollection->aggregate(
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