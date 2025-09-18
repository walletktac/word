<?php

declare(strict_types=1);

namespace App\Word\Application\Query;

use App\Word\Application\ValueObject\Pagination;

/**
 * Wzorzec: Query.
 *
 * Reprezentuje zapytanie o listę słów z opcjonalnym filtrowaniem (`q`, `level`) oraz paginacją.
 * Wzorzec ten pozwala oddzielić dane wejściowe zapytania od jego obsługi (ListWordsHandler),
 * co sprzyja modularności i testowalności kodu.
 */
final class ListWordsQuery
{
    public function __construct(
        public ?string $q,
        public ?string $level,
        public Pagination $pagination
    ) {}
}
