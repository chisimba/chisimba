<?php
//5ive definition
$tablename = 'tbl_forum_mailjobs';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
        'id'=>array(
                'type'=>'text',
                'length'=>'32'
),
        'post_parent'=>array(
                'type'=>'text',
                'length'=>'32'
),
        'post_title'=>array(
                'type'=>'text',
                'length'=>'80'
),
        'post_text'=>array(
                'type'=>'text',
                'length'=>'800'
),
        'forum_name'=>array(
                'type'=>'text',
                'length'=>'32'
),
        'user_id'=>array(
                'type'=>'text',
                'length'=>'32'
),
        'reply_url'=>array(
                'type'=>'text',
                'length'=>'100'
),
        'sent'=>array(
                'type'=>'boolean'
)
);

?>