<?php

declare(strict_types=1);

namespace App\UserWordProgress\Application\Handler;

use App\User\Domain\Repository\UserRepositoryInterface;
use App\UserWordProgress\Application\Command\AddWordToStudyCommand;
use App\UserWordProgress\Domain\Entity\UserWordProgress;
use App\UserWordProgress\Domain\Repository\UserWordProgressRepositoryInterface;
use App\Word\Domain\Repository\WordRepositoryInterface;
use RuntimeException;

final class AddWordToStudyHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private WordRepositoryInterface $wordRepo,
        private UserWordProgressRepositoryInterface $progressRepo,
    ) {}

    public function __invoke(AddWordToStudyCommand $c): void
    {
        $user = $this->userRepo->findByIdOrNull($c->userId)
            ?? throw new RuntimeException('User not found');

        $word = $this->wordRepo->byId($c->wordId)
            ?? throw new RuntimeException('Word not found');

        if ($this->progressRepo->findOne($user, $c->wordId)) {
            return;
        }

        $this->progressRepo->save(new UserWordProgress($user, $word));
    }
}
