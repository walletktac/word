<?php

declare(strict_types=1);

namespace App\Word\Infrastructure\Http;

use App\Word\Application\Command\CreateWordCommand;
use App\Word\Application\Command\DeleteWordCommand;
use App\Word\Application\Command\UpdateWordCommand;
use App\Word\Application\Handler\CreateWordHandler;
use App\Word\Application\Handler\DeleteWordHandler;
use App\Word\Application\Handler\ListWordsHandler;
use App\Word\Application\Handler\UpdateWordHandler;
use App\Word\Application\Query\ListWordsQuery;
use App\Word\Application\ValueObject\Pagination;
use App\Word\Domain\Exception\WordNotFoundException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Wzorce: Controller, CQRS, Dependency Injection, DTO.
 *
 * ### Wzorce projektowe:
 * - **Controller** — bezpośrednio obsługuje żądania HTTP i deleguje logikę do handlerów (Application Layer).
 * - **CQRS (Command Query Responsibility Segregation)** — oddziela operacje modyfikujące dane (Create, Update, Delete) od operacji odczytu (List).
 * - **DTO (Data Transfer Object)** — dane wejściowe i wyjściowe są opakowane w dedykowane klasy `Command` i `ReadModel`, zapewniając silne typowanie.
 * - **Dependency Injection** — handlery wstrzykiwane są przez konstruktor, zgodnie z zasadą Inversion of Control.
 *
 * ### Odpowiedzialność:
 * - Mapowanie danych HTTP (Request) do Commandów (np. `CreateWordCommand`, `UpdateWordCommand`).
 * - Obsługa walidacji i wyjątków (`InvalidArgumentException`, `WordNotFoundException`).
 * - Zwracanie sformatowanych odpowiedzi JSON (z użyciem Symfony JsonResponse).
 *
 * ### Kontekst:
 * Klasa ograniczona do roli `ROLE_ADMIN` za pomocą atrybutu `[IsGranted('ROLE_ADMIN')]`.
 * Służy do zarządzania słowami w panelu administracyjnym.
 *
 * ### Zasady dobrego projektowania:
 * - Brak logiki domenowej w kontrolerze – wszystko przekazywane do warstwy aplikacji.
 * - Jedna odpowiedzialność: przekładanie Request na Command + zwrot odpowiedzi.
 */
#[Route('/api/admin/words')]
#[IsGranted('ROLE_ADMIN')]
final class AdminWordController extends AbstractController
{
    public function __construct(
        private readonly CreateWordHandler $create,
        private readonly UpdateWordHandler $update,
        private readonly DeleteWordHandler $delete,
        private readonly ListWordsHandler $list,
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $req): JsonResponse
    {
        $page = max(1, $req->query->getInt('page', 1));
        $perPage = max(1, min(100, $req->query->getInt('perPage', 10)));
        $q = $req->query->get('q');
        $level = $req->query->get('level');

        $res = ($this->list)(new ListWordsQuery($q, $level, new Pagination($page, $perPage)));

        return $this->json($res);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $req): JsonResponse
    {
        $p = json_decode($req->getContent(), true) ?? [];

        try {
            $cmd = new CreateWordCommand(
                headword: (string) ($p['headword'] ?? ''),
                translation: $p['translation'] ?? null,
                definition: $p['definition'] ?? null,
                pos: $p['pos'] ?? null,
                level: $p['level'] ?? null,
                tags: is_array($p['tags'] ?? null) ? $p['tags'] : [],
                examples: is_array($p['examples'] ?? null) ? $p['examples'] : [],
                phonetic: $p['phonetic'] ?? null
            );
            $rm = ($this->create)($cmd);

            return $this->json($rm, 201);
        } catch (InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], 422);
        }
    }

    #[Route('/{id}', methods: ['PATCH', 'PUT'])]
    public function updateOne(int $id, Request $req): JsonResponse
    {
        $p = json_decode($req->getContent(), true) ?? [];

        try {
            $rm = ($this->update)(new UpdateWordCommand(
                id: $id,
                headword: $p['headword'] ?? null,
                translation: $p['translation'] ?? null,
                definition: $p['definition'] ?? null,
                pos: $p['pos'] ?? null,
                level: $p['level'] ?? null,
                tags: array_key_exists('tags', $p) ? (is_array($p['tags']) ? $p['tags'] : []) : null,
                examples: array_key_exists('examples', $p) ? (is_array($p['examples']) ? $p['examples'] : []) : null,
                phonetic: $p['phonetic'] ?? null
            ));

            return $this->json($rm);
        } catch (WordNotFoundException $e) {
            return $this->json(['message' => $e->getMessage()], 404);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteOne(int $id): JsonResponse
    {
        try {
            ($this->delete)(new DeleteWordCommand($id));

            return $this->json(null, 204);
        } catch (WordNotFoundException $e) {
            return $this->json(['message' => $e->getMessage()], 404);
        }
    }
}
