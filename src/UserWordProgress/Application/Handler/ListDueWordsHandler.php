<?php

declare(strict_types=1);

namespace App\UserWordProgress\Application\Handler;

use App\User\Domain\Repository\UserRepositoryInterface;
use App\UserWordProgress\Application\DTO\DueItem;
use App\UserWordProgress\Application\Query\ListDueWordsQuery;
use App\UserWordProgress\Domain\Repository\UserWordProgressRepositoryInterface;
use DateTimeImmutable;
use LogicException;
use RuntimeException;

final class ListDueWordsHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private UserWordProgressRepositoryInterface $progressRepo,
    ) {}

    /** @return DueItem[] */
    public function __invoke(ListDueWordsQuery $q): array
    {
        $user = $this->userRepo->findByIdOrNull($q->userId)
            ?? throw new RuntimeException('User not found');

        $now = $q->now ?? new DateTimeImmutable();
        $rows = $this->progressRepo->findDue($user, $now, $q->limit);

        $out = [];
        foreach ($rows as $p) {
            $w = $p->getWord();

            $wordId = $w->getId();
            if (null === $wordId) {
                throw new LogicException('Word ID is null for due item.');
            }

            $out[] = new DueItem(
                wordId: $wordId,
                headword: $w->getHeadword(),
                dueAt: $p->getDueAt(),
                interval: $p->getInterval(),
                ef: $p->getEf(),
                reps: $p->getReps(),
            );
        }

        return $out;
    }
}
