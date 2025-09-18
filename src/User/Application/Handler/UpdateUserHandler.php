<?php

declare(strict_types=1);

namespace App\User\Application\Handler;

use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\DTO\UserDTO;
use App\User\Application\Mapper\UserMapper;
use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Repository\UserRepositoryInterface;
use LogicException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UpdateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @throws UserNotFoundException
     * @throws UserAlreadyExistsException
     */
    public function __invoke(UpdateUserCommand $command): UserDTO
    {
        $user = $this->userRepository->findById($command->id);

        if (null !== $command->email && $command->email !== $user->getEmail()) {
            try {
                $existing = $this->userRepository->findByEmail($command->email);
            } catch (UserNotFoundException) {
                $existing = null;
            }

            if (null !== $existing && $existing->getId() !== $user->getId()) {
                throw UserAlreadyExistsException::withEmail($command->email);
            }

            $user->setEmail($command->email);
        }

        if (null !== $command->name) {
            $user->setName($command->name);
        }

        if (null !== $command->roles) {
            $user->setRoles($command->roles);
        }

        if (null !== $command->password) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));
        }

        $saved = $this->userRepository->save($user);
        if (null === $saved->getId()) {
            throw new LogicException('User ID is null after save().');
        }

        return UserMapper::toDto($saved);
    }
}
