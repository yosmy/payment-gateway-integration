<?php

namespace Yosmy\Payment;

use Yosmy;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.execute_charge.in',
 *     ]
 * })
 */
class AnalyzeExecuteChargeInToSetupUser implements AnalyzeExecuteChargeIn
{
    /**
     * @var GatherUser
     */
    private $gatherUser;

    /**
     * @var SetupUser
     */
    private $setupUser;

    /**
     * @param GatherUser  $gatherUser
     * @param SetupUser $setupUser
     */
    public function __construct(
        GatherUser $gatherUser,
        SetupUser $setupUser
    ) {
        $this->gatherUser = $gatherUser;
        $this->setupUser = $setupUser;
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(
        Card $card,
        int $amount,
        string $description,
        string $statement
    ) {
        $user = $this->gatherUser->gather(
            $card->getUser()
        );

        try {
            $this->setupUser->setup($user);
        } catch (Exception $e) {
            throw $e;
        }
    }
}