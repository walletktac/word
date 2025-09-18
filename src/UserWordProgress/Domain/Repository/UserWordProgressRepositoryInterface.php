<?php

declare(strict_types=1);

namespace App\UserWordProgress\Domain\Repository;

use App\User\Domain\Entity\User;
use App\UserWordProgress\Domain\Entity\UserWordProgress;
use DateTimeImmutable;

interface UserWordProgressRepositoryInterface
{
    public function save(UserWordProgress $entity): void;

    public function findOne(User $user, int $wordId): ?UserWordProgress;

    /** @return UserWordProgress[] */
    public function findDue(User $user, DateTimeImmutable $now, int $limit = 100): array;

    /** @return int[] IDs słów, które użytkownik już „ma” w SRS */
    public function wordIdsByUser(User $user): array;
}
