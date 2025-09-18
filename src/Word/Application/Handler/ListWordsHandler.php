<?php

declare(strict_types=1);

namespace App\Word\Application\Handler;

use App\Word\Application\Mapper\WordMapper;
use App\Word\Application\Query\ListWordsQuery;
use App\Word\Domain\Repository\WordRepositoryInterface;

/**
 * Wzorzec: Query Handler.
 *
 * Realizuje zapytanie do odczytu listy słów z możliwością filtrowania i paginacji.
 * Zwraca dane w formacie `ReadModel`, oddzielając logikę odczytu od innych warstw systemu.
 */
final class ListWordsHandler
{
    public function __construct(private WordRepositoryInterface $wordRepository) {}

    /** @return array{items: array<int, mixed>, total: int} */
    public function __invoke(ListWordsQuery $listWordsQuery): array
    {
        $result = $this->wordRepository->search($listWordsQuery->q, $listWordsQuery->level, $listWordsQuery->pagination->page, $listWordsQuery->pagination->perPage);

        return [
            'items' => array_map([WordMapper::class, 'toReadModel'], $result['items']),
            'total' => $result['total'],
        ];
    }
}
