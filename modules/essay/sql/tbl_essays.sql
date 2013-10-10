<?php
/*$sqldata[] = "CREATE TABLE tbl_essays( 
    id VARCHAR(32) NOT NULL, 
    topicid VARCHAR(32) NOT NULL, 
    topic VARCHAR(255) NOT NULL, 
    notes TEXT, 
    `updated` TIMESTAMP(14) NOT NULL,
    PRIMARY KEY (id),
    KEY `topicid` (`topicid`),
    CONSTRAINT `essayEssaysTopic` FOREIGN KEY (`topicid`) REFERENCES `tbl_essay_topics` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE) TYPE=INNODB 
    COMMENT='A list of essays within a topic'";*/

// Table Name
$tablename = 'tbl_essays';

//Options line for comments, encoding and character set
$options = array('comment' => 'A list of essays within a topic', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
    'type' => 'text',
    'length' => 32
    ),
  'topicid'  => array(
     'type'  =>  'text',
     'length'=>  32
    ),
  'topic' =>  array(
      'type'  =>  'text',
      'length' => 255
    ),
    'notes'  =>  array(
      'type'    =>  'clob'
    ),
    'updated'  =>  array(
      'type'    =>  'timestamp'
   )
);
?>
