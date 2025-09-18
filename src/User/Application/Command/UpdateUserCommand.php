<?php

declare(strict_types=1);

namespace App\User\Application\Command;

final class UpdateUserCommand
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        public int $id,
        public ?string $email = null,
        public ?string $name = null,
        public ?array $roles = null,
        public ?string $password = null
    ) {}
}
