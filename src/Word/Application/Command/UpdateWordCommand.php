<?php

declare(strict_types=1);

namespace App\Word\Application\Command;

/**
 * Wzorzec: Command.
 *
 * Reprezentuje żądanie aktualizacji danych słowa.
 * Dzięki opcjonalnym polom pozwala na częściową aktualizację (partial update),
 * a handler decyduje, które dane zmodyfikować.
 */
class UpdateWordCommand
{
    public function __construct(
        public int $id,
        public ?string $headword = null,
        public ?string $translation = null,
        public ?string $definition = null,
        public ?string $pos = null,
        public ?string $level = null,
        /** @var null|string[] */
        public ?array $tags = null,
        /** @var null|string[] */
        public ?array $examples = null,
        public ?string $phonetic = null,
    ) {}
}
