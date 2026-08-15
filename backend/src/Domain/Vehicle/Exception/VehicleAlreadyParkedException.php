<?php

declare(strict_types=1);

namespace Fulll\Domain\Vehicle\Exception;

use Fulll\Domain\Vehicle\PlateNumber;

final class VehicleAlreadyParkedException extends \DomainException
{
    public function __construct(PlateNumber $plateNumber)
    {
        parent::__construct(sprintf("Vehicle '%s' is already parked at this location.", $plateNumber->value()));
    }
}
