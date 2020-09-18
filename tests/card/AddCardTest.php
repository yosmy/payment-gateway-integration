<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;
use LogicException;

class AddCardTest extends TestCase
{
    public function testAdd()
    {
        $customer = new Payment\BaseCustomer(
            'id-1',
            'country-1',
            'gateway-1',
            new Payment\Gateway\Gids([])
        );
        $number = '1111111111111111';
        $name = 'Foo Bar';
        $month = '1';
        $year = '22';
        $cvc = '123';
        $zip = '12345';

        $gid = 'gid-1';

        $analyzePreAddCard = $this->createMock(Payment\AnalyzePreAddCard::class);

        $analyzePreAddCard->expects($this->once())
            ->method('analyze')
            ->with(
                $customer,
                $number,
                $name,
                $month,
                $year,
                $cvc,
                $zip
            );

        $gatherCustomer = $this->createMock(Payment\GatherCustomer::class);

        $gatherCustomer->expects($this->once())
            ->method('gather')
            ->with($customer->getUser())
            ->willReturn($customer);

        $createGid = $this->createMock(Payment\Card\CreateGid::class);

        $createGid->expects($this->once())
            ->method('create')
            ->with(
                $customer,
                $number,
                $name,
                $month,
                $year,
                $cvc,
                $zip
            )
            ->willReturn($gid);

        $resolveCard = $this->createMock(Payment\ResolveCard::class);

        $card = new Payment\Card(
            'id-1',
            'id-1',
            '1111',
            'qwer',
            new Payment\Gateway\Gids([]),
            []
        );

        $resolveCard->expects($this->once())
            ->method('resolve')
            ->with(
                $customer,
                $number,
                $name,
                $month,
                $year,
                $cvc,
                $zip
            )
            ->willReturn($card);

        $addGid = $this->createMock(Payment\Card\AddGid::class);

        $cardWithGid = new Payment\Card(
            'id-1',
            'id-1',
            '1111',
            'qwer',
            new Payment\Gateway\Gids([
                new Payment\Gateway\Gid(
                    $gid,
                    $customer->getGateway()
                )
            ]),
            []
        );

        $addGid->expects($this->once())
            ->method('add')
            ->with(
                $card,
                $customer,
                $gid
            )
            ->willReturn($cardWithGid);

        $card = $cardWithGid;

        $analyzePostAddCardSuccess = $this->createMock(Payment\AnalyzePostAddCardSuccess::class);

        $analyzePostAddCardSuccess->expects($this->once())
            ->method('analyze')
            ->with(
                $card
            );

        $addCard = new Payment\AddCard(
            $gatherCustomer,
            $resolveCard,
            $createGid,
            $addGid,
            [$analyzePreAddCard],
            [$analyzePostAddCardSuccess],
            []
        );

        try {
            $actualCard = $addCard->add(
                $customer,
                $number,
                $name,
                $month,
                $year,
                $cvc,
                $zip
            );
        } catch (Payment\Exception $e) {
            throw new LogicException();
        }

        $this->assertEquals(
            $card,
            $actualCard
        );
    }

    /**
     * @throws Payment\Exception
     */
    public function testAddHavingExceptionOnPreAdd()
    {
        $customer = new Payment\BaseCustomer(
            'id-1',
            'country-1',
            'gateway-1',
            new Payment\Gateway\Gids([])
        );
        $number = '1111111111111111';
        $name = 'Foo Bar';
        $month = '1';
        $year = '22';
        $cvc = '123';
        $zip = '12345';

        $analyzePreAddCard = $this->createMock(Payment\AnalyzePreAddCard::class);

        $e = new Payment\KnownException('error-message');

        $analyzePreAddCard->expects($this->once())
            ->method('analyze')
            ->willThrowException($e);

        $gatherCustomer = $this->createMock(Payment\GatherCustomer::class);

        $createGid = $this->createMock(Payment\Card\CreateGid::class);

        $resolveCard = $this->createMock(Payment\ResolveCard::class);

        $addGid = $this->createMock(Payment\Card\AddGid::class);

        $analyzePostAddCardFail = $this->createMock(Payment\AnalyzePostAddCardFail::class);

        $analyzePostAddCardFail->expects($this->once())
            ->method('analyze')
            ->with(
                $customer,
                $number,
                $name,
                $month,
                $year,
                $cvc,
                $zip,
                $e
            );

        $addCard = new Payment\AddCard(
            $gatherCustomer,
            $resolveCard,
            $createGid,
            $addGid,
            [$analyzePreAddCard],
            [],
            [$analyzePostAddCardFail]
        );

        $this->expectExceptionObject($e);

        $addCard->add(
            $customer,
            $number,
            $name,
            $month,
            $year,
            $cvc,
            $zip
        );
    }

    /**
     * @throws Payment\Exception
     */
    public function testAddHavingExceptionOnCreateGid()
    {
        $customer = new Payment\BaseCustomer(
            'id-1',
            'country-1',
            'gateway-1',
            new Payment\Gateway\Gids([])
        );
        $number = '1111111111111111';
        $name = 'Foo Bar';
        $month = '1';
        $year = '22';
        $cvc = '123';
        $zip = '12345';

        $gatherCustomer = $this->createMock(Payment\GatherCustomer::class);

        $gatherCustomer->expects($this->once())
            ->method('gather')
            ->with($customer->getUser())
            ->willReturn($customer);

        $createGid = $this->createMock(Payment\Card\CreateGid::class);

        $e = new Payment\KnownException('error-message');

        $createGid->expects($this->once())
            ->method('create')
            ->willThrowException($e);

        $resolveCard = $this->createMock(Payment\ResolveCard::class);

        $addGid = $this->createMock(Payment\Card\AddGid::class);

        $analyzePostAddCardFail = $this->createMock(Payment\AnalyzePostAddCardFail::class);

        $analyzePostAddCardFail->expects($this->once())
            ->method('analyze')
            ->with(
                $customer,
                $number,
                $name,
                $month,
                $year,
                $cvc,
                $zip,
                $e
            );

        $addCard = new Payment\AddCard(
            $gatherCustomer,
            $resolveCard,
            $createGid,
            $addGid,
            [],
            [],
            [$analyzePostAddCardFail]
        );

        $this->expectExceptionObject($e);

        $addCard->add(
            $customer,
            $number,
            $name,
            $month,
            $year,
            $cvc,
            $zip
        );
    }
}