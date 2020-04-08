<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use LogicException;
use Yosmy\Payment;

class SetupUserTest extends TestCase
{
    public function testSetupHavingGateway()
    {
        $gateway = 'gateway-1';

        $user = $this->createMock(Payment\User::class);

        $user->expects($this->once())
            ->method('getGateway')
            ->willReturn($gateway);

        $gids = $this->createMock(Payment\Gateway\Gids::class);

        $gids->expects($this->once())
            ->method('has')
            ->with($this->equalTo($gateway))
            ->willReturn(true);

        $user->expects($this->once())
            ->method('getGids')
            ->willReturn($gids);

        $createGid = $this->createMock(Payment\User\CreateGid::class);

        $manageCollection = $this->createMock(Payment\ManageUserCollection::class);

        $setupUser = new Payment\SetupUser(
            $createGid,
            $manageCollection
        );

        try {
            $setupUser->setup($user);
        } catch (Payment\Exception $e) {
            throw new LogicException();
        }
    }

    public function testSetup()
    {
        $gateway = 'gateway-1';

        $gid = 'gid-1';

        $user = $this->createMock(Payment\User::class);

        $user->expects($this->exactly(3))
            ->method('getGateway')
            ->willReturn($gateway);

        $gids = $this->createMock(Payment\Gateway\Gids::class);

        $gids->expects($this->once())
            ->method('has')
            ->with($this->equalTo($gateway))
            ->willReturn(false);

        $user->expects($this->once())
            ->method('getGids')
            ->willReturn($gids);

        $createGid = $this->createMock(Payment\User\CreateGid::class);

        $createGid->expects($this->once())
            ->method('create')
            ->with($this->equalTo($gateway))
            ->willReturn($gid);

        $manageCollection = $this->createMock(Payment\ManageUserCollection::class);

        $manageCollection->expects($this->once())
            ->method('updateOne')
            ->with(
                $this->equalTo(
                    [
                        '_id' => $user->getId()
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

        $setupUser = new Payment\SetupUser(
            $createGid,
            $manageCollection
        );

        try {
            $setupUser->setup($user);
        } catch (Payment\Exception $e) {
            throw new LogicException();
        }
    }

    /**
     * @throws Payment\Exception
     */
    public function testSetupHavingProblemsWithGateway()
    {
        $gateway = 'gateway-1';

        $user = $this->createMock(Payment\User::class);

        $user->expects($this->exactly(2))
            ->method('getGateway')
            ->willReturn($gateway);

        $gids = $this->createMock(Payment\Gateway\Gids::class);

        $gids->expects($this->once())
            ->method('has')
            ->with($this->equalTo($gateway))
            ->willReturn(false);

        $user->expects($this->once())
            ->method('getGids')
            ->willReturn($gids);

        $createGid = $this->createMock(Payment\User\CreateGid::class);

        $exception = $this->createMock(Payment\Exception::class);

        $createGid->expects($this->once())
            ->method('create')
            ->willThrowException($exception);

        $manageCollection = $this->createMock(Payment\ManageUserCollection::class);

        $setupUser = new Payment\SetupUser(
            $createGid,
            $manageCollection
        );

        $this->expectException(Payment\Exception::class);

        $setupUser->setup($user);
    }
}