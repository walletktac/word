<?php

declare(strict_types=1);

namespace App\User\Application\Handler;

use App\User\Application\Command\DeleteUserCommand;
use App\User\Domain\Repository\UserRepositoryInterface;

final readonly class DeleteUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(DeleteUserCommand $command): void
    {
        $user = $this->userRepository->findById($command->id);
        $this->userRepository->delete($user);
    }
}
