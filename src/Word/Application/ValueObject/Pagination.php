<?php

declare(strict_types=1);

namespace App\Word\Application\ValueObject;

/**
 * Wzorzec: Value Object.
 *
 * Reprezentuje dane paginacji w sposób niemutowalny i bez tożsamości.
 * Dzięki enkapsulacji logiki (np. minimalna strona to 1, maksymalna liczba elementów to 100),
 * Value Object zapewnia spójność i centralizację walidacji na poziomie konstrukcji obiektu.
 *
 * Jest odporny na nieprawidłowe użycie i wspiera czystą architekturę.
 */
final class Pagination
{
    public function __construct(public int $page, public int $perPage)
    {
        $this->page = max(1, $page);
        $this->perPage = min(100, max(1, $perPage));
    }
}
