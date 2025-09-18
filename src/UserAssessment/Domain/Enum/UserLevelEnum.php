<?php

declare(strict_types=1);

namespace App\UserAssessment\Domain\Enum;

use InvalidArgumentException;

enum UserLevelEnum: string
{
    case A1 = 'A1';
    case A2 = 'A2';
    case B1 = 'B1';
    case B2 = 'B2';
    case C1 = 'C1';
    case C2 = 'C2';

    public static function fromString(string $level): self
    {
        return match (strtoupper($level)) {
            'A1' => self::A1,
            'A2' => self::A2,
            'B1' => self::B1,
            'B2' => self::B2,
            'C1' => self::C1,
            'C2' => self::C2,
            default => throw new InvalidArgumentException("Invalid level: {$level}"),
        };
    }
}
