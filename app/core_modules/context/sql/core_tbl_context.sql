################################### Context ##############################
#
# Table structure for table `tbl_context`
#

CREATE TABLE `tbl_context` (
  `id` varchar(32) NOT NULL default '',
  `contextCode` varchar(255) NOT NULL default '',
  `title` TEXT NOT NULL default '',
  `menutext` varchar(255) default NULL,
  `about` TEXT default NULL,
  `userid` varchar(255) NOT NULL default '',
  `dateCreated` date default NULL,
  `isClosed` int(11) default NULL,
  `isActive` int(11) default NULL,
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY  (`id`,`contextCode`),
  KEY `contextCode` (`contextCode`)

) TYPE=InnoDB ;