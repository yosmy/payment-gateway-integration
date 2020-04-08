<?php

namespace Yosmy\Payment;

class UnknownException extends Exception
{
    public function __construct()
    {
        parent::__construct('Se produjo con error con tu tarjeta. Intenta más tarde o contacta con tu banco');
    }
}
