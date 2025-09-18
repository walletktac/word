<?php

declare(strict_types=1);

namespace App\Word\Infrastructure\Http;

use App\User\Domain\Entity\User as DomainUser;
use App\Word\Application\Mapper\WordMapper;
use App\Word\Domain\Entity\Word; // ⬅️ potrzebne do typowanej lambdy
use App\Word\Domain\Repository\WordRepositoryInterface; // ⬅️ dla PHPDoc nad $auth
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Wzorce: Controller, CQRS (Query), Repository, DTO, Dependency Injection.
 *
 * ### Wzorce projektowe:
 * - **Controller** — klasa odpowiada za obsługę zapytań HTTP i mapowanie ich do akcji aplikacyjnych.
 * - **CQRS (Query part)** — operacje tylko do odczytu (listowanie słów), bez modyfikacji danych.
 * - **Repository** — wykorzystuje `WordRepositoryInterface` do pobierania danych z warstwy infrastruktury.
 * - **DTO** — wykorzystuje `WordReadModel` jako ustandaryzowaną strukturę odpowiedzi.
 * - **Dependency Injection** — repozytorium wstrzyknięte przez konstruktor.
 *
 * ### Odpowiedzialność:
 * - Udostępnia publiczne API do przeglądania słówek (`/api/words` oraz `/api/words/daily`).
 * - Obsługuje paginację, filtrowanie po poziomie oraz deterministiczne dobieranie "słów dnia".
 * - Konwertuje encje domenowe (`Word`) na DTO (`WordReadModel`) za pomocą `WordMapper`.
 *
 * ### Specyfika endpointów:
 * - `GET /api/words` — lista słów z paginacją i filtrowaniem.
 * - `GET /api/words/daily` — codzienna porcja słów użytkownika deterministycznie dobrana na podstawie ID i daty.
 *
 * ### Dobre praktyki:
 * - Brak logiki biznesowej – tylko routing, paginacja i prezentacja danych.
 * - Neutralna architektura — może być używany zarówno przez użytkowników zalogowanych, jak i niezalogowanych.
 * - Zastosowanie czystych obiektów DTO jako warstwy prezentacji.
 */
#[Route('/api/words')]
final class PublicWordController extends AbstractController
{
    public function __construct(private readonly WordRepositoryInterface $repo) {}

    #[Route('', methods: ['GET'])]
    public function list(Request $req): JsonResponse
    {
        $page = max(1, $req->query->getInt('page', 1));
        $perPage = min(50, max(1, $req->query->getInt('perPage', 20)));

        $res = $this->repo->search(
            q: $req->query->get('q'),
            level: $req->query->get('level'),
            page: $page,
            perPage: $perPage
        );

        /** @var list<Word> $items */
        $items = $res['items'];

        return $this->json([
            'page' => $page,
            'limit' => $perPage,
            'count' => count($items),
            'items' => array_map(static fn (Word $w) => WordMapper::toReadModel($w), $items),
            'total' => $res['total'],
        ]);
    }

    #[Route('/daily', methods: ['GET'])]
    public function daily(Request $req): JsonResponse
    {
        $dateParam = $req->query->get('date');
        $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable('today');

        $total = $this->repo->totalCount();
        $limit = 10;
        $offset = 0;

        if ($total > $limit) {
            /** @var null|DomainUser $auth */
            $auth = $this->getUser();
            $userId = $auth?->getId() ?? 0;
            $seed = crc32($userId.$date->format('Y-m-d'));
            $offset = $seed % max(1, $total - $limit + 1);
        }

        /** @var list<Word> $items */
        $items = $this->repo->slice($offset, $limit);

        return $this->json([
            'date' => $date->format('Y-m-d'),
            'count' => count($items),
            'items' => array_map(static fn (Word $w) => WordMapper::toReadModel($w), $items),
        ]);
    }
}
