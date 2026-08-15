<?php

declare(strict_types=1);

namespace Fulll\Domain\Fleet\Exception;

use Fulll\Domain\Fleet\FleetId;

final class FleetNotFoundException extends \RuntimeException
{
    public function __construct(FleetId $fleetId)
    {
        parent::__construct(sprintf("Fleet '%s' does not exist.", $fleetId->value()));
    }
}
