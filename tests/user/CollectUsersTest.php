<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;
use Traversable;

class CollectUsersTest extends TestCase
{
    public function testCollect()
    {
        $ids = ['id-1', 'id-2'];
        $criteria = [];
        $cursor = $this->createMock(Traversable::class);

        $buildCriteria = $this->createMock(Payment\User\BuildCriteria::class);

        $buildCriteria->expects($this->once())
            ->method('build')
            ->with($this->equalTo($ids))
            ->willReturn($criteria);

        $manageCollection = $this->createMock(Payment\ManageUserCollection::class);

        $manageCollection->expects($this->once())
            ->method('find')
            ->with($this->equalTo($criteria))
            ->willReturn($cursor);

        $collectUsers = new Payment\CollectUsers(
            $buildCriteria,
            $manageCollection
        );

        $users = $collectUsers->collect($ids);

        $this->assertEquals(
            new Payment\Users($cursor),
            $users
        );
    }
}