<?php

declare(strict_types=1);

namespace App\UserWordProgress\Application\Command;

class AddWordToStudyCommand
{
    public function __construct(
        public int $userId,
        public int $wordId,
    ) {}
}
