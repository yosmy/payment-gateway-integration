<?php

namespace Yosmy\Payment;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use Yosmy\Payment\Gateway;

class Charge extends BSONDocument
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
            'id' => $id,
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
        return $this->offsetGet('id');
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
    public function bsonSerialize()
    {
        /** @var Gateway\Gid $gid */
        $gid = $this->gid;

        $date = new UTCDateTime($this->date * 1000);

        $data = $this->getArrayCopy();

        $data['_id'] = $data['id'];

        unset($data['id']);

        $data['gid'] = $gid->jsonSerialize();

        $data['date'] = $date;

        return (object) $data;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $data['id'] = $data['_id'];
        unset($data['_id']);

        $data['gid'] = new Gateway\Gid(
            $data['gid']->id,
            $data['gid']->gateway
        );

        /** @var UTCDateTime $date */
        $date = $data['date'];
        $data['date'] = $date->toDateTime()->getTimestamp();

        parent::bsonUnserialize($data);
    }
}
