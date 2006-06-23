CREATE TABLE `tbl_modcat` (
  `id` varchar(32) UNIQUE,
  `modName` varchar(50) NOT NULL DEFAULT '',
  `description` mediumtext,
  `category` varchar(50) NOT NULL DEFAULT 'Other',
  `dateCreated` datetime DEFAULT '2006-06-06 06:06:06',
  `creatorUserId` varchar(25) DEFAULT '1',
  `dateLastModified` datetime DEFAULT NULL,
  `modifiedByUserId` varchar(25) NOT NULL DEFAULT 1,
  PRIMARY KEY  (id)
) TYPE=InnoDB ;

#
# Dumping data for table `tbl_modcat`
#
INSERT INTO tbl_modcat (id,modName,description,category) VALUES ('init_1','modulecatalogue','Categorical module manager and updater','Admin');