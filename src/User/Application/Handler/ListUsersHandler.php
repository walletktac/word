<?php

declare(strict_types=1);

namespace App\User\Application\Handler;

use App\User\Application\DTO\PagedUsersDTO;
use App\User\Application\Mapper\UserMapper;
use App\User\Application\Query\ListUsersQuery;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;

final readonly class ListUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(ListUsersQuery $query): PagedUsersDTO
    {
        /** @var array{items: list<User>, total: int} $result */
        $result = $this->userRepository->findWithPagination(
            page: $query->pagination->page,
            perPage: $query->pagination->perPage,
            search: $query->search
        );

        $items = array_map(static fn (User $u) => UserMapper::toDto($u), $result['items']);

        return new PagedUsersDTO(items: $items, total: $result['total']);
    }
}
