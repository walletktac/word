<?php

declare(strict_types=1);

namespace App\UserAssessment\Infrastructure\OpenAI;

use App\UserAssessment\Application\DTO\QuizDTO;
use App\UserAssessment\Domain\Service\QuizGeneratorInterface;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAiQuizGenerator implements QuizGeneratorInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $openAiApiKey
    ) {}

    public function generate(): QuizDTO
    {
        $prompt = $this->buildPrompt();

        $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->openAiApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo', // lub gpt-3.5-turbo jeśli chcesz
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.8,
            ],
        ]);

        $data = $response->toArray();

        $jsonString = $data['choices'][0]['message']['content'] ?? null;
        if (!$jsonString) {
            throw new RuntimeException('No content received from OpenAI.');
        }

        return QuizDTO::fromJson($jsonString);
    }

    private function buildPrompt(): string
    {
        return <<<'PROMPT'
Wygeneruj quiz oceniający poziom języka angielskiego użytkownika. Zwróć 5 pytań w formacie JSON. Każde pytanie powinno zawierać:

- "question": treść pytania,
- "options": lista 4 odpowiedzi (A, B, C, D),
- "answer": prawidłowa odpowiedź (np. "B")

Odpowiedz wyłącznie w formacie JSON:
[
  {
    "question": "What is the past tense of 'go'?",
    "options": ["goed", "went", "goes", "gone"],
    "answer": "went"
  },
  ...
]
PROMPT;
    }
}
