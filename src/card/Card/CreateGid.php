<?php

namespace Yosmy\Payment\Card;

use Yosmy;
use Yosmy\Payment;
use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     private: true
 * })
 */
class CreateGid
{
    /**
     * @var Gateway\Card\Add\SelectGateway
     */
    private $selectGateway;

    /**
     * @var Payment\ProcessGatewayException[]
     */
    private $processGatewayExceptionServices;

    /**
     * @di\arguments({
     *     processGatewayExceptionServices: '#yosmy.payment.gateway_exception_throwed',
     * })
     *
     * @param Gateway\Card\Add\SelectGateway    $selectGateway
     * @param Payment\ProcessGatewayException[] $processGatewayExceptionServices
     */
    public function __construct(
        Gateway\Card\Add\SelectGateway $selectGateway,
        array $processGatewayExceptionServices
    ) {
        $this->selectGateway = $selectGateway;
        $this->processGatewayExceptionServices = $processGatewayExceptionServices;
    }

    /**
     * @param Payment\Customer $customer
     * @param string               $number
     * @param string               $name
     * @param string               $month
     * @param string               $year
     * @param string               $cvc
     * @param string               $zip
     *
     * @return string The gid
     *
     * @throws Payment\Exception
     */
    public function create(
        Payment\Customer $customer,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    ): string {
        try {
            $gid = $this->selectGateway
                ->select($customer->getGateway())
                ->add(
                    $customer->getGids()->get($customer->getGateway()),
                    $number,
                    $name,
                    $month,
                    $year,
                    $cvc,
                    $zip
                )
                ->getId();
        } catch (
            Gateway\FieldException
            | Gateway\FraudException
            | Gateway\FundsException
            | Gateway\IssuerException
            | Gateway\RiskException
            | Gateway\UnknownException $e
        ) {
            foreach ($this->processGatewayExceptionServices as $processException) {
                try {
                    $processException->process(
                        $e,
                        $customer
                    );
                } catch (Payment\Exception $e) {
                    throw $e;
                }
            }

            throw new Payment\UnknownException();
        }

        return $gid;
    }
}