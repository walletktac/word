<?php

declare(strict_types=1);

namespace App\UserAssessment\Application\Handler;

use App\UserAssessment\Application\Command\EvaluateUserLevelCommand;
use App\UserAssessment\Application\DTO\LearningPlanDTO;
use App\UserAssessment\Domain\Repository\UserAssessmentRepositoryInterface;
use App\UserAssessment\Infrastructure\OpenAI\FakeOpenAiLevelEvaluator;
use App\UserAssessment\Infrastructure\OpenAI\OpenAiLevelEvaluator;

final readonly class EvaluateUserLevelHandler
{
    public function __construct(
        private OpenAiLevelEvaluator $aiEvaluator,
        //        private FakeOpenAiLevelEvaluator $fakeEvaluator,
        private UserAssessmentRepositoryInterface $repository,
    ) {}

    public function __invoke(EvaluateUserLevelCommand $command): LearningPlanDTO
    {
        /** @var list<array{word: string, correct: bool}> $normalizedHistory */
        $normalizedHistory = array_values($command->wordHistory);

        $result = $this->aiEvaluator->evaluate($normalizedHistory);

        $assessment = $result->toEntity($command->userId);
        $this->repository->save($assessment);

        return $result;
    }
}
