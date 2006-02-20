<?
  $sqldata[]="CREATE TABLE `tbl_context_temp` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` varchar(32) default NULL,
  `filetype` varchar(255) default NULL,
  `name` varchar(255) default NULL,  
  `size` int(255) default NULL,
  `filedata` longblob,
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB AUTO_INCREMENT=1 ;
";
?>