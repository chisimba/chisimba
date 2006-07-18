<?

/*
$sqldata[]="CREATE TABLE `tbl_stories` (
  `id` varchar(32) NOT NULL default '',
  `category` varchar(32) NOT NULL default 'hidden',
  `isActive` tinyint(1) NOT NULL default '0',
  `parentId` varchar(32) default 'base',
  `language` char(2) default 'en',
  `title` varchar(255) default NULL,
  `abstract` text,
  `mainText` text,
  `dateCreated` datetime default NULL,
  `creatorId` varchar(25) default NULL,
  `expirationDate` datetime NOT NULL,
  `notificationDate` datetime default NULL,
  `isSticky` tinyint(1) NOT NULL default '0',
  `modified` timestamp(14) NOT NULL,
  `dateModified` datetime default NULL,
  `modifierId` varchar(25) default NULL,
  `commentCount` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB ROW_FORMAT=DYNAMIC COMMENT='Used to hold stories as elements of text for display'";
*/

// Table Name
$tablename = 'tbl_stories';

//Options line for comments, encoding and character set
$options = array('comment' => 'Used to hold stories as elements of text for display', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'category' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE,
        'default' => 'hidden'
		),
    'isActive' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 0
		),
    'parentId' => array(
		'type' => 'text',
        'length' => 32,
        'default' => 'base'
		),
    'language' => array(
		'type' => 'text',
        'length' => 2,
        'default' => 'en'
		),
    'title' => array(
		'type' => 'text',
        'length' => 255,
		),
    'abstract' => array(
		'type' => 'text'
		),
    'mainText' => array(
		'type' => 'text'
		),
    'dateCreated' => array(
		'type' => 'date'
		),
    'creatorId' => array(
		'type' => 'text',
        'length' => 25
		),
    'expirationDate' => array(
		'type' => 'date'
		),
    'notificationDate' => array(
		'type' => 'date'
		),
    'isSticky' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
    'modified' => array(
		'type' => 'timestamp'
		),
    'dateModified' => array(
		'type' => 'date'
		),
    'modifierId' => array(
		'type' => 'text',
        'length' => 25
		),
    'commentCount' => array(
		'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE,
        'default' => 0
		)
    );



/*
Table data for killme.tbl_stories
*/
/*
$sqldata[]="INSERT INTO `tbl_stories` VALUES ('init_1','prelogin',1,'base','en','KEWL.NextGen default prelogin story','This is the KEWL.NextGen main prelogin story','&nbsp;This is the default prelogin story. To edit this story, go to\r\nsite admin, and choose website stories, and edit this prelogin story.\r\nAlternatively, you can delete this story and add one or more additional\r\nstories to the prelogin category.<br>','2005-04-20 12:04:42','1','2025-04-21 00:00:00',NULL,1,20050420121112,'2005-04-20 12:04:12','1',0) , ('init_2','prelogin',1,'base','en','Welcome to KEWL.NextGen','Welcome to KEWL.NextGen, an e-learning system produced by the African Virtual Open Initiatives and Resources (AVOIR) project.','<p>KEWL.NextGen is an advanced e-learning system (sometimes referred to as a learning management system, a virtual learning environment) with features similar to common proprietary systems. It is free software (open source) released under the GNU GPL, and available for download from http://avoir.uwc.ac.za/ External link or by anonymous CVS checkout from the repository nextgen in /cvsroot on cvs.uwc.ac.za using the username and password anoncvs.</p><p>KEWL.NextGen was developed based on several years of experience in e-learning at the University of the Western Cape and partner institutions using its predecessor KEWL, and is under active development by a team of developers in 11 African higher education institutions.</p>','2005-04-20 12:04:35','1','2020-04-21 00:00:00',NULL,0,20050420121835,NULL,NULL,0) , ('dkeats_3','postlogin',1,'base','en','You have successfully logged in to KEWL.NextGen',
'You now have access to KEWL.NextGen\'s rich set of features. You should really ask the system administrator for this site to change this message.','You now have access to KEWL.NextGen\'s rich set of features. If you are\r\nlooking for documentation for users you can download it from\r\nhttp://kngforge.uwc.ac.za/. You can access the AVOIR project at\r\nhttp://avoir.uwc.ac.za/ or explore other documentation, source code,\r\netc at http://cvs.uwc.ac.za/<br>','2005-04-20 12:04:34','1','2025-04-21 00:00:00',NULL,1,20050420122334,NULL,NULL,0) ;";
*/

?>