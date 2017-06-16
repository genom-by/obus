BEGIN;
CREATE TABLE obus(
  name VARCHAR(20) DEFAULT NULL UNIQUE,
  id_obus INTEGER PRIMARY KEY NOT NULL,
  CONSTRAINT XPKobus
    UNIQUE(id_obus)
);
CREATE TABLE pitstop_type(
  type VARCHAR(20) DEFAULT NULL UNIQUE,
  id_pittype INTEGER PRIMARY KEY NOT NULL,
  CONSTRAINT XPKpitstop_type
    UNIQUE(id_pittype)
);
CREATE TABLE station(
  name VARCHAR(40) DEFAULT NULL UNIQUE,
  shortName VARCHAR(5) DEFAULT NOT NULL,
  id_station INTEGER PRIMARY KEY NOT NULL,
  CONSTRAINT XPKstation
    UNIQUE(id_station)
);
CREATE TABLE pitstop(
  time TIMESTAMP NOT NULL,
  id_station INTEGER NOT NULL,
  id_pitstop INTEGER PRIMARY KEY NOT NULL,
  id_pittype INTEGER DEFAULT NULL,
  CONSTRAINT XPKpitstop
    UNIQUE(id_pitstop),
  CONSTRAINT fk_123
    FOREIGN KEY(id_station)
    REFERENCES station
    ON DELETE SET NULL,
  CONSTRAINT fk_234
    FOREIGN KEY(id_pittype)
    REFERENCES pitstop_type
    ON DELETE SET NULL
);
CREATE TABLE obus_pitstop(
  id_obus INTEGER NOT NULL,
  id_pitstop INTEGER NOT NULL,
  PRIMARY KEY(id_obus,id_pitstop),
  CONSTRAINT XPKobus_pitstop
    UNIQUE(id_obus,id_pitstop),
  CONSTRAINT fk_345
    FOREIGN KEY(id_pitstop)
    REFERENCES pitstop
    ON DELETE RESTRICT,
  CONSTRAINT fk_456
    FOREIGN KEY(id_obus)
    REFERENCES obus
    ON DELETE RESTRICT
);

CREATE TABLE itinerary(
    id_itin INTEGER PRIMARY KEY NOT NULL,
    name VARCHAR(20) DEFAULT NOT NULL UNIQUE,
    start_station INTEGER NOT NULL,
    start_time INTEGER NOT NULL,
  CONSTRAINT XPKitinerary
    UNIQUE(id_itin),
  CONSTRAINT fk_ToStation
    FOREIGN KEY(start_station)
    REFERENCES station
    ON DELETE RESTRICT
);

COMMIT;
