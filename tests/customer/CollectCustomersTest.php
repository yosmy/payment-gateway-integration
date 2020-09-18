<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;
use Traversable;

class CollectCustomersTest extends TestCase
{
    public function testCollect()
    {
        $users = ['user-1', 'user-2'];
        $criteria = [];
        $cursor = $this->createMock(Traversable::class);

        $buildCriteria = $this->createMock(Payment\Customer\BuildCriteria::class);

        $buildCriteria->expects($this->once())
            ->method('build')
            ->with($this->equalTo($users))
            ->willReturn($criteria);

        $manageCollection = $this->createMock(Payment\ManageCustomerCollection::class);

        $manageCollection->expects($this->once())
            ->method('find')
            ->with($this->equalTo($criteria))
            ->willReturn($cursor);

        $collectCustomers = new Payment\CollectCustomers(
            $buildCriteria,
            $manageCollection
        );

        $customers = $collectCustomers->collect($users);

        $this->assertEquals(
            new Payment\Customers($cursor),
            $customers
        );
    }
}