<?php

declare(strict_types=1);

namespace App\Word\Application\Command;

/**
 * Wzorzec: Command.
 *
 * Przechowuje dane potrzebne do usunięcia słowa (tutaj tylko ID).
 * Izoluje operację od logiki wykonania – logika będzie w handlerze.
 */
final class DeleteWordCommand
{
    public function __construct(public int $id) {}
}
