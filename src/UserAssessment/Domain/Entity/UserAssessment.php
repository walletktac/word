<?php

declare(strict_types=1);

namespace App\UserAssessment\Domain\Entity;

use App\UserAssessment\Infrastructure\Persistence\Doctrine\UserAssessmentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAssessmentRepository::class)]
#[ORM\Table(name: 'user_assessment')]
class UserAssessment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Column(length: 10)]
    private string $level;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $plan;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $assessedAt;

    /**
     * @param list<string> $plan
     */
    public function __construct(
        int $userId,
        string $level,
        array $plan,
        ?DateTimeImmutable $assessedAt = null
    ) {
        $this->userId = $userId;
        $this->level = $level;
        $this->plan = $plan;
        $this->assessedAt = $assessedAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @return list<string>
     */
    public function getPlan(): array
    {
        return $this->plan;
    }

    public function getAssessedAt(): DateTimeImmutable
    {
        return $this->assessedAt;
    }
}
