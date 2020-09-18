<?php

namespace Yosmy\Payment;

use Yosmy\Mongo;

class Customers extends Mongo\Collection
{
    /**
     * @var Customer[]
     */
    protected $cursor;
}

