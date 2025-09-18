<?php

declare(strict_types=1);

namespace App\Tests\User\Application\Handler;

use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Handler\CreateUserHandler;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class CreateUserHandlerTest extends TestCase
{
    public function testCreatesAndReturnsDto(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $repo->expects($this->once())
            ->method('existsByEmail')
            ->with('trzcinskikar@gmail.com')
            ->willReturn(false)
        ;

        $hasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), 'secret')
            ->willReturn('hashed123')
        ;

        $saved = $this->createConfiguredMock(User::class, [
            'getId' => 10,
            'getEmail' => 'trzcinskikar@gmail.com',
            'getName' => 'Karol',
            'getRoles' => ['ROLE_USER'],
            'getLevel' => null,
        ]);

        $repo->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class))
            ->willReturn($saved)
        ;

        $handler = new CreateUserHandler($repo, $hasher);

        $dto = $handler(new CreateUserCommand(
            email: 'trzcinskikar@gmail.com',
            password: 'secret',
            name: 'Karol',
            roles: ['ROLE_USER']
        ));

        $this->assertSame(10, $dto->id);
        $this->assertSame('trzcinskikar@gmail.com', $dto->email);
        $this->assertSame('Karol', $dto->name);
        $this->assertSame(['ROLE_USER'], $dto->roles);
    }

    public function testThrowsWhenEmailExists(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $repo->method('existsByEmail')->with('trzcinskikar@gmail.com')->willReturn(true);

        $handler = new CreateUserHandler($repo, $hasher);

        $this->expectException(UserAlreadyExistsException::class);

        $handler(new CreateUserCommand(
            email: 'trzcinskikar@gmail.com',
            password: 'x',
            name: null,
            roles: ['ROLE_USER']
        ));
    }
}
