<?php

declare(strict_types=1);

namespace App\User\Application\Command;

final class CreateUserCommand
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        public string $email,
        public string $password,
        public ?string $name = null,
        public array $roles = ['ROLE_USER'],
    ) {}
}
