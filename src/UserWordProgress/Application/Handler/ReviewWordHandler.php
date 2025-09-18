<?php

declare(strict_types=1);

namespace App\UserWordProgress\Application\Handler;

use App\User\Domain\Repository\UserRepositoryInterface;
use App\UserWordProgress\Application\Command\ReviewWordCommand;
use App\UserWordProgress\Domain\Repository\UserWordProgressRepositoryInterface;
use DateTimeImmutable;
use RuntimeException;

final class ReviewWordHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private UserWordProgressRepositoryInterface $progressRepo,
    ) {}

    public function __invoke(ReviewWordCommand $c): void
    {
        $user = $this->userRepo->findByIdOrNull($c->userId)
            ?? throw new RuntimeException('User not found');

        $progress = $this->progressRepo->findOne($user, $c->wordId)
            ?? throw new RuntimeException('Word not in study set');

        $progress->review($c->grade, new DateTimeImmutable());
        $this->progressRepo->save($progress);
    }
}
