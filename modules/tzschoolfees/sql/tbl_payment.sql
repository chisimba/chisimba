<?php

$tablename = 'tbl_payment';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
'tbl_student_classes_id'=>array(
'type' => 'text',
'length'=>32,
'notnull'=>TRUE
),
'tbl_fee_id'=>array(
'type' => 'text',
'length'=>32,
'notnull'=>TRUE
),
'tbl_status_id'=>array(
'type' => 'text',
'length'=>32,
'notnull'=>TRUE
),
    'amount_paid' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
    'receipt_no' => array(
        'type' => 'text',
        'length' => 45,
        'notnull' => TRUE
        ),
    'bank_name' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

     'bank_branch' => array(
          'type' => 'text',
          'length' => 45,
          'notnull' => TRUE
    ),

      'date_paid' => array(
            'type' => 'timestamp',
            'notnull' => TRUE
    ),

        'installments' => array(
            'type' => 'text',
            'length' => 32,
            'notnull' => TRUE
     ));

$name = 'tbl_payment_Fkindex1';
$indexes = array(
        'fields' => array(
          'tbl_status_id'=>array(),
          'tbl_student_classes_id'=>array(),
          'tbl_fee_id'=>array()
)
);

?>