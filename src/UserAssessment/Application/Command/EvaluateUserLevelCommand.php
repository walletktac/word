<?php

declare(strict_types=1);

namespace App\UserAssessment\Application\Command;

final readonly class EvaluateUserLevelCommand
{
    public function __construct(
        public int $userId,
        /** @var array<int, array{word: string, correct: bool}> */
        public array $wordHistory
    ) {}
}
