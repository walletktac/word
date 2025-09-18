<?php

declare(strict_types=1);

namespace App\User\Application\DTO;

final readonly class PagedUsersDTO
{
    /**
     * @param list<UserDTO> $items
     */
    public function __construct(
        public array $items,
        public int $total,
    ) {}
}
