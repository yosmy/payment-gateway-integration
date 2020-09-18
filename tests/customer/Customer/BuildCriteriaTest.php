<?php

namespace Yosmy\Payment\Test\Customer;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;

class BuildCriteriaTest extends TestCase
{
    public function testBuild()
    {
        $ids = ['id-1'];

        $buildCriteria = new Payment\Customer\BuildCriteria();

        $criteria = $buildCriteria->build($ids);

        $this->assertEquals(
            [
               '_id' => ['$in' => $ids]
            ],
            $criteria
        );
    }

    public function testBuildWithNoIds()
    {
        $ids = null;

        $buildCriteria = new Payment\Customer\BuildCriteria();

        $criteria = $buildCriteria->build($ids);

        $this->assertEquals(
            [],
            $criteria
        );
    }
}