<?php
$sqldata[]="CREATE TABLE `tbl_forum_settings` (
  `id` varchar(32) NOT NULL default '',
  `item` varchar(5) NOT NULL default '',
  `item_id` varchar(32) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `item` (`item`,`item_id`)
) TYPE=InnoDB;";

?>