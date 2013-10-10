<?php
/*$sqldata[] = "CREATE TABLE tbl_essay_book( 
    id VARCHAR(32) NOT NULL, 
    studentid VARCHAR(32) NOT NULL, 
    essayid VARCHAR(32) NOT NULL, 
    topicid VARCHAR(32) NOT NULL, 
    fileid VARCHAR(100), 
    context VARCHAR(255), 
    submitdate DATE, 
    mark INT, 
    comment TEXT, 
    `updated` TIMESTAMP(14) NOT NULL,
    PRIMARY KEY (id),
    KEY `essayid` (`essayid`),
    KEY `studentid` (`studentid`),
    CONSTRAINT `essayBooked` FOREIGN KEY (`essayid`) REFERENCES `tbl_essays` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `essayBookStudent` FOREIGN KEY (`studentid`) REFERENCES `tbl_users` (`userId`)
    ON DELETE CASCADE ON UPDATE CASCADE) TYPE=INNODB 
    COMMENT='Students booked essays'";*/
// Table Name
$tablename = 'tbl_essay_zipfiles';

//Options line for comments, encoding and character set
$options = array('comment' => 'zipped essays', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
    'type' => 'text',
    'length' => 32,
    'notnull' => 1  
    ),
  'filename'  => array(
     'type'  =>  'text',
     'length'=>  255,
     'notnull' => 1
    ),
  'creatorid' =>  array(
      'type'  =>  'text',
      'length' => 32,
      'notnull' => 1
    ),
    'fileurl'  =>  array(
      'type'    =>  'clob',
      'notnull' => 1
    ),
    'filepath'  =>  array(
      'type'    =>  'clob',
     'notnull' => 1
    ),
    'datecreated'  =>  array(
      'type'    =>  'timestamp',
      'length' => 14,
      'notnull' => 1
    )
);
?>
