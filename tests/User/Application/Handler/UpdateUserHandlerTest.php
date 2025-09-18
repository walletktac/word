<?php

declare(strict_types=1);

namespace App\Tests\User\Application\Handler;

use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\Handler\UpdateUserHandler;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class UpdateUserHandlerTest extends TestCase
{
    public function testUpdatesAndReturnsDto(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $current = $this->createConfiguredMock(User::class, [
            'getId' => 7,
            'getEmail' => 'old@example.com',
            'getName' => 'Old',
            'getRoles' => ['ROLE_USER'],
        ]);

        $repo->expects($this->once())
            ->method('findById')
            ->with(7)
            ->willReturn($current)
        ;

        $repo->expects($this->once())
            ->method('findByEmail')
            ->with('new@example.com')
            ->willReturn(null)
        ;

        $hasher->expects($this->once())
            ->method('hashPassword')
            ->with($current, 'newpass')
            ->willReturn('hashed-new')
        ;

        $saved = $this->createConfiguredMock(User::class, [
            'getId' => 7,
            'getEmail' => 'new@example.com',
            'getName' => 'New',
            'getRoles' => ['ROLE_ADMIN'],
            'getLevel' => null,
        ]);

        $repo->expects($this->once())
            ->method('save')
            ->with($current)
            ->willReturn($saved)
        ;

        $handler = new UpdateUserHandler($repo, $hasher);

        $dto = $handler(new UpdateUserCommand(
            id: 7,
            email: 'new@example.com',
            name: 'New',
            roles: ['ROLE_ADMIN'],
            password: 'newpass'
        ));

        $this->assertSame(7, $dto->id);
        $this->assertSame('new@example.com', $dto->email);
        $this->assertSame('New', $dto->name);
        $this->assertSame(['ROLE_ADMIN'], $dto->roles);
    }

    public function testThrowsWhenUserMissing(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $repo->method('findById')->with(123)->willThrowException(UserNotFoundException::withId(123));

        $handler = new UpdateUserHandler($repo, $hasher);

        $this->expectException(UserNotFoundException::class);

        $handler(new UpdateUserCommand(
            id: 123,
            email: null,
            name: null,
            roles: null,
            password: null
        ));
    }

    public function testThrowsWhenEmailTakenByAnotherUser(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $current = $this->createConfiguredMock(User::class, [
            'getId' => 5,
            'getEmail' => 'me@example.com',
            'getRoles' => ['ROLE_USER'],
        ]);
        $other = $this->createConfiguredMock(User::class, [
            'getId' => 9,
            'getEmail' => 'taken@example.com',
        ]);

        $repo->method('findById')
            ->with(5)
            ->willReturn($current)
        ;

        $repo->method('findByEmail')
            ->with('taken@example.com')
            ->willReturn($other)
        ;

        $handler = new UpdateUserHandler($repo, $hasher);

        $this->expectException(UserAlreadyExistsException::class);

        $handler(new UpdateUserCommand(
            id: 5,
            email: 'taken@example.com',
            name: null,
            roles: null,
            password: null
        ));
    }
}
