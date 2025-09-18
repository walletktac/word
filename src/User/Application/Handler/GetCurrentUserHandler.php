<?php

declare(strict_types=1);

namespace App\User\Application\Handler;

use App\User\Application\DTO\UserDTO;
use App\User\Application\Mapper\UserMapper;
use App\User\Application\Query\GetCurrentUserQuery;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Repository\UserRepositoryInterface;

final readonly class GetCurrentUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * @throws UserNotFoundException
     */
    public function __invoke(GetCurrentUserQuery $query): UserDTO
    {
        $user = $this->userRepository->findByEmail($query->userIdentifier);
        if (null === $user) {
            throw UserNotFoundException::withEmail($query->userIdentifier);
        }

        return UserMapper::toDto($user);
    }
}
