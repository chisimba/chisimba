<?
$sqldata[]="CREATE TABLE `tbl_chat_users` (
  `id` varchar(32) NOT NULL default '',
  `username` varchar(20) default NULL,
  `contextId` bigint(20) default NULL,  
  `start` bigint(20) default NULL,
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB ROW_FORMAT=DYNAMIC;
";
?>
