<?php
$sqldata[]="CREATE TABLE `tbl_forum_attachments` (
  `id` varchar(32) NOT NULL default '',
  `forum_id` varchar(32) NOT NULL default '',
  `fileId` varchar(32) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `userId` varchar(32) NOT NULL default '',
  `dateLastUpdated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB   COMMENT='Files that users upload to the forum';";
?>