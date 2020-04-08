<?php

namespace Yosmy\Payment\Card;

use Yosmy;
use Yosmy\Payment;
use Yosmy\Payment\Gateway;

/**
 * @di\service()
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
    private $processGatewayExceptionListeners;

    /**
     * @di\arguments({
     *     processGatewayExceptionListeners: '#yosmy.payment.card.setup_gid.gateway_exception_throwed',
     * })
     *
     * @param Gateway\Card\Add\SelectGateway    $selectGateway
     * @param Payment\ProcessGatewayException[] $processGatewayExceptionListeners
     */
    public function __construct(
        Gateway\Card\Add\SelectGateway $selectGateway,
        array $processGatewayExceptionListeners
    ) {
        $this->selectGateway = $selectGateway;
        $this->processGatewayExceptionListeners = $processGatewayExceptionListeners;
    }

    /**
     * @param Payment\User $user
     * @param string $number
     * @param string $name
     * @param string $month
     * @param string $year
     * @param string $cvc
     * @param string $zip
     *
     * @return string The gid
     *
     * @throws Payment\Exception
     */
    public function create(
        Payment\User $user,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    ) {
        try {
            $gid = $this->selectGateway
                ->select($user->getGateway())
                ->add(
                    $user->getGids()->get($user->getGateway()),
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
            foreach ($this->processGatewayExceptionListeners as $processException) {
                try {
                    $processException->process(
                        $e,
                        $user
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