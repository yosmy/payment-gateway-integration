<?php

namespace Yosmy\Payment;

use Exception as BaseException;
use JsonSerializable;

class Exception extends BaseException implements JsonSerializable
{
    public function __construct(
        string $message
    ) {
        parent::__construct($message);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'message' => $this->message
        ];
    }
}
