Alter table destination
add uid integer CONSTRAINT [fk_ToUser] REFERENCES [user]([id_user]) ON DELETE CASCADE

Alter table itinerary
add uid integer CONSTRAINT [fk_ToUser] REFERENCES [user]([id_user]) ON DELETE CASCADE

Alter table obus
add uid integer CONSTRAINT [fk_ToUser] REFERENCES [user]([id_user]) ON DELETE CASCADE

Alter table sequences
add uid integer CONSTRAINT [fk_ToUser] REFERENCES [user]([id_user]) ON DELETE CASCADE

Alter table station
add uid integer CONSTRAINT [fk_ToUser] REFERENCES [user]([id_user]) ON DELETE CASCADE

update destination set uid=1
update itinerary set uid=1
update obus set uid=1
update sequences set uid=1
update station set uid=1