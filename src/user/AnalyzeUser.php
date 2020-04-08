<?php

namespace Yosmy\Payment;

interface AnalyzeUser
{
    /**
     * @param User $user
     *
     * @throws Exception
     */
    public function analyze(
        User $user
    );
}