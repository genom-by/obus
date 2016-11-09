-- Creator:       MySQL Workbench 5.2.44/ExportSQLite plugin 2009.12.02
-- Author:        Abratchuk
-- Caption:       New Model
-- Project:       Name of the project
-- Changed:       2016-09-19 09:50
-- Created:       2016-09-19 09:50
PRAGMA foreign_keys = OFF;

-- Schema: default_schema
BEGIN;
CREATE TABLE "obus"(
  "name" VARCHAR(20) DEFAULT NULL UNIQUE,
  "id_obus" INTEGER PRIMARY KEY NOT NULL,
  CONSTRAINT "XPKobus"
    UNIQUE("id_obus")
);
CREATE TABLE "pitstop_type"(
  "type" VARCHAR(20) DEFAULT NULL UNIQUE,
  "id_pittype" INTEGER PRIMARY KEY NOT NULL,
  CONSTRAINT "XPKpitstop_type"
    UNIQUE("id_pittype")
);
CREATE TABLE "station"(
  "name" VARCHAR(40) DEFAULT NULL UNIQUE,
  "id_station" INTEGER PRIMARY KEY NOT NULL,
  CONSTRAINT "XPKstation"
    UNIQUE("id_station")
);
CREATE TABLE "pitstop"(
  "time" TIMESTAMP NOT NULL,
  "id_station" INTEGER NOT NULL,
  "id_pitstop" INTEGER PRIMARY KEY NOT NULL,
  "id_pittype" INTEGER DEFAULT NULL,
  CONSTRAINT "XPKpitstop"
    UNIQUE("id_pitstop"),
  CONSTRAINT "fk_{6ACB7A68-F8B4-49D0-966A-35FB1778560C}"
    FOREIGN KEY("id_station")
    REFERENCES "station"
    ON DELETE SET NULL,
  CONSTRAINT "fk_{98743A24-37DF-4606-BAF1-CC4AD909D086}"
    FOREIGN KEY("id_pittype")
    REFERENCES "pitstop_type"
    ON DELETE SET NULL
);
CREATE TABLE "obus_pitstop"(
  "id_obus" INTEGER NOT NULL,
  "id_pitstop" INTEGER NOT NULL,
  PRIMARY KEY("id_obus","id_pitstop"),
  CONSTRAINT "XPKobus_pitstop"
    UNIQUE("id_obus","id_pitstop"),
  CONSTRAINT "fk_{00342AAF-88D1-4537-8310-68BC60448A42}"
    FOREIGN KEY("id_pitstop")
    REFERENCES "pitstop"
    ON DELETE RESTRICT,
  CONSTRAINT "fk_{AC8BF173-3415-48AB-9253-E447A0DA961E}"
    FOREIGN KEY("id_obus")
    REFERENCES "obus"
    ON DELETE RESTRICT
);

CREATE TABLE "itinerary"(
    "id_itin" INTEGER PRIMARY KEY NOT NULL,
    "name" VARCHAR(20) DEFAULT NOT NULL UNIQUE,
    "start_station" INTEGER NOT NULL,
    "start_time" INTEGER NOT NULL,
  CONSTRAINT "XPKitinerary"
    UNIQUE("id_itin"),
  CONSTRAINT "fk_ToStation"
    FOREIGN KEY("start_station")
    REFERENCES "station"
    ON DELETE RESTRICT
);

COMMIT;
