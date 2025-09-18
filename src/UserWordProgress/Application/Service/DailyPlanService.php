<?php

declare(strict_types=1);

namespace App\UserWordProgress\Application\Service;

use App\User\Domain\Entity\User;
use App\UserWordProgress\Domain\Repository\UserWordProgressRepositoryInterface;
use App\Word\Domain\Repository\WordRepositoryInterface;
use DateTimeImmutable;
use LogicException;

final class DailyPlanService
{
    private const BATCH_MIN = 100;

    public function __construct(
        private UserWordProgressRepositoryInterface $progressRepo,
        private WordRepositoryInterface $wordRepo,
    ) {}

    /**
     * @return array{
     *   items: list<array{"type":'new'|'review',"wordId":int,"headword":string,"dueAt":null|DateTimeImmutable}>,
     *   mix: array{"review":int,"new":int}
     * }
     */
    public function buildWithReviewFirst(
        User $user,
        DateTimeImmutable $date,
        int $target = 10,
        int $reviewFirstDays = 2,
        int $reviewFirstMin = 5
    ): array {
        $userId = $user->getId();
        if (null === $userId) {
            throw new LogicException('Authenticated user has null ID.');
        }

        $allDue = $this->progressRepo->findDue($user, $date, max($target, $reviewFirstMin) * 3);
        $threshold = $date->modify("-{$reviewFirstDays} days");
        $overdueCount = 0;

        $items = [];
        foreach ($allDue as $p) {
            if ($p->getDueAt() && $p->getDueAt() <= $threshold) {
                ++$overdueCount;
            }
        }

        // 1) ile recenzji bierzemy
        $takeReviews = min($target, max($reviewFirstMin, count($allDue)));
        if (0 === $overdueCount) {
            // brak mocno zaległych -> bierz tyle due, ile jest do targetu
            $takeReviews = min($target, count($allDue));
        }

        $reviewItems = array_slice($allDue, 0, $takeReviews);
        foreach ($reviewItems as $p) {
            $w = $p->getWord();
            $wordId = $w->getId();

            if (null === $wordId) {
                throw new LogicException('Word ID is null for a persisted entity.');
            }

            $items[] = [
                'type' => 'review',
                'wordId' => $wordId,
                'headword' => $w->getHeadword(),
                'dueAt' => $p->getDueAt(),
            ];
        }

        // 2) dołóż „new”, jeśli jeszcze brakuje do targetu i nie ma mocno zaległych
        $reviewCount = count($reviewItems);
        $newCount = 0;

        if ($reviewCount < $target && 0 === $overdueCount) {
            $needed = $target - $reviewCount;

            $known = array_flip($this->progressRepo->wordIdsByUser($user));
            $total = $this->wordRepo->totalCount();

            if ($total > 0) {
                $seed = crc32($userId.$date->format('Y-m-d'));
                $offset = $seed % max(1, $total);

                $collected = 0;
                $visited = 0;
                $batch = max(self::BATCH_MIN, $needed * 2);

                while ($collected < $needed && $visited < $total) {
                    $take = min($batch, $total - $visited);
                    $slice = $this->wordRepo->slice($offset, $take);

                    foreach ($slice as $w) {
                        $id = $w->getId();
                        if (null === $id) {
                            continue;
                        }
                        if (!isset($known[$id])) {
                            $items[] = [
                                'type' => 'new',
                                'wordId' => $id,
                                'headword' => $w->getHeadword(),
                                'dueAt' => null,
                            ];
                            if (++$collected >= $needed) {
                                break;
                            }
                        }
                    }
                    $visited += $take;
                    $offset = ($offset + $take) % $total;
                }
                $newCount = $collected;
            }
        }

        return [
            'items' => $items,
            'mix' => ['review' => $reviewCount, 'new' => $newCount],
        ];
    }
}
