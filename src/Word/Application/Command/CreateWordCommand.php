<?php

declare(strict_types=1);

namespace App\Word\Application\Command;

/**
 * Wzorzec: Command.
 *
 * Reprezentuje dane wymagane do utworzenia nowego słowa w systemie.
 * Oddziela dane wejściowe od logiki, która je przetwarza (handler),
 * zgodnie z podejściem Command-Handler.
 */
final class CreateWordCommand
{
    public function __construct(
        public string $headword,
        public ?string $translation = null,
        public ?string $definition = null,
        public ?string $pos = null,
        public ?string $level = null,
        /** @var string[] */
        public array $tags = [],
        /** @var string[] */
        public array $examples = [],
        public ?string $phonetic = null,
    ) {}
}
