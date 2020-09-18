<?php

namespace Yosmy\Payment;

use Yosmy\Mongo;

class Cards extends Mongo\Collection
{
    /**
     * @var Card[]
     */
    protected $cursor;
}