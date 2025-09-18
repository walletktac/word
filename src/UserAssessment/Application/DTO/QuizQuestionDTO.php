<?php

declare(strict_types=1);

namespace App\UserAssessment\Application\DTO;

/**
 * @phpstan-type OptionList list<string>
 */
final readonly class QuizQuestionDTO
{
    /**
     * @param list<string> $options
     */
    public function __construct(
        public string $question,
        public array $options,
        public string $answer
    ) {}
}
