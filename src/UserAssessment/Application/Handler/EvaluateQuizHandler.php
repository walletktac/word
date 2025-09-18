<?php

declare(strict_types=1);

namespace App\UserAssessment\Application\Handler;

use App\User\Domain\Repository\UserRepositoryInterface;
use App\UserAssessment\Application\DTO\LearningPlanDTO;
use App\UserAssessment\Application\DTO\QuizAnswerDTO;
use App\UserAssessment\Domain\Event\UserAssessedEvent;
use App\UserAssessment\Domain\Repository\UserAssessmentRepositoryInterface;
use App\UserAssessment\Domain\Service\QuizEvaluatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class EvaluateQuizHandler
{
    public function __construct(
        private QuizEvaluatorInterface $evaluator,
        private UserAssessmentRepositoryInterface $assessmentRepository,
        private UserRepositoryInterface $userRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function __invoke(QuizAnswerDTO $dto): LearningPlanDTO
    {
        $result = $this->evaluator->evaluate($dto);

        $assessment = $result->toEntity($dto->userId);
        $this->assessmentRepository->save($assessment);

        $user = $this->userRepository->findById($dto->userId);
        $user->updateAssessedLevel($result->level->value);

        $this->userRepository->save($user);

        $this->eventDispatcher->dispatch(new UserAssessedEvent(
            user: $user,
            level: $result->level->value,
            plan: $result->plan
        ));

        return $result;
    }
}
