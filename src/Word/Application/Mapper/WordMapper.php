<?php

declare(strict_types=1);

namespace App\Word\Application\Mapper;

use App\Word\Domain\DTO\WordListReadModel;
use App\Word\Domain\DTO\WordReadModel;
use App\Word\Domain\Entity\Word;
use LogicException;

/**
 * Wzorzec: Mapper (lub DTO Assembler).
 *
 * Odpowiada za transformację encji domenowej `Word` na modele odczytu (`WordReadModel`, `WordListReadModel`),
 * które są używane w warstwie prezentacji (np. API, UI).
 *
 * Dzięki zastosowaniu mappera, warstwa domenowa pozostaje niezależna od formatów danych do odczytu,
 * co sprzyja czystej architekturze i testowalności.
 */
class WordMapper
{
    public static function toReadModel(Word $word): WordReadModel
    {
        $id = $word->getId();
        if (null === $id) {
            throw new LogicException('Word ID is null – make sure you flushed before mapping.');
        }

        return new WordReadModel(
            id: $id,
            headword: $word->getHeadword(),
            translation: $word->getTranslation(),
            definition: $word->getDefinition(),
            pos: $word->getPos(),
            level: $word->getLevel(),
            tags: $word->getTags(),
            examples: $word->getExamples(),
            phonetic: $word->getPhonetic(),
            createdAt: $word->getCreatedAt(),
            updatedAt: $word->getUpdatedAt()
        );
    }

    /** @param list<Word> $items */
    public static function toListReadModel(array $items, int $total): WordListReadModel
    {
        $dtoItems = array_map([self::class, 'toReadModel'], $items);

        return new WordListReadModel($dtoItems, $total);
    }
}
