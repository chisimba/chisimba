################################### System config ##############################
#
# Table structure for table `tbl_sysconfig_properties`
#

CREATE TABLE `tbl_sysconfig_properties` (
  `id` varchar(32) NOT NULL default '',
  `pmodule` varchar(25) NOT NULL default '',
  `pname` varchar(32) NOT NULL default '',
  `pvalue` varchar(255) NOT NULL default '',
  `creatorId` varchar(25) default NULL,
  `dateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifierId` varchar(25) default NULL,
  `dateModified` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB ;