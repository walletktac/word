<?php

declare(strict_types=1);

namespace App\UserAssessment\Domain\Service;

use App\UserAssessment\Application\DTO\LearningPlanDTO;
use App\UserAssessment\Application\DTO\QuizAnswerDTO;

interface QuizEvaluatorInterface
{
    public function evaluate(QuizAnswerDTO $dto): LearningPlanDTO;
}
