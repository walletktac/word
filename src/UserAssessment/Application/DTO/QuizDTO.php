<?php

declare(strict_types=1);

namespace App\UserAssessment\Application\DTO;

final readonly class QuizDTO
{
    /** @param list<QuizQuestionDTO> $questions */
    public function __construct(public array $questions) {}

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $questions = array_map(
            fn (array $q) => new QuizQuestionDTO(
                $q['question'] ?? '',
                $q['options'] ?? [],
                $q['answer'] ?? ''
            ),
            $data
        );

        return new self(array_values($questions));
    }
}
