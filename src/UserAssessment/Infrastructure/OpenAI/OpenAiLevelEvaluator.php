<?php

declare(strict_types=1);

namespace App\UserAssessment\Infrastructure\OpenAI;

use App\UserAssessment\Application\DTO\LearningPlanDTO;
use App\UserAssessment\Domain\Service\LevelEvaluatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class OpenAiLevelEvaluator implements LevelEvaluatorInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $openAiApiKey
    ) {}

    public function evaluate(array $wordHistory): LearningPlanDTO
    {
        //        dd($this->openAiApiKey);

        $prompt = $this->buildPrompt($wordHistory);

        $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->openAiApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an English language level evaluator.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ],
        ]);

        $data = $response->toArray();

        // Parsowanie JSON z odpowiedzi AI
        return LearningPlanDTO::fromJson($data['choices'][0]['message']['content']);
    }

    /**
     * @param array<int, array{word: string, correct: bool}> $history
     */
    private function buildPrompt(array $history): string
    {
        $json = json_encode($history, JSON_PRETTY_PRINT);

        return <<<PROMPT
Na podstawie danych poniżej oceń poziom językowy użytkownika wg CEFR (A1–C2) oraz zaproponuj 7-dniowy plan nauki. Zwróć odpowiedź jako JSON:

{
  "level": "B1",
  "plan": [
    "Dzień 1: Phrasal verbs – podstawy",
    "Dzień 2: Idiomy codzienne",
    ...
  ]
}

Dane użytkownika:
{$json}
PROMPT;
    }
}
