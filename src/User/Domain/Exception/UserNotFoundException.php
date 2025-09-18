<?php

declare(strict_types=1);

namespace App\User\Domain\Exception;

use DomainException;

final class UserNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("User with ID {$id} not found");
    }

    public static function withEmail(string $email): self
    {
        return new self("User with email {$email} not found");
    }
}
