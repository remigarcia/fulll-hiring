<?php

declare(strict_types=1);

namespace Fulll\App\Fleet\Command;

final readonly class CreateFleetCommand
{
    public function __construct(
        public string $userId,
    ) {}
}
