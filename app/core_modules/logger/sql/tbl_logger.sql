<?php
/* 
The table for the event log. The fields are:
 id - The framework generated primary key
 userId - The userId of the currently logged in user
 module - The module code from the querystring
 eventCode - A code to represent the event
 eventParamName - The type of event parameters sent
 eventParamValue - Any parameters the event needs to send
 context - The context of the event
 dateCreated - The datetime stamp for the event
 
*/

$sqldata[]="CREATE TABLE `tbl_logger` (                                                                                                                                                                                                                               
             `id` varchar(32) NOT NULL,
			 `userId` varchar(25) NOT NULL default '',
             `module` varchar(32) default NULL,
			 `eventcode` varchar(32) default NULL,
             `eventParamName` varchar(32) default NULL,
			 `eventParamValue` varchar(32) default NULL,
			 `context` varchar(32) default NULL,
             `dateCreated` datetime default NULL,
             `isLanguageCode` tinyint, 
             PRIMARY KEY  (`id`), 
			 KEY `userId` (`userId`),
			 CONSTRAINT `logger_user` FOREIGN KEY (`userId`) REFERENCES `tbl_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE)
             TYPE=InnoDb ROW_FORMAT=DYNAMIC  COMMENT='Table to hold the log events'";
?>
