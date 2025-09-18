<?php

declare(strict_types=1);

namespace App\UserWordProgress\Application\DTO;

use DateTimeImmutable;

class DueItem
{
    public function __construct(
        public int $wordId,
        public string $headword,
        public ?DateTimeImmutable $dueAt,
        public int $interval,
        public float $ef,
        public int $reps,
    ) {}
}
