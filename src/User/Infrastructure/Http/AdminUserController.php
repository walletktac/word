<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Http;

use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Command\DeleteUserCommand;
use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\Handler\CreateUserHandler;
use App\User\Application\Handler\DeleteUserHandler;
use App\User\Application\Handler\ListUsersHandler;
use App\User\Application\Handler\UpdateUserHandler;
use App\User\Application\Query\ListUsersQuery;
use App\User\Application\ValueObject\Pagination;
use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Exception\UserNotFoundException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/users')]
#[IsGranted('ROLE_ADMIN')]
final class AdminUserController extends AbstractController
{
    public function __construct(
        private readonly CreateUserHandler $create,
        private readonly UpdateUserHandler $update,
        private readonly DeleteUserHandler $delete,
        private readonly ListUsersHandler $list,
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('perPage', 10);

        $queryRaw = $request->query->get('q');
        $query = is_string($queryRaw) ? $queryRaw : null;

        $response = ($this->list)(new ListUsersQuery(
            search: $query,
            pagination: new Pagination($page, $perPage)
        ));

        return $this->json($response);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];

        try {
            $command = new CreateUserCommand(
                email: (string) ($payload['email'] ?? ''),
                password: (string) ($payload['password'] ?? ''),
                name: $payload['name'] ?? null,
                roles: is_array($payload['roles'] ?? null) ? $payload['roles'] : ['ROLE_USER']
            );

            $responseModel = ($this->create)($command);

            return $this->json($responseModel, 201);
        } catch (UserAlreadyExistsException $e) {
            return $this->json(['message' => $e->getMessage()], 409);
        } catch (InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], 422);
        }
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function updateOne(int $id, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];

        try {
            $responseModel = ($this->update)(new UpdateUserCommand(
                id: $id,
                email: $payload['email'] ?? null,
                name: array_key_exists('name', $payload) ? $payload['name'] : null,
                roles: array_key_exists('roles', $payload) ? (is_array($payload['roles']) ? $payload['roles'] : []) : null,
                password: !empty($payload['password']) ? (string) $payload['password'] : null
            ));

            return $this->json($responseModel);
        } catch (UserNotFoundException $e) {
            return $this->json(['message' => $e->getMessage()], 404);
        } catch (UserAlreadyExistsException $e) {
            return $this->json(['message' => $e->getMessage()], 409);
        } catch (InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], 422);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteOne(int $id): JsonResponse
    {
        try {
            ($this->delete)(new DeleteUserCommand($id));

            return $this->json(null, 204);
        } catch (UserNotFoundException $e) {
            return $this->json(['message' => $e->getMessage()], 404);
        }
    }
}
