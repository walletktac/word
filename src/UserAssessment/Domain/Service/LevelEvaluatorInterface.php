<?php

declare(strict_types=1);

namespace App\UserAssessment\Domain\Service;

use App\UserAssessment\Application\DTO\LearningPlanDTO;

interface LevelEvaluatorInterface
{
    /**
     * @param array<int, array{word: string, correct: bool}> $wordHistory
     */
    public function evaluate(array $wordHistory): LearningPlanDTO;
}
