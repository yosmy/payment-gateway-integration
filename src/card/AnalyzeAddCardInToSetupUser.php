<?php

namespace Yosmy\Payment;

use Yosmy\Payment;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.add_card.in',
 *     ]
 * })
 */
class AnalyzeAddCardInToSetupUser implements Payment\AnalyzeAddCardIn
{
    /**
     * @var Payment\SetupUser
     */
    private $setupUser;

    /**
     * @param SetupUser $setupUser
     */
    public function __construct(SetupUser $setupUser)
    {
        $this->setupUser = $setupUser;
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(
        User $user,
        string $number,
        string $name,
        string $month,
        string $year,
        string $cvc,
        string $zip
    ) {
        unset($number, $name, $month, $year, $cvc, $zip);

        $this->setupUser->setup($user);
    }
}