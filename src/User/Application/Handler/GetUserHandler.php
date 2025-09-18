<?php

declare(strict_types=1);

namespace App\User\Application\Handler;

use App\User\Application\DTO\UserDTO;
use App\User\Application\Mapper\UserMapper;
use App\User\Application\Query\GetUserQuery;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Repository\UserRepositoryInterface;

final readonly class GetUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * @throws UserNotFoundException
     */
    public function __invoke(GetUserQuery $query): UserDTO
    {
        $user = $this->userRepository->findById($query->id);

        return UserMapper::toDto($user);
    }
}
