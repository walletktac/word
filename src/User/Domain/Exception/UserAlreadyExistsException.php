<?php

declare(strict_types=1);

namespace App\User\Domain\Exception;

use Exception;

final class UserAlreadyExistsException extends Exception
{
    public static function withEmail(string $email): self
    {
        return new self("User with email {$email} already exists");
    }
}
