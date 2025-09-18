<?php

declare(strict_types=1);

namespace App\User\Domain\Repository;

use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFoundException;

interface UserRepositoryInterface
{
    public function save(User $user): User;

    /**
     * @throws UserNotFoundException
     */
    public function findById(int $id): User;

    /**
     * @throws UserNotFoundException
     */
    public function findByEmail(string $email): ?User;

    public function findByIdOrNull(int $id): ?User;

    public function delete(User $user): void;

    public function existsByEmail(string $email): bool;

    /**
     * @return array{items: User[], total: int}
     */
    public function findWithPagination(
        int $page = 1,
        int $perPage = 10,
        ?string $search = null
    ): array;
}
