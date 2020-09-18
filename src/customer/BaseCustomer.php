<?php

namespace Yosmy\Payment;

use Yosmy\Mongo;

class BaseCustomer extends Mongo\Document implements Customer
{
    /**
     * @param string       $user
     * @param string       $country
     * @param string       $gateway
     * @param Gateway\Gids $gids
     */
    public function __construct(
        string $user,
        string $country,
        string $gateway,
        Gateway\Gids $gids
    ) {
        parent::__construct([
            '_id' => $user,
            'country' => $country,
            'gateway' => $gateway,
            'gids' => $gids,
        ]);
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->offsetGet('_id');
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
    public function bsonUnserialize(array $data)
    {
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

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): object
    {
        $data = parent::jsonSerialize();

        $data->id = $data->_id;

        unset($data->_id);

        $data->gids = $this->getGids()->jsonSerialize();

        return $data;
    }
}
