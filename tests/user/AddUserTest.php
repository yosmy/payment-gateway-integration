<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;

class AddUserTest extends TestCase
{
    public function testAdd()
    {
        $id = 'id-1';
        $country = 'country-1';
        $gateway = 'gateway-1';
        $gids = [];

        $manageCollection = $this->createMock(Payment\ManageUserCollection::class);

        $manageCollection->expects($this->once())
            ->method('insertOne')
            ->with($this->equalTo([
                '_id' => $id,
                'country' => $country,
                'gateway' => $gateway,
                'gids' => $gids
            ]));

        $addUser = new Payment\AddUser($manageCollection);

        $user = $addUser->add($id, $country, $gateway);

        $this->assertEquals(
            new Payment\User(
                $id,
                $country,
                $gateway,
                new Payment\Gateway\Gids($gids)
            ),
            $user
        );
    }
}