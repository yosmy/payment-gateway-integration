<?php

namespace Yosmy\Payment\Test\Customer;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;

class AddGidTest extends TestCase
{
    public function testAddHavingGateway()
    {
        $id = 'id-1';
        $gateway = 'gateway-1';
        $gid = 'gid-1';

        $customer = $this->createMock(Payment\BaseCustomer::class);

        $customer->expects($this->once())
            ->method('getUser')
            ->with()
            ->willReturn($id);

        $customer->expects($this->once())
            ->method('getGateway')
            ->with()
            ->willReturn($gateway);

        $manageCollection = $this->createMock(Payment\ManageCustomerCollection::class);

        $manageCollection->expects($this->once())
            ->method('updateOne')
            ->with(
                $this->equalTo(
                    [
                        '_id' => $id
                    ]
                ),
                $this->equalTo(
                    [
                        '$addToSet' => [
                            'gids' => [
                                'id' => $gid,
                                'gateway' => $gateway
                            ]
                        ]
                    ]
                )
            );

        $addGid = new Payment\Customer\AddGid(
            $manageCollection
        );

        $addGid->add(
            $customer,
            $gid
        );
    }
}