<?php

declare(strict_types=1);

namespace App\UserAssessment\Infrastructure\Http;

use App\UserAssessment\Application\Command\EvaluateUserLevelCommand;
use App\UserAssessment\Application\DTO\QuizAnswerDTO;
use App\UserAssessment\Application\Handler\EvaluateQuizHandler;
use App\UserAssessment\Application\Handler\EvaluateUserLevelHandler;
use App\UserAssessment\Application\Handler\GenerateQuizHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class UserAssessmentController extends AbstractController
{
    #[Route('/api/user-assessment', name: 'user_assessment_evaluate', methods: ['POST'])]
    public function __invoke(Request $request, EvaluateUserLevelHandler $evaluateUserLevelHandler): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['userId'], $data['wordHistory']) || !is_array($data['wordHistory'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }

        $command = new EvaluateUserLevelCommand(
            (int) $data['userId'],
            $data['wordHistory']
        );

        try {
            $dto = $evaluateUserLevelHandler($command);

            return $this->json([
                'level' => $dto->level->value,   // enum
                'plan' => $dto->plan,
            ]);
        } catch (Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/api/user-assessment/quiz', methods: ['POST'])]
    public function generate(GenerateQuizHandler $handler): JsonResponse
    {
        return $this->json($handler());
    }

    #[Route('/api/user-assessment/evaluate', methods: ['POST'])]
    public function evaluate(Request $request, EvaluateQuizHandler $handler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new QuizAnswerDTO(
            userId: (int) ($data['userId'] ?? 0),
            answers: $data['answers'] ?? []
        );

        $result = $handler($dto);

        return $this->json($result);
    }
}
