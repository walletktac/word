<?php

declare(strict_types=1);

namespace App\Word\Application\Handler;

use App\Word\Application\Command\CreateWordCommand;
use App\Word\Application\Mapper\WordMapper;
use App\Word\Domain\DTO\WordReadModel;
use App\Word\Domain\Entity\Word;
use App\Word\Domain\Repository\WordRepositoryInterface;
use InvalidArgumentException;

/**
 * Wzorzec: Command Handler.
 *
 * Obsługuje przypadek użycia tworzenia nowego słowa.
 * Przyjmuje obiekt `CreateWordCommand` i wykonuje logikę tworzenia encji,
 * walidację oraz zapis do repozytorium. Zwraca model do odczytu (ReadModel).
 */
final class CreateWordHandler
{
    public function __construct(private WordRepositoryInterface $repo) {}

    public function __invoke(CreateWordCommand $c): WordReadModel
    {
        $headword = trim((string) $c->headword);
        if ('' === $headword) {
            throw new InvalidArgumentException('Field "headword" is required.');
        }

        $tags = array_values(array_filter(
            array_map(static fn ($v) => trim((string) $v), $c->tags ?? []),
            static fn (string $s) => '' !== $s
        ));
        $examples = array_values(array_filter(
            array_map(static fn ($v) => trim((string) $v), $c->examples ?? []),
            static fn (string $s) => '' !== $s
        ));

        $word = new Word($headword);
        $word->setTranslation($c->translation);
        $word->setDefinition($c->definition);
        $word->setPos($c->pos);
        $word->setLevel($c->level);
        $word->setTags($tags);
        $word->setExamples($examples);
        $word->setPhonetic($c->phonetic);

        $this->repo->add($word);

        return WordMapper::toReadModel($word);
    }
}
