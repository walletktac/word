<?php

declare(strict_types=1);

namespace App\Tests\User\Application\Handler;

use App\User\Application\Command\DeleteUserCommand;
use App\User\Application\Handler\DeleteUserHandler;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class DeleteUserHandlerTest extends TestCase
{
    public function testDeletesWhenUserExists(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);

        $user = $this->createConfiguredMock(User::class, [
            'getId' => 3,
        ]);

        $repo->method('findById')->with(3)->willReturn($user);
        $repo->expects($this->once())->method('delete')->with($user);

        $handler = new DeleteUserHandler($repo);
        $handler(new DeleteUserCommand(3));

        $this->addToAssertionCount(1);
    }

    public function testThrowsWhenUserMissing(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $repo->method('findById')
            ->with(404)
            ->willThrowException(UserNotFoundException::withId(404))
        ;

        $handler = new DeleteUserHandler($repo);

        $this->expectException(UserNotFoundException::class);
        $handler(new DeleteUserCommand(404));
    }
}
