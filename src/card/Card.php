<?php

namespace Yosmy\Payment;

use MongoDB\Model\BSONDocument;
use Yosmy\Payment\Gateway;

class Card extends BSONDocument
{
    /**
     * @param string       $id
     * @param string       $user
     * @param string       $last4
     * @param string       $fingerprint
     * @param Gateway\Gids $gids
     * @param array        $raw
     */
    public function __construct(
        string $id,
        string $user,
        string $last4,
        string $fingerprint,
        Gateway\Gids $gids,
        array $raw
    ) {
        parent::__construct([
            'id' => $id,
            'user' => $user,
            'last4' => $last4,
            'fingerprint' => $fingerprint,
            'gids' => $gids,
            'raw' => $raw,
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
    public function getLast4(): string
    {
        return $this->offsetGet('last4');
    }

    /**
     * @return string
     */
    public function getFingerprint(): string
    {
        return $this->offsetGet('fingerprint');
    }

    /**
     * @return Gateway\Gids
     */
    public function getGids(): Gateway\Gids
    {
        return $this->offsetGet('gids');
    }

    /**
     * Raw data, to use it for other new gateways
     *
     * @return array
     */
    public function getRaw(): array
    {
        return $this->offsetGet('raw');
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->offsetGet('raw') == [];
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

        $data['raw'] = (array) $data['raw'];

        parent::bsonUnserialize($data);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            '_id' => $this->id,
            'user' => $this->user,
            'last4' => $this->last4,
            'fingerprint' => $this->fingerprint,
            'gids' => $this->gids->jsonSerialize(),
            'raw' => $this->raw,
        ];
    }
}
