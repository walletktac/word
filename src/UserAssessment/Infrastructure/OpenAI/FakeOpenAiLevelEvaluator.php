<?php

declare(strict_types=1);

namespace App\UserAssessment\Infrastructure\OpenAI;

use App\UserAssessment\Application\DTO\LearningPlanDTO;
use App\UserAssessment\Domain\Enum\UserLevelEnum;
use App\UserAssessment\Domain\Service\LevelEvaluatorInterface;

class FakeOpenAiLevelEvaluator implements LevelEvaluatorInterface
{
    public function evaluate(array $wordHistory): LearningPlanDTO
    {
        return new LearningPlanDTO(
            UserLevelEnum::B1,
            [
                'Day 1: Basic vocabulary',
                'Day 2: Phrasal verbs',
                'Day 3: Grammar essentials',
                'Day 4: Listening practice',
                'Day 5: Speaking session',
                'Day 6: Vocabulary review',
                'Day 7: Progress test',
            ]
        );
    }
}
