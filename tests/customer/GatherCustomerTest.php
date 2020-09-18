<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;

class GatherCustomerTest extends TestCase
{
    public function testGather()
    {
        $user = 'user-1';
        $customer = new Payment\BaseCustomer(
            $user,
            'country-1',
            'gateway-1',
            new Payment\Gateway\Gids([])
        );

        $manageCollection = $this->createMock(Payment\ManageCustomerCollection::class);

        $manageCollection->expects($this->once())
            ->method('findOne')
            ->with($this->equalTo([
                '_id' => $user
            ]))
            ->willReturn($customer);

        $gatherCustomer = new Payment\GatherCustomer(
            $manageCollection
        );

        $actualCustomer = $gatherCustomer->gather($user);

        $this->assertEquals(
            $customer,
            $actualCustomer
        );
    }
}