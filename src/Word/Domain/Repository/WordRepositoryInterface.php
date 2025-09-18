<?php

declare(strict_types=1);

namespace App\Word\Domain\Repository;

use App\Word\Domain\Entity\Word;

/**
 * Wzorzec: Repository (interfejs repozytorium).
 *
 * Odpowiada za oddzielenie logiki dostępu do danych od logiki domenowej.
 * Definiuje kontrakt dla repozytoriów obsługujących encję Word.
 *
 * Główne cechy:
 * - Abstrakcja nad źródłem danych (np. Doctrine, API, plik).
 * - Pozwala na łatwe mockowanie i testowanie.
 * - Zgodne z DDD — repozytorium działa jak "kolekcja" obiektów domenowych.
 *
 * Definiowane metody:
 * - `search()` — filtrowanie i paginacja słów.
 * - `add()` / `remove()` — zarządzanie encjami.
 * - `byId()` — pobieranie po identyfikatorze.
 * - `slice()` — pobieranie fragmentów kolekcji (przydatne np. przy eksportach).
 */
interface WordRepositoryInterface
{
    /** @return array{items: Word[], total: int} */
    public function search(?string $q, ?string $level, int $page, int $perPage): array;

    public function add(Word $word): void;

    public function remove(Word $word): void;

    public function byId(int $id): ?Word;

    public function totalCount(): int;

    /** @return list<Word> */
    public function slice(int $offset, int $limit): array;
}
