<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;

class BaseCustomerTest extends TestCase
{
    public function testGetters()
    {
        $user = 'user-1';
        $country = 'country-1';
        $gateway = 'gateway-1';
        $gids = new Payment\Gateway\Gids([
            new Payment\Gateway\Gid(
                'gid-1',
                'gateway-1'
            ),
            new Payment\Gateway\Gid(
                'gid-2',
                'gateway-2'
            )
        ]);

        $customer = new Payment\BaseCustomer(
            $user,
            $country,
            $gateway,
            $gids
        );

        $this->assertEquals(
            $user,
            $customer->getUser()
        );

        $this->assertEquals(
            $country,
            $customer->getCountry()
        );

        $this->assertEquals(
            $gateway,
            $customer->getGateway()
        );

        $this->assertEquals(
            $gids,
            $customer->getGids()
        );
    }

    public function testBson()
    {
        $user = 'user-1';
        $gateway = 'gateway-1';
        $gid1 = [
            'id' => 'gid-1',
            'gateway' => 'gateway-1'
        ];
        $gid2 = [
            'id' => 'gid-2',
            'gateway' => 'gateway-2'
        ];

        $customer = new Payment\BaseCustomer(
            '',
            '',
            '',
            new Payment\Gateway\Gids([])
        );

        $customer->bsonUnserialize([
            '_id' => $user,
            'gateway' => $gateway,
            'gids' => [
                (object) $gid1,
                (object) $gid2
            ]
        ]);

        $this->assertEquals(
            [
                'user' => $user,
                'gateway' => $gateway,
                'gids' => [
                    $gid1,
                    $gid2
                ]
            ],
            $customer->bsonSerialize()
        );
    }
}