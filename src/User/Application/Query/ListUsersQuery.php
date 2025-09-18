<?php

declare(strict_types=1);

namespace App\User\Application\Query;

use App\User\Application\ValueObject\Pagination;

final readonly class ListUsersQuery
{
    public function __construct(
        public ?string $search = null,
        public ?string $role = null,
        public Pagination $pagination = new Pagination(1, 10)
    ) {}
}
