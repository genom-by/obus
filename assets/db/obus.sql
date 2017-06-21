
CREATE TABLE obus (
       name                 VARCHAR(20),
       id_obus              INTEGER NOT NULL
);

CREATE UNIQUE INDEX XPKobus ON obus
(
       id_obus
);


ALTER TABLE obus
       ADD PRIMARY KEY (id_obus);


CREATE TABLE obus_pitstop (
       id_obus              INTEGER NOT NULL,
       id_pitstop           INTEGER NOT NULL
);

CREATE UNIQUE INDEX XPKobus_pitstop ON obus_pitstop
(
       id_obus,
       id_pitstop
);


ALTER TABLE obus_pitstop
       ADD PRIMARY KEY (id_obus, id_pitstop);


CREATE TABLE pitstop (
       time                 TIMESTAMP,
       id_station           INTEGER NOT NULL,
       id_pitstop           INTEGER NOT NULL,
       id_pittype           INTEGER
);

CREATE UNIQUE INDEX XPKpitstop ON pitstop
(
       id_pitstop
);


ALTER TABLE pitstop
       ADD PRIMARY KEY (id_pitstop);


CREATE TABLE pitstop_type (
       type                 VARCHAR(20),
       id_pittype           INTEGER NOT NULL
);

CREATE UNIQUE INDEX XPKpitstop_type ON pitstop_type
(
       id_pittype
);


ALTER TABLE pitstop_type
       ADD PRIMARY KEY (id_pittype);


CREATE TABLE station (
       name                 VARCHAR(40),
       id_station           INTEGER NOT NULL
);

CREATE UNIQUE INDEX XPKstation ON station
(
       id_station
);


ALTER TABLE station
       ADD PRIMARY KEY (id_station);


ALTER TABLE obus_pitstop
       ADD FOREIGN KEY (id_pitstop)
                             REFERENCES pitstop
                             ON DELETE RESTRICT;


ALTER TABLE obus_pitstop
       ADD FOREIGN KEY (id_obus)
                             REFERENCES obus
                             ON DELETE RESTRICT;


ALTER TABLE pitstop
       ADD FOREIGN KEY (id_station)
                             REFERENCES station
                             ON DELETE SET NULL;


ALTER TABLE pitstop
       ADD FOREIGN KEY (id_pittype)
                             REFERENCES pitstop_type
                             ON DELETE SET NULL;

CREATE TABLE "user"(
  "name" VARCHAR(30) DEFAULT NULL UNIQUE,  
  "pwdHash" VARCHAR(90) DEFAULT NULL UNIQUE,  
  "email" VARCHAR(90) DEFAULT NULL UNIQUE,
  "id_user" INTEGER PRIMARY KEY NOT NULL,
  CONSTRAINT "XPKuser" UNIQUE("id_user"),  
  CONSTRAINT "XPKuserName" UNIQUE("name")
);