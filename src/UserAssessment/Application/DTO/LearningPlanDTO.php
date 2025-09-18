<?php

declare(strict_types=1);

namespace App\UserAssessment\Application\DTO;

use App\UserAssessment\Domain\Entity\UserAssessment;
use App\UserAssessment\Domain\Enum\UserLevelEnum;

final readonly class LearningPlanDTO
{
    public function __construct(
        public UserLevelEnum $level,
        /** @var list<string> */
        public array $plan
    ) {}

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self(
            UserLevelEnum::fromString($data['level'] ?? ''),
            is_array($data['plan'] ?? null) ? array_values($data['plan']) : []
        );
    }

    public function toEntity(int $userId): UserAssessment
    {
        return new UserAssessment(
            $userId,
            $this->level->value,
            $this->plan
        );
    }
}
