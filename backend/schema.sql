CREATE TABLE IF NOT EXISTS fleets (
    id CHAR(32) PRIMARY KEY,
    user_id TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS vehicles (
    plate_number TEXT PRIMARY KEY,
    latitude DOUBLE PRECISION,
    longitude DOUBLE PRECISION,
    altitude DOUBLE PRECISION
);

CREATE TABLE IF NOT EXISTS fleet_vehicles (
    fleet_id CHAR(32) NOT NULL REFERENCES fleets (id),
    plate_number TEXT NOT NULL REFERENCES vehicles (plate_number),
    PRIMARY KEY (fleet_id, plate_number)
);
