<?php

declare(strict_types=1);

namespace App\UserAssessment\Infrastructure\EventListener;

use App\UserAssessment\Domain\Event\UserAssessedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
readonly class LogUserAssessedListener
{
    public function __construct(private LoggerInterface $logger) {}

    public function __invoke(UserAssessedEvent $event): void
    {
        $this->logger->info(sprintf(
            'User #%d was assessed with level "%s".',
            $event->user->getId(),
            $event->level
        ));
    }
}
