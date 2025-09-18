<?php

declare(strict_types=1);

namespace App\User\Application\ValueObject;

final class Pagination
{
    public function __construct(public int $page, public int $perPage)
    {
        $this->page = max(1, $page);
        $this->perPage = min(100, max(1, $perPage));
    }
}
