<?php

namespace Yosmy\Payment;

use Yosmy\Mongo;
use Yosmy\Payment\Gateway;

/**
 * @di\service()
 */
class ExecuteCharge
{
    /**
     * @var GatherCard
     */
    private $gatherCard;

    /**
     * @var GatherCustomer
     */
    private $gatherCustomer;

    /**
     * @var Gateway\Charge\Execute\SelectGateway
     */
    private $selectGateway;

    /**
     * @var ProcessGatewayException[]
     */
    private $processGatewayExceptionServices;
    
    /**
     * @var ManageChargeCollection
     */
    private $manageCollection;

    /**
     * @var AnalyzePreExecuteCharge[]
     */
    private $analyzePreExecuteChargeServices;

    /**
     * @var AnalyzePostExecuteChargeSuccess[]
     */
    private $analyzePostExecuteChargeSuccessServices;

    /**
     * @var AnalyzePostExecuteChargeFail[]
     */
    private $analyzePostExecuteChargeFailServices;

    /**
     * @di\arguments({
     *     processGatewayExceptionServices:         '#yosmy.payment.gateway_exception_throwed',
     *     analyzePreExecuteChargeServices:         '#yosmy.payment.pre_execute_charge',
     *     analyzePostExecuteChargeSuccessServices: '#yosmy.payment.post_execute_charge_success',
     *     analyzePostExecuteChargeFailServices:    '#yosmy.payment.post_execute_charge_fail'
     * })
     *
     * @param GatherCard                           $gatherCard
     * @param GatherCustomer                       $gatherCustomer
     * @param Gateway\Charge\Execute\SelectGateway $selectGateway
     * @param ProcessGatewayException[]            $processGatewayExceptionServices
     * @param ManageChargeCollection               $manageCollection
     * @param AnalyzePreExecuteCharge[]            $analyzePreExecuteChargeServices
     * @param AnalyzePostExecuteChargeSuccess[]    $analyzePostExecuteChargeSuccessServices
     * @param AnalyzePostExecuteChargeFail[]       $analyzePostExecuteChargeFailServices
     */
    public function __construct(
        GatherCard $gatherCard,
        GatherCustomer $gatherCustomer,
        Gateway\Charge\Execute\SelectGateway $selectGateway,
        array $processGatewayExceptionServices,
        ManageChargeCollection $manageCollection,
        ?array $analyzePreExecuteChargeServices,
        ?array $analyzePostExecuteChargeSuccessServices,
        ?array $analyzePostExecuteChargeFailServices
    ) {
        $this->gatherCard = $gatherCard;
        $this->gatherCustomer = $gatherCustomer;
        $this->selectGateway = $selectGateway;
        $this->processGatewayExceptionServices = $processGatewayExceptionServices;
        $this->manageCollection = $manageCollection;
        $this->analyzePreExecuteChargeServices = $analyzePreExecuteChargeServices;
        $this->analyzePostExecuteChargeSuccessServices = $analyzePostExecuteChargeSuccessServices;
        $this->analyzePostExecuteChargeFailServices = $analyzePostExecuteChargeFailServices;
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
    ): Charge {
        foreach ($this->analyzePreExecuteChargeServices as $analyzePreExecuteCharge) {
            try {
                $analyzePreExecuteCharge->analyze(
                    $card,
                    $amount,
                    $description,
                    $statement
                );
            } catch (Exception $e) {
                foreach ($this->analyzePostExecuteChargeFailServices as $analyzePostExecuteChargeFail) {
                    $analyzePostExecuteChargeFail->analyze(
                        $card,
                        $amount,
                        $e
                    );
                }

                throw $e;
            }
        }

        // Gather it again, because pre listeners could changed
        $card = $this->gatherCard->gather(
            $card->getId(),
            null,
            null
        );

        $customer = $this->gatherCustomer->gather(
            $card->getUser()
        );

        $gateway = $customer->getGateway();

        try {
            $charge = $this->selectGateway->select($gateway)->execute(
                $customer->getGids()->get($gateway),
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
            $exception = new UnknownException();

            foreach ($this->processGatewayExceptionServices as $processException) {
                try {
                    $processException->process(
                        $e,
                        $customer
                    );
                } catch (Exception $e) {
                    $exception = $e;
                }
            }

            foreach ($this->analyzePostExecuteChargeFailServices as $analyzePostExecuteChargeFail) {
                $analyzePostExecuteChargeFail->analyze(
                    $card,
                    $amount,
                    $exception
                );
            }
            
            throw $exception;
        }

        $id = uniqid();

        $this->manageCollection->insertOne([
            '_id' => $id,
            'user' => $customer->getUser(),
            'card' => $card->getId(),
            'amount' => $amount,
            'gid' => [
                'id' => $charge->getId(),
                'gateway' => $gateway
            ],
            'date' => new Mongo\DateTime($charge->getDate() * 1000)
        ]);

        $charge = new Charge(
            $id,
            $customer->getUser(),
            $card->getId(),
            $amount,
            new Gateway\Gid(
                $charge->getId(),
                $gateway
            ),
            $charge->getDate()
        );

        foreach ($this->analyzePostExecuteChargeSuccessServices as $analyzePreExecuteChargeSuccess) {
            $analyzePreExecuteChargeSuccess->analyze(
                $charge
            );
        }

        return $charge;
    }
}