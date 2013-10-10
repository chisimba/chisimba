<?php

  $sqldata[]="CREATE TABLE tbl_fixtures (
  id varchar(32) NOT NULL default '',
  team_A varchar(32) NOT NULL default '',
  team_B varchar(32) NOT NULL default '',
  tournamentId varchar(32) NOT NULL default '',
  place varchar(32) NOT NULL default '',
  matchDate datetime,
  sportId varchar(32),
   updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (tournamentId)
  REFERENCES tbl_tournament(id) 
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY(team_A)
  REFERENCES tbl_team(id) 
  ON DELETE CASCADE 
  ON UPDATE CASCADE,
  FOREIGN KEY(team_B)
  REFERENCES tbl_team(id) 
  ON DELETE CASCADE 
  ON UPDATE CASCADE,
  FOREIGN KEY(sportId)
  REFERENCES tbl_sport(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE  
  
)TYPE=InnoDB
";


?>