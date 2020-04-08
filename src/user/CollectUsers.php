<?php

namespace Yosmy\Payment;

/**
 * @di\service()
 */
class CollectUsers
{
    /**
     * @var User\BuildCriteria
     */
    private $buildCriteria;

    /**
     * @var ManageUserCollection
     */
    private $manageCollection;

    /**
     * @param User\BuildCriteria   $buildCriteria
     * @param ManageUserCollection $manageCollection
     */
    public function __construct(
        User\BuildCriteria $buildCriteria,
        ManageUserCollection $manageCollection
    ) {
        $this->buildCriteria = $buildCriteria;
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string[] $ids
     *
     * @return Users
     */
    public function collect(
        ?array $ids
    ) {
        $criteria = $this->buildCriteria->build($ids);

        $cursor = $this->manageCollection->find($criteria);

        return new Users($cursor);
    }
}
