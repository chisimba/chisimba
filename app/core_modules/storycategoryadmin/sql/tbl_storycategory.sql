<?php
$sqldata[]="CREATE TABLE `tbl_storycategory` (
  `id` varchar(32) NOT NULL,
  `category` varchar(32) NOT NULL,                                                                                                                                                                                                                                                                                                                                                          
  `title` varchar(250) default NULL,
  `dateCreated` datetime default NULL,
  `creatorId` varchar(32) default NULL,
  `dateModified` datetime default NULL,
  `modifierId` varchar(32) default NULL,
  `modified` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB ROW_FORMAT=DYNAMIC  COMMENT='Table to hold story categories'";



$sqldata[]="INSERT INTO `tbl_storycategory` VALUES 
 ('init_1','postlogin','Post login stories','2005-03-15 08:46:25','1','2005-03-15 10:05:08','1',20050315100508) , 
 ('init_2','prelogin','Prelogin public stories','2005-03-15 09:34:35','1',NULL,NULL,20050315093435) , 
 ('init_3','preloginfooter','Story for prelogin footer','2005-03-15 09:35:41','1',NULL,NULL,20050315093541) , 
 ('init_4','preloginfooter','Story for prelogin footer','2005-03-15 09:37:12','1',NULL,NULL,20050315093712)";

?>