#
# Table structure for table `tbl_loggedinusers`
#

CREATE TABLE `tbl_loggedinusers` (
   id int(11) NOT NULL auto_increment,
  `userId` varchar(25) NOT NULL default '0',
  `ipAddress` varchar(100) NOT NULL default '',
  `sessionId` varchar(100) NOT NULL default '',
  `whenLoggedIn` datetime NOT NULL default '0000-00-00 00:00:00',
  `WhenLastActive` datetime NOT NULL default '0000-00-00 00:00:00',
  `isInvisible` tinyint(1) NOT NULL default '0',
  `coursecode` varchar(100) NOT NULL default '',
  `themeUsed` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB  COMMENT='This table is used to maintain state and enable communication';