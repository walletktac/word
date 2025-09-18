<?php

declare(strict_types=1);

namespace App\UserAssessment\Domain\Event;

use App\User\Domain\Entity\User;

readonly class UserAssessedEvent
{
    /**
     * @param list<string> $plan
     */
    public function __construct(
        public User $user,
        public string $level,
        public array $plan
    ) {}
}
