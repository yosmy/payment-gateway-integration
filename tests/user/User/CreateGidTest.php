<?php

namespace Yosmy\Payment\Test\User;

use PHPUnit\Framework\TestCase;
use LogicException;
use Yosmy\Payment;

class CreateGidTest extends TestCase
{
    public function testSetup()
    {
        $gid = 'gid-1';
        $gateway = 'gateway-1';

        $customer = $this->createMock(Payment\Gateway\Customer::class);

        $customer->expects($this->once())
            ->method('getId')
            ->willReturn($gid);

        $addCustomer = $this->createMock(Payment\Gateway\AddCustomer::class);

        $addCustomer->expects($this->once())
            ->method('add')
            ->willReturn($customer);

        $selectGateway = $this->createMock(Payment\Gateway\Customer\Add\SelectGateway::class);

        $selectGateway->expects($this->once())
            ->method('select')
            ->with($this->equalTo($gateway))
            ->willReturn($addCustomer);

        $createGid = new Payment\User\CreateGid(
            $selectGateway
        );

        try {
            $actualGid = $createGid->create($gateway);
        } catch (Payment\Exception $e) {
            throw new LogicException();
        }

        $this->assertEquals(
            $gid,
            $actualGid
        );
    }

    /**
     * @throws Payment\Exception
     */
    public function testSetupWithGatewayException()
    {
        $gateway = 'gateway-1';

        $addCustomer = $this->createMock(Payment\Gateway\AddCustomer::class);

        $addCustomer->expects($this->once())
            ->method('add')
            ->willThrowException(new Payment\Gateway\UnknownException());

        $selectGateway = $this->createMock(Payment\Gateway\Customer\Add\SelectGateway::class);

        $selectGateway->expects($this->once())
            ->method('select')
            ->with($this->equalTo($gateway))
            ->willReturn($addCustomer);

        $createGid = new Payment\User\CreateGid(
            $selectGateway
        );

        $this->expectException(Payment\UnknownException::class);

        try {
            $createGid->create($gateway);
        } catch (Payment\Exception $e) {
            throw $e;
        }
    }
}