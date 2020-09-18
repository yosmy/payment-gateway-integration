<?php

namespace Yosmy\Payment;

use Yosmy;
use LogicException;

/**
 * @di\service()
 */
class RefundCharge
{
    /**
     * @var Gateway\Charge\Refund\SelectGateway
     */
    private $selectGateway;

    /**
     * @var AnalyzePostRefundChargeSuccess[]
     */
    private $analyzePostRefundChargeSuccessServices;

    /**
     * @di\arguments({
     *     analyzePostRefundChargeSuccessServices: '#yosmy.payment.post_refund_charge_success',
     * })
     *
     * @param Gateway\Charge\Refund\SelectGateway $selectGateway
     * @param AnalyzePostRefundChargeSuccess[]    $analyzePostRefundChargeSuccessServices
     */
    public function __construct(
        Gateway\Charge\Refund\SelectGateway $selectGateway,
        array $analyzePostRefundChargeSuccessServices
    ) {
        $this->selectGateway = $selectGateway;
        $this->analyzePostRefundChargeSuccessServices = $analyzePostRefundChargeSuccessServices;
    }

    /**
     * @param Charge   $charge
     * @param int|null $amount
     */
    public function refund(
        Charge $charge,
        ?int $amount
    ) {
        try {
            $this->selectGateway->select($charge->getGid()->getGateway())->refund(
                $charge->getGid()->getId(),
                $amount ?: $charge->getAmount()
            );
        } catch (Gateway\UnknownException $e) {
            throw new LogicException(null, null, $e);
        }

        foreach ($this->analyzePostRefundChargeSuccessServices as $analyzePostRefundChargeSuccess) {
            $analyzePostRefundChargeSuccess->analyze(
                $charge
            );
        }
    }
}