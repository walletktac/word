<?php

declare(strict_types=1);

namespace App\Word\Application\Handler;

use App\Word\Application\Command\DeleteWordCommand;
use App\Word\Domain\Exception\WordNotFoundException;
use App\Word\Domain\Repository\WordRepositoryInterface;

/**
 * Wzorzec: Command Handler.
 *
 * Realizuje przypadek użycia usuwania słowa na podstawie ID.
 * Handler oddziela logikę biznesową od kontrolera i zapewnia izolację logiki aplikacyjnej.
 */
final class DeleteWordHandler
{
    public function __construct(private readonly WordRepositoryInterface $wordRepository) {}

    public function __invoke(DeleteWordCommand $deleteWordCommand): void
    {
        $word = $this->wordRepository->byId($deleteWordCommand->id) ?? throw WordNotFoundException::byId($deleteWordCommand->id);
        $this->wordRepository->remove($word);
    }
}
