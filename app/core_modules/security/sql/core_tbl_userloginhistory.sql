CREATE TABLE `tbl_userloginhistory` (
  `id` varchar(32) NOT NULL,
  `userId` char(25) NOT NULL default '0',
  `lastLoginDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `userId` (`userId`)
) TYPE=InnoDB  ROW_FORMAT=FIXED COMMENT='Used to hold the loginhistory of a user';