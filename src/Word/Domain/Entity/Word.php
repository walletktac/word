<?php

declare(strict_types=1);

namespace App\Word\Domain\Entity;

use App\Word\Infrastructure\Persistence\Doctrine\WordDoctrineRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Wzorzec: Entity (Encja domenowa).
 *
 * Reprezentuje słowo języka angielskiego w kontekście domeny nauki języka.
 * Zawiera dane, reguły oraz metody związane bezpośrednio z obiektem "słowo".
 *
 * Główne cechy:
 * - Posiada tożsamość (`id`) — encja, nie obiekt-wartość (Value Object).
 * - Przechowuje dane takie jak definicja, poziom, znaczenie, itd.
 * - Implementuje proste zasady walidacji (np. pusty headword).
 * - Korzysta z adnotacji Doctrine ORM do mapowania na bazę danych.
 * - Wspiera aktualizację znacznika czasu przez Doctrine Lifecycle Callback (`PreUpdate`).
 *
 * Jest to centralny obiekt domenowy dla kontekstu nauki słownictwa.
 */
#[ORM\Entity(repositoryClass: WordDoctrineRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Word
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['word:read', 'admin:word:list', 'admin:word:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['word:read', 'admin:word:list', 'admin:word:write'])]
    private string $headword;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['word:read', 'admin:word:list', 'admin:word:write'])]
    private ?string $pos = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['word:read', 'admin:word:list', 'admin:word:write'])]
    private ?string $phonetic = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['word:read', 'admin:word:list', 'admin:word:write'])]
    private ?string $definition = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['word:read', 'admin:word:list', 'admin:word:write'])]
    private ?string $translation = null;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    #[Groups(['word:read', 'admin:word:list', 'admin:word:write'])]
    private array $examples = [];

    #[ORM\Column(length: 4, nullable: true)]
    #[Groups(['word:read', 'admin:word:list', 'admin:word:write'])]
    private ?string $level = null; // A1–C2

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    #[Groups(['word:read', 'admin:word:list', 'admin:word:write'])]
    private array $tags = [];

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['admin:word:list', 'admin:word:write'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['admin:word:list', 'admin:word:write'])]
    private DateTimeImmutable $updatedAt;

    public function __construct(string $headword)
    {
        $headword = trim($headword);
        if ('' === $headword) {
            throw new InvalidArgumentException('Headword must not be empty.');
        }
        $now = new DateTimeImmutable();
        $this->headword = $headword;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    // --- getters/setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeadword(): string
    {
        return $this->headword;
    }

    public function setHeadword(string $headword): void
    {
        $headword = trim($headword);
        if ('' === $headword) {
            throw new InvalidArgumentException('Headword must not be empty.');
        }
        $this->headword = $headword;
    }

    public function getPos(): ?string
    {
        return $this->pos;
    }

    public function setPos(?string $pos): self
    {
        $this->pos = $pos;

        return $this;
    }

    public function getPhonetic(): ?string
    {
        return $this->phonetic;
    }

    public function setPhonetic(?string $phonetic): self
    {
        $this->phonetic = $phonetic;

        return $this;
    }

    public function getDefinition(): ?string
    {
        return $this->definition;
    }

    public function setDefinition(?string $definition): self
    {
        $this->definition = $definition;

        return $this;
    }

    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    public function setTranslation(?string $translation): self
    {
        $this->translation = $translation;

        return $this;
    }

    /** @return list<string> */
    public function getExamples(): array
    {
        return $this->examples;
    }

    /** @param list<string> $examples */
    public function setExamples(array $examples): self
    {
        $this->examples = $examples;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): self
    {
        $this->level = $level;

        return $this;
    }

    /** @return list<string> */
    public function getTags(): array
    {
        return $this->tags;
    }

    /** @param list<string> $tags */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
