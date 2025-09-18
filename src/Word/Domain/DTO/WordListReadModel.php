<?php

declare(strict_types=1);

namespace App\Word\Domain\DTO;

/**
 * Wzorzec: Data Transfer Object (DTO).
 *
 * Służy do przekazania listy słów (`WordReadModel`) wraz z liczbą wszystkich wyników.
 * Używany jako zwracany typ w zapytaniach paginowanych (np. w API).
 *
 * Oddziela dane prezentacyjne od domeny i zapewnia wygodny format odpowiedzi.
 */
final class WordListReadModel
{
    /** @param list<WordReadModel> $items */
    public function __construct(
        public array $items,
        public int $total
    ) {}
}
