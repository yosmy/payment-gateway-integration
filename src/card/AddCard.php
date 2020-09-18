<?php

namespace Yosmy\Payment;

use Yosmy;

/**
 * @di\service()
 */
class AddCard
{
    /**
     * @var GatherCustomer
     */
    private $gatherCustomer;

    /**
     * @var ResolveCard
     */
    private $resolveCard;

    /**
     * @var Card\CreateGid
     */
    private $createGid;

    /**
     * @var Card\AddGid
     */
    private $addGid;

    /**
     * @var AnalyzePreAddCard[]
     */
    private $analyzePreAddCardServices;

    /**
     * @var AnalyzePostAddCardSuccess[]
     */
    private $analyzePostAddCardSuccessServices;

    /**
     * @var AnalyzePostAddCardFail[]
     */
    private $analyzePostAddCardFailServices;

    /**
     * @di\arguments({
     *     analyzePreAddCardServices:         '#yosmy.payment.pre_add_card',
     *     analyzePostAddCardSuccessServices: '#yosmy.payment.post_add_card_success',
     *     analyzePostAddCardFailServices:    '#yosmy.payment.post_add_card_fail'
     * })
     *
     * @param GatherCustomer              $gatherCustomer
     * @param ResolveCard                 $resolveCard
     * @param Card\CreateGid              $createGid
     * @param Card\AddGid                 $addGid
     * @param AnalyzePreAddCard[]         $analyzePreAddCardServices
     * @param AnalyzePostAddCardSuccess[] $analyzePostAddCardSuccessServices
     * @param AnalyzePostAddCardFail[]    $analyzePostAddCardFailServices
     */
    public function __construct(
        GatherCustomer $gatherCustomer,
        ResolveCard $resolveCard,
        Card\CreateGid $createGid,
        Card\AddGid $addGid,
        ?array $analyzePreAddCardServices,
        ?array $analyzePostAddCardSuccessServices,
        ?array $analyzePostAddCardFailServices
    ) {
        $this->gatherCustomer = $gatherCustomer;
        $this->resolveCard = $resolveCard;
        $this->createGid = $createGid;
        $this->addGid = $addGid;
        $this->analyzePreAddCardServices = $analyzePreAddCardServices;
        $this->analyzePostAddCardSuccessServices = $analyzePostAddCardSuccessServices;
        $this->analyzePostAddCardFailServices = $analyzePostAddCardFailServices;
    }

    /**
     * @param Customer $customer
     * @param string       $number
     * @param string       $name
     * @param string       $month
     * @param string       $year
     * @param string       $cvc
     * @param string       $zip
     *
     * @return Card
     *
     * @throws Exception
     */
    public function add(
        Customer $customer,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    ): Card {
        foreach ($this->analyzePreAddCardServices as $analyzePreAddCard) {
            try {
                $analyzePreAddCard->analyze(
                    $customer,
                    $number,
                    $name,
                    $month,
                    $year,
                    $cvc,
                    $zip
                );
            } catch (Exception $e) {
                foreach ($this->analyzePostAddCardFailServices as $analyzePostAddCardFail) {
                    $analyzePostAddCardFail->analyze(
                        $customer,
                        $number,
                        $name,
                        $month,
                        $year,
                        $cvc,
                        $zip,
                        $e
                    );
                }

                throw $e;
            }
        }

        // Gather it again, because pre listeners could changed
        $customer = $this->gatherCustomer->gather($customer->getUser());

        try {
            $gid = $this->createGid->create(
                $customer,
                $number,
                $name,
                $month,
                $year,
                $cvc,
                $zip
            );
        } catch (Exception $e) {
            foreach ($this->analyzePostAddCardFailServices as $analyzePostAddCardFail) {
                $analyzePostAddCardFail->analyze(
                    $customer,
                    $number,
                    $name,
                    $month,
                    $year,
                    $cvc,
                    $zip,
                    $e
                );
            }

            throw $e;
        }

        $card = $this->resolveCard->resolve(
            $customer,
            $number,
            $name,
            $month,
            $year,
            $cvc,
            $zip
        );

        $card = $this->addGid->add(
            $card,
            $customer,
            $gid
        );

        foreach ($this->analyzePostAddCardSuccessServices as $analyzePostAddCardSuccess) {
            $analyzePostAddCardSuccess->analyze(
                $card
            );
        }

        return $card;
    }
}