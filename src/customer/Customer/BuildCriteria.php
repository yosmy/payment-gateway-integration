<?php

namespace Yosmy\Payment\Customer;

/**
 * @di\service({
 *     private: true
 * })
 */
class BuildCriteria
{
    /**
     * @param string[] $ids
     *
     * @return array
     */
    public function build(
        ?array $ids
    ): array {
        $criteria = [];

        if ($ids) {
            $criteria['_id'] = ['$in' => $ids];
        }

        return $criteria;
    }
}
