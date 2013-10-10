<?php

  $sqldata[]="CREATE TABLE tbl_scores(
  id varchar(32) NOT NULL default '',
  sportId varchar(32),
  playerId varchar(32) NOT NULL default '',
  fixtureId varchar(32),
  tournamentId varchar(32) default '',
  teamId varchar(32) NOT NULL default '',
  time varchar(32),
  enteredBy varchar(32) NOT NULL default '',
  creationDate date,
  updatedBy varchar(32),
  PRIMARY KEY(id),
  FOREIGN KEY (sportId)
  REFERENCES tbl_sports(id) 
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (tournamentId)
  REFERENCES tbl_tournament(id) 
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (fixtureId)
  REFERENCES tbl_fixture(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  foreign key (enteredBy)
  REFERENCES tbl_user 
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (teamId)
  REFERENCES tbl_team(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (playerId)
  REFERENCES tbl_player(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE
 
)TYPE=InnoDB
";


?>