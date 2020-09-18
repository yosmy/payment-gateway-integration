<?php

namespace Yosmy\Payment;

use Yosmy\Mongo;

class Charges extends Mongo\Collection
{
    /**
     * @var Charge[]
     */
    protected $cursor;
}

