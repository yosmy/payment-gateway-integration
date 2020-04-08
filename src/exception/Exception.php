<?php

namespace Yosmy\Payment;

use Exception as BaseException;
use JsonSerializable;

abstract class Exception extends BaseException implements JsonSerializable
{
    public function __construct(
        string $message
    ) {
        parent::__construct($message);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'message' => $this->message
        ];
    }
}
