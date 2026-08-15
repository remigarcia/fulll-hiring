<?php

declare(strict_types=1);

namespace Fulll\Domain\Fleet\Exception;

use Fulll\Domain\Fleet\FleetId;
use Fulll\Domain\Vehicle\PlateNumber;

final class VehicleNotRegisteredException extends \DomainException
{
    public function __construct(PlateNumber $plateNumber, FleetId $fleetId)
    {
        parent::__construct(sprintf(
            "Vehicle '%s' is not registered into fleet '%s'.",
            $plateNumber->value(),
            $fleetId->value(),
        ));
    }
}
