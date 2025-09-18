<?php

declare(strict_types=1);

namespace App\User\Application\DTO;

final readonly class UserDTO
{
    /**
     * @param list<string> $roles
     */
    public function __construct(
        public int $id,
        public string $email,
        public ?string $name,
        public array $roles,
        public ?string $level = null,
    ) {}
}
