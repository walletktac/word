<?php

declare(strict_types=1);

namespace App\UserWordProgress\Domain\Enum;

enum ReviewStatus: string
{
    case NEW = 'new';
    case LEARNING = 'learning';
    case LEARNED = 'learned';
}
