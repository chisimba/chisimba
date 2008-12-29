<?php
// Table Name
$tablename = 'tbl_groupadmin_group';

//Options line for comments, encoding and character set
$options = array('comment' => 'The tbl_groupadmin_groups table is managed by the groupadmin module, its purpose to allow for groups and subgroups. This allows for users to have a context ( ie. In KNG, the permissions module will require information about the users context )', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'parent_id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'name' => array(
        'type' => 'text',
        'length' => 32
        ),
    'description' => array(
        'type' => 'text',
        'length' => 100
        ),
    'last_updated' => array(
        'type' => 'date'
        ),
    'last_updated_by' => array(
        'type' => 'text',
        'length' => 32
        )
    );

//create other indexes here...

$name = 'ind_groups_FK';

$indexes = array(
                'fields' => array(
                    'parent_id' => array()
                )
        );
?>