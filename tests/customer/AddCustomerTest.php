<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;

class AddCustomerTest extends TestCase
{
    public function testAdd()
    {
        $user = 'user-1';
        $country = 'country-1';
        $gateway = 'gateway-1';
        $gids = [];

        $manageCollection = $this->createMock(Payment\ManageCustomerCollection::class);

        $manageCollection->expects($this->once())
            ->method('insertOne')
            ->with($this->equalTo([
                '_id' => $user,
                'country' => $country,
                'gateway' => $gateway,
                'gids' => $gids
            ]));

        $addCustomer = new Payment\AddCustomer($manageCollection);

        $customer = $addCustomer->add(
            $user,
            $country,
            $gateway
        );

        $this->assertEquals(
            new Payment\BaseCustomer(
                $user,
                $country,
                $gateway,
                new Payment\Gateway\Gids($gids)
            ),
            $customer
        );
    }
}