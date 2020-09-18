<?php

namespace Yosmy\Payment;

interface Customer
{
    /**
     * @return string
     */
    public function getUser(): string;

    /**
     * @return string
     */
    public function getCountry(): string;

    /**
     * @return string
     */
    public function getGateway(): string;

    /**
     * @return Gateway\Gids
     */
    public function getGids(): Gateway\Gids;
}
