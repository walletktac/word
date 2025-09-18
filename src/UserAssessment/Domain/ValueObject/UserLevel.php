<?php

declare(strict_types=1);

namespace App\UserAssessment\Domain\ValueObject;

use App\UserAssessment\Domain\Enum\UserLevelEnum;

class UserLevel
{
    public function __construct(private UserLevelEnum $value) {}

    public function __toString(): string
    {
        return $this->value->value;
    }

    public function getValue(): UserLevelEnum
    {
        return $this->value;
    }

    public function equals(UserLevel $other): bool
    {
        return $this->value === $other->value;
    }
}
