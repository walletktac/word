<?php

declare(strict_types=1);

namespace App\UserAssessment\Infrastructure\OpenAI;

use App\UserAssessment\Application\DTO\LearningPlanDTO;
use App\UserAssessment\Application\DTO\QuizAnswerDTO;
use App\UserAssessment\Domain\Service\QuizEvaluatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class OpenAiQuizEvaluator implements QuizEvaluatorInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $openAiApiKey
    ) {}

    public function evaluate(QuizAnswerDTO $dto): LearningPlanDTO
    {
        $prompt = $this->buildPrompt($dto);

        $response = $this->httpClient->request(
            'POST',
            'https://api.openai.com/v1/chat/completions',
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->openAiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an English language level evaluator.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.7,
                ],
            ]
        );

        $data = $response->toArray();

        /** @var string $jsonString */
        $jsonString = $data['choices'][0]['message']['content'] ?? '{}';

        return LearningPlanDTO::fromJson($jsonString);
    }

    private function buildPrompt(QuizAnswerDTO $dto): string
    {
        $answersJson = json_encode($dto->answers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Na podstawie odpowiedzi użytkownika oceń jego poziom języka angielskiego zgodnie z CEFR (A1–C2).

Zwróć poziom i zaproponuj plan nauki na 7 dni jako JSON:

{
  "level": "B1",
  "plan": [
    "Dzień 1: Phrasal verbs – podstawy",
    "Dzień 2: Idiomy codzienne",
    ...
  ]
}

Odpowiedzi użytkownika:
{$answersJson}
PROMPT;
    }
}
