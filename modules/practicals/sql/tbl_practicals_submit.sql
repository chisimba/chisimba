<?php
//Table Name
$tablename = 'tbl_practicals_submit';

//Options line for comments, encoding and character set
$options = array('comment' => 'Marked and submitted practicals for students in a context', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
/*Fields
*/
$fields = array(
        'id' => array(
                'type' => 'text',
                'length' => 32,
                'notnull'=> 1,
                'default'=> '',
        ),
        'practicalid' => array(
                'type' => 'text',
                'length' => 32
        ),
        'userid' => array(
                'type' => 'text',
                'length' => 32
        ),
        'studentfileid' => array(
                'type' => 'text',
                'length' => 32
        ),
        'lecturerfileid' => array(
                'type' => 'text',
                'length' => 32
        ),
        'online' => array(
                'type' => 'clob',
        ),
        'datesubmitted' => array(
                'type' => 'timestamp'
        ),
        'mark' => array(
                'type' => 'decimal',
        ),
        'commentinfo' => array(
                'type' => 'clob'
        ),
        'updated' => array(
                'type' => 'timestamp',
                'length' => 14
        ),
        'fileid' => array(
                'type' => 'timestamp',
                'length' => 14
        )
);
?>