<?php

namespace Yosmy\Payment\User;

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
    ) {
        $criteria = [];

        if ($ids) {
            $criteria['_id'] = ['$in' => $ids];
        }

        return $criteria;
    }
}
