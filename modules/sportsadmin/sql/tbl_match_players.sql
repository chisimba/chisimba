<?php

  $sqldata[]="CREATE TABLE tbl_match_players (
  id varchar(32) NOT NULL default '',
  teamId varchar(32) NOT NULL default '',
  playerId varchar(32) NOT NULL default '',
  tournamentId varchar(32), 
  fixtureId varchar (32) NOT NULL default '',
  goals int(32),
  sportId varchar(32), 
  PRIMARY KEY(id),
  position varchar(32),
  FOREIGN KEY (sportId)
  REFERENCES tbl_sports(id) 
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (playerId)
  REFERENCES tbl_player(id) 
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY(fixtureId)
  REFERENCES tbl_fixtures(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE  
  
)TYPE=InnoDB";


?>