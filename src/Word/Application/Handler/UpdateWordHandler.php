<?php

declare(strict_types=1);

namespace App\Word\Application\Handler;

use App\Word\Application\Command\UpdateWordCommand;
use App\Word\Application\Mapper\WordMapper;
use App\Word\Domain\DTO\WordReadModel;
use App\Word\Domain\Exception\WordNotFoundException;
use App\Word\Domain\Repository\WordRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

/**
 * Wzorzec: Command Handler.
 *
 * Przetwarza komendę aktualizacji słowa. Dopuszcza częściowe aktualizacje danych.
 * Wzorzec ten oddziela logikę manipulacji encją od warstw HTTP i repozytorium.
 */
final readonly class UpdateWordHandler
{
    public function __construct(
        private WordRepositoryInterface $wordRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(UpdateWordCommand $c): WordReadModel
    {
        $word = $this->wordRepository->byId($c->id) ?? throw WordNotFoundException::byId($c->id);

        if (null !== $c->headword) {
            $hw = trim($c->headword);
            if ('' === $hw) {
                throw new InvalidArgumentException('Field "headword" cannot be empty.');
            }
            $word->setHeadword($hw);
        }
        if (null !== $c->translation) {
            $word->setTranslation($c->translation);
        }
        if (null !== $c->definition) {
            $word->setDefinition($c->definition);
        }
        if (null !== $c->pos) {
            $word->setPos($c->pos);
        }
        if (null !== $c->level) {
            $word->setLevel($c->level);
        }
        if (null !== $c->phonetic) {
            $word->setPhonetic($c->phonetic);
        }

        if (null !== $c->tags) {
            $tags = array_values(array_filter(array_map('strval', $c->tags), fn (string $v) => '' !== $v));
            $word->setTags($tags);
        }
        if (null !== $c->examples) {
            $examples = array_values(array_filter(array_map('strval', $c->examples), fn (string $v) => '' !== $v));
            $word->setExamples($examples);
        }

        $this->entityManager->flush();

        return WordMapper::toReadModel($word);
    }
}
