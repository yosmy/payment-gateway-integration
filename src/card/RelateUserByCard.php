<?php

namespace Yosmy\Payment;

use Yosmy;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.relate_user'
 *     ]
 * })
 */
class RelateUserByCard implements Yosmy\RelateUser
{
    /**
     * @var GatherCard
     */
    private $gatherCard;

    /**
     * @var CollectCards
     */
    private $collectCards;

    /**
     * @param GatherCard   $gatherCard
     * @param CollectCards $collectCards
     */
    public function __construct(
        GatherCard $gatherCard,
        CollectCards $collectCards
    ) {
        $this->gatherCard = $gatherCard;
        $this->collectCards = $collectCards;
    }

    /**
     * {@inheritDoc}
     */
    public function relate(
        string $user,
        array $included
    ): array {
        /** @var Card[] $cardsByUser */
        $cardsByUser = $this->collectCards->collect(
            null,
            $user,
            null,
            true,
            null,
            null
        );

        foreach ($cardsByUser as $cardByUser) {
            $included[$cardByUser->getId()] = $cardByUser;

            /** @var Card[] $cardsByFingerprint */
            $cardsByFingerprint = $this->collectCards->collect(
                null,
                null,
                $cardByUser->getFingerprint(),
                true,
                null,
                null
            );

            foreach ($cardsByFingerprint as $cardByFingerprint) {
                if (isset($included[$cardByFingerprint->getId()])) {
                    continue;
                }

                $included[$cardByFingerprint->getId()] = $cardByFingerprint;

                $included = $this->relate(
                    $cardByFingerprint->getUser(),
                    $included
                );
            }
        }

        return $included;
    }
}