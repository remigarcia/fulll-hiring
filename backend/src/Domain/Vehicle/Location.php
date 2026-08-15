<?php

declare(strict_types=1);

namespace Fulll\Domain\Vehicle;

final class Location
{
    public function __construct(
        private readonly float $latitude,
        private readonly float $longitude,
        private readonly ?float $altitude = null,
    ) {
        if ($latitude < -90.0 || $latitude > 90.0) {
            throw new \InvalidArgumentException(sprintf('Latitude must be between -90 and 90, got %s.', $latitude));
        }
        if ($longitude < -180.0 || $longitude > 180.0) {
            throw new \InvalidArgumentException(sprintf('Longitude must be between -180 and 180, got %s.', $longitude));
        }
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function altitude(): ?float
    {
        return $this->altitude;
    }

    public function equals(self $other): bool
    {
        return $this->latitude === $other->latitude
            && $this->longitude === $other->longitude
            && $this->altitude === $other->altitude;
    }
}
