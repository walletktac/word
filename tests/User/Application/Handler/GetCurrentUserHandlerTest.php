<?php

declare(strict_types=1);

namespace App\Tests\User\Application\Handler;

use App\User\Application\Handler\GetCurrentUserHandler;
use App\User\Application\Query\GetCurrentUserQuery;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class GetCurrentUserHandlerTest extends TestCase
{
    public function testReturnsDtoWhenUserFound(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);

        $userMock = $this->createConfiguredMock(User::class, [
            'getId' => 10,
            'getEmail' => 'trzcinskikar@gmail.com',
            'getName' => 'Karol',
            'getRoles' => ['ROLE_USER'],
            'getLevel' => 'B1',
        ]);

        $repo->expects($this->once())
            ->method('findByEmail')
            ->with('trzcinskikar@gmail.com')
            ->willReturn($userMock)
        ;

        $handler = new GetCurrentUserHandler($repo);

        $dto = $handler(new GetCurrentUserQuery('trzcinskikar@gmail.com'));

        $this->assertSame(10, $dto->id);
        $this->assertSame('trzcinskikar@gmail.com', $dto->email);
        $this->assertSame('Karol', $dto->name);
        $this->assertSame(['ROLE_USER'], $dto->roles);
        $this->assertSame('B1', $dto->level);
    }

    public function testThrowsWhenUserNotFound(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $repo->method('findByEmail')->willReturn(null);

        $handler = new GetCurrentUserHandler($repo);

        $this->expectException(UserNotFoundException::class);
        $handler(new GetCurrentUserQuery('missing@example.com'));
    }
}
