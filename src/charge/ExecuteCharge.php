<?php

namespace Yosmy\Payment;

use MongoDB\BSON\UTCDateTime;
use Yosmy\Payment\Gateway;

/**
 * @di\service()
 */
class ExecuteCharge
{
    /**
     * @var GatherUser
     */
    private $gatherUser;

    /**
     * @var Gateway\Charge\Execute\SelectGateway
     */
    private $selectGateway;

    /**
     * @var ManageChargeCollection
     */
    private $manageCollection;

    /**
     * @var AnalyzeExecuteChargeIn[]
     */
    private $analyzeExecuteChargeInServices;

    /**
     * @var AnalyzeExecuteChargeSuccessedOut[]
     */
    private $analyzeExecuteChargeSuccessedOutService;

    /**
     * @var AnalyzeExecuteChargeFailedOut[]
     */
    private $analyzeExecuteChargeFailedOutServices;

    /**
     * @di\arguments({
     *     analyzeExecuteChargeInServices:           '#yosmy.payment.execute_charge.in',
     *     analyzeExecuteChargeSuccessedOutServices: '#yosmy.payment.execute_charge.successed_out',
     *     analyzeExecuteChargeFailedOutServices:    '#yosmy.payment.execute_charge.failed_out'
     * })
     *
     * @param GatherUser                           $gatherUser
     * @param Gateway\Charge\Execute\SelectGateway $selectGateway
     * @param ManageChargeCollection               $manageCollection
     * @param AnalyzeExecuteChargeIn[]             $analyzeExecuteChargeInServices
     * @param AnalyzeExecuteChargeSuccessedOut[]   $analyzeExecuteChargeSuccessedOutServices
     * @param AnalyzeExecuteChargeFailedOut[]      $analyzeExecuteChargeFailedOutServices
     */
    public function __construct(
        GatherUser $gatherUser,
        Gateway\Charge\Execute\SelectGateway $selectGateway,
        ManageChargeCollection $manageCollection,
        ?array $analyzeExecuteChargeInServices,
        ?array $analyzeExecuteChargeSuccessedOutServices,
        ?array $analyzeExecuteChargeFailedOutServices
    ) {
        $this->gatherUser = $gatherUser;
        $this->selectGateway = $selectGateway;
        $this->manageCollection = $manageCollection;
        $this->analyzeExecuteChargeInServices = $analyzeExecuteChargeInServices;
        $this->analyzeExecuteChargeSuccessedOutService = $analyzeExecuteChargeSuccessedOutServices;
        $this->analyzeExecuteChargeFailedOutServices = $analyzeExecuteChargeFailedOutServices;
    }

    /**
     * @param Card   $card
     * @param int    $amount      In cents
     * @param string $description
     * @param string $statement
     *
     * @return Charge
     *
     * @throws Exception
     */
    public function execute(
        Card $card,
        int $amount,
        string $description,
        string $statement
    ) {
        foreach ($this->analyzeExecuteChargeInServices as $analyzeExecuteChargeIn) {
            try {
                $analyzeExecuteChargeIn->analyze(
                    $card,
                    $amount,
                    $description,
                    $statement
                );
            } catch (Exception $e) {
                throw $e;
            }
        }

        $user = $this->gatherUser->gather(
            $card->getUser()
        );

        $gateway = $user->getGateway();

        try {
            $charge = $this->selectGateway->select($gateway)->execute(
                $user->getGids()->get($gateway),
                $card->getGids()->get($gateway),
                $amount,
                $description,
                $statement
            );
        } catch (
            Gateway\FieldException
            |Gateway\FundsException
            |Gateway\IssuerException
            |Gateway\RiskException
            |Gateway\FraudException
            |Gateway\UnknownException $e
        ) {
            $exception = null;

            foreach ($this->analyzeExecuteChargeFailedOutServices as $analyzeExecuteChargeFailedOut) {
                try {
                    $analyzeExecuteChargeFailedOut->analyze(
                        $card,
                        $amount,
                        $e
                    );
                } catch (Exception $e) {
                    $exception = $e;

                    break;
                }
            }

            if (!$exception) {
                $exception = new UnknownException();
            }

            throw $exception;
        }

        $id = uniqid();

        $this->manageCollection->insertOne([
            '_id' => $id,
            'user' => $user->getId(),
            'card' => $card->getId(),
            'amount' => $amount,
            'gid' => [
                'id' => $charge->getId(),
                'gateway' => $gateway
            ],
            'date' => new UTCDateTime($charge->getDate() * 1000)
        ]);

        $charge = new Charge(
            $id,
            $user->getId(),
            $card->getId(),
            $amount,
            new Gateway\Gid(
                $charge->getId(),
                $gateway
            ),
            $charge->getDate()
        );

        foreach ($this->analyzeExecuteChargeSuccessedOutService as $analyzeExecuteChargeSuccessedOut) {
            $analyzeExecuteChargeSuccessedOut->analyze(
                $card,
                $amount,
                $charge
            );
        }

        return $charge;
    }
}