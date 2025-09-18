<?php

declare(strict_types=1);

namespace App\User\Application\Command;

final class DeleteUserCommand
{
    public function __construct(
        public int $id
    ) {}
}
