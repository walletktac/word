<?php

declare(strict_types=1);

namespace App\User\Application\Mapper;

use App\User\Application\DTO\UserDTO;
use App\User\Domain\Entity\User;
use LogicException;

class UserMapper
{
    public static function toDto(User $user): UserDTO
    {
        $id = $user->getId();
        if (null === $id) {
            throw new LogicException('User entity has null ID.');
        }

        /** @var list<string> $roles */
        $roles = $user->getRoles();

        return new UserDTO(
            id: $id,
            email: $user->getEmail(),
            name: $user->getName(),
            roles: $roles,
            level: $user->getLevel(),
        );
    }
}
