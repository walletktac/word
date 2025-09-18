<?php

declare(strict_types=1);

namespace App\Tests\User\Application\Handler;

use App\User\Application\Handler\ListUsersHandler;
use App\User\Application\Query\ListUsersQuery;
use App\User\Application\ValueObject\Pagination;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ListUsersHandlerTest extends TestCase
{
    public function testReturnsPagedDto(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);

        $u1 = $this->createConfiguredMock(User::class, [
            'getId' => 1,
            'getEmail' => 'a@ex.com',
            'getName' => 'A',
            'getRoles' => ['ROLE_USER'],
            'getLevel' => null,
        ]);
        $u2 = $this->createConfiguredMock(User::class, [
            'getId' => 2,
            'getEmail' => 'b@ex.com',
            'getName' => 'B',
            'getRoles' => ['ROLE_ADMIN'],
            'getLevel' => 'silver',
        ]);

        $repo->expects($this->once())
            ->method('findWithPagination')
            ->with(1, 2, null)
            ->willReturn(['items' => [$u1, $u2], 'total' => 10])
        ;

        $pagination = new class(1, 2) {
            public function __construct(public int $page, public int $perPage) {}
        };

        $handler = new ListUsersHandler($repo);

        $dto = $handler(new ListUsersQuery(
            search: null,
            role: null,
            pagination: new Pagination(1, 2),
        ));

        $this->assertSame(10, $dto->total);
        $this->assertCount(2, $dto->items);
        $this->assertSame(1, $dto->items[0]->id);
        $this->assertSame('a@ex.com', $dto->items[0]->email);
        $this->assertSame('silver', $dto->items[1]->level);
    }
}
