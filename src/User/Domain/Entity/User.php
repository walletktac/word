<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\User\Infrastructure\Persistence\Doctrine\UserDoctrineRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserDoctrineRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $level = null;

    /**
     * @param array<string> $roles
     */
    public function __construct(
        string $email,
        string $password,
        ?string $name = null,
        array $roles = ['ROLE_USER']
    ) {
        $this->setEmail($email);
        $this->password = $password;
        $this->name = $name;
        $this->setRoles($roles);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $email = trim($email);
        if ('' === $email) {
            throw new InvalidArgumentException('Email cannot be empty');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /** @deprecated */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique(array_map('strval', $roles)));
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        /** @var list<string> $normalized */
        $normalized = array_values(array_unique(array_map('strval', $roles)));
        $this->roles = $normalized;

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        if ('' === trim($password)) {
            throw new InvalidArgumentException('Password cannot be empty');
        }
        $this->password = $password;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function updateAssessedLevel(?string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function eraseCredentials(): void {}

    public function changePassword(string $newPassword): void
    {
        $this->setPassword($newPassword);
    }

    public function promoteToAdmin(): void
    {
        if (!$this->hasRole('ROLE_ADMIN')) {
            $this->roles[] = 'ROLE_ADMIN';
            $this->roles = array_values(array_unique($this->roles));
        }
    }

    public function demoteFromAdmin(): void
    {
        $this->roles = array_values(
            array_filter($this->roles, static fn (string $r) => 'ROLE_ADMIN' !== $r)
        );
    }
}
