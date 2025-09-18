<?php

declare(strict_types=1);

namespace App\UserAssessment\Domain\Repository;

use App\UserAssessment\Domain\Entity\UserAssessment;

interface UserAssessmentRepositoryInterface
{
    public function save(UserAssessment $assessment): void;

    public function findLatestByUserId(int $userId): ?UserAssessment;
}
