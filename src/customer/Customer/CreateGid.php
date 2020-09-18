<?php

namespace Yosmy\Payment\Customer;

use Yosmy\Payment;

/**
 * @di\service()
 */
class CreateGid
{
    /**
     * @var Payment\Gateway\Customer\Add\SelectGateway
     */
    private $selectGateway;

    /**
     * @param Payment\Gateway\Customer\Add\SelectGateway $selectGateway
     */
    public function __construct(
        Payment\Gateway\Customer\Add\SelectGateway $selectGateway
    ) {
        $this->selectGateway = $selectGateway;
    }

    /**
     * @param string $gateway
     *
     * @return string The gid
     *
     * @throws Payment\Exception
     */
    public function create(
        string $gateway
    ): string {
        try {
            $gid = $this->selectGateway
                ->select($gateway)
                ->add()
                ->getId();
        } catch (Payment\Gateway\UnknownException $e) {
            throw new Payment\UnknownException();
        }

        return $gid;
    }
}