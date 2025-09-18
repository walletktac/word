<?php

declare(strict_types=1);

namespace App\Tests\User\Application\Handler;

use App\User\Application\Handler\GetUserHandler;
use App\User\Application\Query\GetUserQuery;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class GetUserHandlerTest extends TestCase
{
    public function testReturnsDtoWhenFound(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);

        $userMock = $this->createConfiguredMock(User::class, [
            'getId' => 5,
            'getEmail' => 'trzcinskikar@gmail.com',
            'getName' => 'Karol',
            'getRoles' => ['ROLE_USER'],
            'getLevel' => null,
        ]);

        $repo->expects($this->once())
            ->method('findById')
            ->with(5)
            ->willReturn($userMock)
        ;

        $handler = new GetUserHandler($repo);
        $dto = $handler(new GetUserQuery(5));

        $this->assertSame(5, $dto->id);
        $this->assertSame('trzcinskikar@gmail.com', $dto->email);
    }

    public function testThrowsWhenMissing(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);

        $repo->method('findById')
            ->with(999)
            ->willThrowException(UserNotFoundException::withId(999))
        ;

        $handler = new GetUserHandler($repo);

        $this->expectException(UserNotFoundException::class);
        $handler(new GetUserQuery(999));
    }
}
