<?php

namespace Yosmy\Payment;

use Yosmy\Mongo;
use Yosmy\Payment\Gateway;

class Charge extends Mongo\Document
{
    /**
     * @param string      $id
     * @param string      $user
     * @param string      $card
     * @param int         $amount
     * @param Gateway\Gid $gid
     * @param int         $date
     */
    public function __construct(
        string $id,
        string $user,
        string $card,
        int $amount,
        Gateway\Gid $gid,
        int $date
    ) {
        parent::__construct([
            '_id' => $id,
            'user' => $user,
            'card' => $card,
            'amount' => $amount,
            'gid' => $gid,
            'date' => $date,
        ]);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->offsetGet('_id');
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->offsetGet('user');
    }

    /**
     * @return string
     */
    public function getCard(): string
    {
        return $this->offsetGet('card');
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->offsetGet('amount');
    }

    /**
     * @return Gateway\Gid
     */
    public function getGid(): Gateway\Gid
    {
        return $this->offsetGet('gid');
    }

    /**
     * @return int
     */
    public function getDate(): int
    {
        return $this->offsetGet('date');
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize(): object
    {
        /** @var Gateway\Gid $gid */
        $gid = $this->gid;

        $date = new Mongo\DateTime($this->date * 1000);

        $data = $this->getArrayCopy();

        $data['gid'] = $gid->jsonSerialize();

        $data['date'] = $date;

        return (object) $data;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $data['gid'] = new Gateway\Gid(
            $data['gid']->id,
            $data['gid']->gateway
        );

        /** @var Mongo\DateTime $date */
        $date = $data['date'];
        $data['date'] = $date->toDateTime()->getTimestamp();

        parent::bsonUnserialize($data);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): object
    {
        $data = parent::jsonSerialize();

        $data->id = $data->_id;

        unset($data->_id);

        $data->gid = $this->getGid()->jsonSerialize();

        return $data;
    }
}
