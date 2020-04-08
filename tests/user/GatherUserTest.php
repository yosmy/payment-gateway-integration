<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;

class GatherUserTest extends TestCase
{
    public function testGather()
    {
        $id = 'id-1';
        $user = new Payment\User($id, 'country-1', 'gateway-1', new Payment\Gateway\Gids([]));

        $manageCollection = $this->createMock(Payment\ManageUserCollection::class);

        $manageCollection->expects($this->once())
            ->method('findOne')
            ->with($this->equalTo([
                '_id' => $id
            ]))
            ->willReturn($user);

        $gatherUser = new Payment\GatherUser(
            $manageCollection
        );

        $actualUser = $gatherUser->gather($id);

        $this->assertEquals(
            $user,
            $actualUser
        );
    }
}