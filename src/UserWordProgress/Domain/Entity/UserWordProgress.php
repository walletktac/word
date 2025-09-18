<?php

declare(strict_types=1);

namespace App\UserWordProgress\Domain\Entity;

use App\User\Domain\Entity\User;
use App\UserWordProgress\Domain\Enum\ReviewStatus;
use App\Word\Domain\Entity\Word;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'user_word',
    uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_user_word', columns: ['user_id', 'word_id'])],
    indexes: [
        new ORM\Index(name: 'idx_user_due', columns: ['user_id', 'due_at']),
        new ORM\Index(name: 'idx_status', columns: ['status']),
    ]
)]
#[ORM\HasLifecycleCallbacks]
class UserWordProgress
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Word $word;

    #[ORM\Column(enumType: ReviewStatus::class)]
    private ReviewStatus $status = ReviewStatus::NEW;

    #[ORM\Column(type: 'float')]
    private float $ef = 2.5;

    #[ORM\Column(type: 'integer')]
    private int $intervalDays = 0;

    #[ORM\Column(type: 'integer')]
    private int $reps = 0;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $dueAt = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $lastResult = null; // 0–5

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $updatedAt;

    public function __construct(User $user, Word $word)
    {
        $this->user = $user;
        $this->word = $word;
        $this->updatedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    // --- metody domenowe ---
    public function review(int $grade, DateTimeImmutable $now = new DateTimeImmutable()): void
    {
        // prosta wersja SM-2
        $this->lastResult = $grade;
        ++$this->reps;

        $q = max(0, min(5, $grade));
        $this->ef = max(1.3, $this->ef + (0.1 - (5 - $q) * (0.08 + (5 - $q) * 0.02)));

        if ($q < 3) {
            $this->intervalDays = 1;
            $this->status = ReviewStatus::LEARNING;
        } else {
            if (1 == $this->reps) {
                $this->intervalDays = 1;
            } elseif (2 == $this->reps) {
                $this->intervalDays = 6;
            } else {
                $this->intervalDays = (int) round($this->intervalDays * $this->ef);
            }
            if ($this->intervalDays > 21) {
                $this->status = ReviewStatus::LEARNED;
            }
        }
        $this->dueAt = $now->modify("+{$this->intervalDays} days");
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getWord(): Word
    {
        return $this->word;
    }

    public function setWord(Word $word): void
    {
        $this->word = $word;
    }

    public function getStatus(): ReviewStatus
    {
        return $this->status;
    }

    public function setStatus(ReviewStatus $status): void
    {
        $this->status = $status;
    }

    public function getEf(): float
    {
        return $this->ef;
    }

    public function setEf(float $ef): void
    {
        $this->ef = $ef;
    }

    public function getInterval(): int
    {
        return $this->intervalDays;
    }

    public function setInterval(int $days): void
    {
        $this->intervalDays = $days;
    }

    public function getReps(): int
    {
        return $this->reps;
    }

    public function setReps(int $reps): void
    {
        $this->reps = $reps;
    }

    public function getDueAt(): ?DateTimeImmutable
    {
        return $this->dueAt;
    }

    public function setDueAt(?DateTimeImmutable $dueAt): void
    {
        $this->dueAt = $dueAt;
    }

    public function getLastResult(): ?int
    {
        return $this->lastResult;
    }

    public function setLastResult(?int $lastResult): void
    {
        $this->lastResult = $lastResult;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
