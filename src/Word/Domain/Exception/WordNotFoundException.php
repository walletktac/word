<?php

declare(strict_types=1);

namespace App\Word\Domain\Exception;

use RuntimeException;

final class WordNotFoundException extends RuntimeException
{
    public static function byId(int $id): self
    {
        return new self("Word #{$id} not found");
    }
}
