<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Http;

use App\User\Application\Handler\GetCurrentUserHandler;
use App\User\Application\Query\GetCurrentUserQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MeController extends AbstractController
{
    public function __construct(
        private readonly GetCurrentUserHandler $getCurrentUser
    ) {}

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->json(['message' => 'Not authenticated'], 401);
        }

        $result = ($this->getCurrentUser)(new GetCurrentUserQuery($user->getUserIdentifier()));

        return $this->json($result);
    }
}
