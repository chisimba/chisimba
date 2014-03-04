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
$tablename = 'tbl_essay_book';

//Options line for comments, encoding and character set
$options = array('comment' => 'Students booked essays', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
    'type' => 'text',
    'length' => 32
    ),
  'studentid'  => array(
     'type'  =>  'text',
     'length'=>  32
    ),
  'essayid' =>  array(
      'type'  =>  'text',
      'length' => 32
    ),
  'topicid'  =>  array(
      'type'    =>  'text',
      'length'  =>  32
    ),
    'studentfileid'  =>  array(
      'type'    =>  'text',
      'length'  =>  32
    ),
    'lecturerfileid'  =>  array(
      'type'    =>  'text',
      'length'  =>  32
    ),
    'context'  =>  array(
      'type'    =>  'text',
      'length'  =>  255
    ),
    'submitdate'  =>  array(
      'type'    =>  'timestamp' 
    ),
    'mark'  =>  array(
      'type'    =>  'integer',
      'length'  =>  4
    ),
    'comment'  =>  array(
      'type'    =>  'clob'
    ),
    'updated'  =>  array(
      'type'    =>  'timestamp'
    )
);
// Other indicies 
$name = 'book_index';
$indexes = array(
    'fields' => array(
        'essayid' => array(),
        'topicid' => array()
    )
);
?>