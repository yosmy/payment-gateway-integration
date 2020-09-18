<?php

namespace Yosmy\Payment;

/**
 * @di\service()
 */
class CollectCustomers
{
    /**
     * @var Customer\BuildCriteria
     */
    private $buildCriteria;

    /**
     * @var ManageCustomerCollection
     */
    private $manageCollection;

    /**
     * @param Customer\BuildCriteria       $buildCriteria
     * @param ManageCustomerCollection $manageCollection
     */
    public function __construct(
        Customer\BuildCriteria $buildCriteria,
        ManageCustomerCollection $manageCollection
    ) {
        $this->buildCriteria = $buildCriteria;
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string[] $users
     *
     * @return Customers
     */
    public function collect(
        ?array $users
    ): Customers {
        $criteria = $this->buildCriteria->build($users);

        $cursor = $this->manageCollection->find($criteria);

        return new Customers($cursor);
    }
}
