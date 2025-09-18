<?php

declare(strict_types=1);

namespace App\UserWordProgress\Application\Query;

use DateTimeImmutable;

class ListDueWordsQuery
{
    public function __construct(
        public int $userId,
        public ?DateTimeImmutable $now = null,
        public int $limit = 100,
    ) {}
}
