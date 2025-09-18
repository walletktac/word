<?php

declare(strict_types=1);

namespace App\UserWordProgress\Application\Command;

class ReviewWordCommand
{
    public function __construct(
        public int $userId,
        public int $wordId,
        public int $grade, // 0–5
    ) {}
}
