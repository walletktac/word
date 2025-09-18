<?php

declare(strict_types=1);

namespace App\User\Application\Handler;

use App\User\Application\Command\CreateUserCommand;
use App\User\Application\DTO\UserDTO;
use App\User\Application\Mapper\UserMapper;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Repository\UserRepositoryInterface;
use LogicException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class CreateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @throws UserAlreadyExistsException
     */
    public function __invoke(CreateUserCommand $command): UserDTO
    {
        if ($this->userRepository->existsByEmail($command->email)) {
            throw UserAlreadyExistsException::withEmail($command->email);
        }

        $user = new User(
            email: $command->email,
            password: '',
            name: $command->name,
            roles: $command->roles
        );

        $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));

        $saved = $this->userRepository->save($user);
        if (null === $saved->getId()) {
            throw new LogicException('User ID is null after save().');
        }

        return UserMapper::toDto($saved);
    }
}
