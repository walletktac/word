<?php

declare(strict_types=1);

namespace App\Word\Domain\DTO;

use DateTimeImmutable;

/**
 * Wzorzec: Data Transfer Object (DTO).
 *
 * Reprezentuje pełne dane słowa, które są przekazywane do warstwy prezentacji (np. API).
 * Oddziela dane do odczytu od encji domenowej `Word` i nie zawiera żadnej logiki biznesowej.
 *
 * Stosowany w celu uproszczenia transferu danych i uniknięcia bezpośredniego ujawniania encji domenowych.
 */
final readonly class WordReadModel
{
    public function __construct(
        public int $id,
        public string $headword,
        public ?string $translation,
        public ?string $definition,
        public ?string $pos,
        public ?string $level,
        /** @var string[] */
        public array $tags,
        /** @var string[] */
        public array $examples,
        public ?string $phonetic,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {}
}
