<?php

namespace Yosmy\Payment;

use MongoDB\Model\BSONDocument;

class User extends BSONDocument
{
    /**
     * @param string       $id
     * @param string       $country
     * @param string       $gateway
     * @param Gateway\Gids $gids
     */
    public function __construct(
        string $id,
        string $country,
        string $gateway,
        Gateway\Gids $gids
    ) {
        parent::__construct([
            'id' => $id,
            'country' => $country,
            'gateway' => $gateway,
            'gids' => $gids,
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
    public function getCountry(): string
    {
        return $this->offsetGet('country');
    }

    /**
     * @return string
     */
    public function getGateway(): string
    {
        return $this->offsetGet('gateway');
    }

    /**
     * @return Gateway\Gids
     */
    public function getGids(): Gateway\Gids
    {
        return $this->offsetGet('gids');
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return array_merge(
            $this->getArrayCopy(),
            // Override
            [
                'gids' => $this->getGids()->jsonSerialize()
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $data['id'] = $data['_id'];
        unset($data['_id']);

        $gids = [];
        foreach ($data['gids'] as $gid) {
            $gids[] = new Gateway\Gid(
                $gid->id,
                $gid->gateway
            );
        }
        $data['gids'] = new Gateway\Gids($gids);

        parent::bsonUnserialize($data);
    }
}
