CREATE TABLE [station2] (
  [name] VARCHAR(40) DEFAULT NULL, 
  [id_station] INTEGER NOT NULL PRIMARY KEY CONSTRAINT [XPKstation] UNIQUE, 
  [shortName] CHAR, 
  [uid] integer NOT NULL
  CONSTRAINT [fk_ToUser] REFERENCES [user]([id_user]) ON DELETE CASCADE,
  CONSTRAINT [uniq_st_name] UNIQUE([name],[uid]) ON CONFLICT FAIL )
  
  INSERT INTO station2 SELECT name, id_station, shortName, uid FROM station
  
  DROP TABLE station
  
  ALTER TABLE station2 RENAME TO station


CREATE TABLE [obus2] (
  [id_obus] INTEGER NOT NULL PRIMARY KEY CONSTRAINT [XPKobus] UNIQUE, 
  [name] VARCHAR(20) NOT NULL, 
  [uid] integer NOT NULL CONSTRAINT [fk_ToUser] REFERENCES [user]([id_user]) ON DELETE CASCADE,
  CONSTRAINT [uniq_ob_name] UNIQUE([name],[uid]) ON CONFLICT FAIL )
  
  INSERT INTO obus2 SELECT id_obus, name, uid FROM obus
  
  DROP TABLE obus
  
  ALTER TABLE obus2 RENAME TO obus