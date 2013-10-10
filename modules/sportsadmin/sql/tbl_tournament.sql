<?php

  $sqldata[]="CREATE TABLE tbl_tournament(
  id varchar(32) NOT NULL,
  name varchar(32) NOT NULL,
  sponsorName varchar(32),
  creator varchar(25) NOT NULL, 
  startDate date,
  endDate date,
  sportId varchar(32),
  updated TIMESTAMP (14) NOT NULL,
  PRIMARY KEY(id),
  CONSTRAINT `FK_sports_userId` FOREIGN KEY (`creator`) REFERENCES `tbl_users` (`userId`)
  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sports_sportId` FOREIGN KEY (`sportId`) REFERENCES `tbl_sports` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE
  
  
)TYPE=InnoDB
";



?>