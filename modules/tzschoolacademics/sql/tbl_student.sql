<?php
/* 
CREATE  TABLE IF NOT EXISTS `tbl_student` (
  `reg_no` VARCHAR(20) NOT NULL ,
  `tbl_guardian_id` VARCHAR(50) NOT NULL ,
  `firstname` VARCHAR(30) NOT NULL ,
  `lastname` VARCHAR(30) NOT NULL ,
  `othernames` VARCHAR(50) NULL ,
  `gender` CHAR(2) NOT NULL ,
  `birth_date` DATE NOT NULL ,
  `religion` VARCHAR(45) NULL ,
  PRIMARY KEY (`reg_no`, `tbl_guardian_id`) ,
  INDEX `student_FKIndex1` (`tbl_guardian_id` ASC) ,
  UNIQUE INDEX `reg_no_UNIQUE` (`reg_no` ASC) ,
  CONSTRAINT `fk_969036f0-5f5c-11e0-b737-0019d288e6dc`
    FOREIGN KEY (`tbl_guardian_id` )
    REFERENCES `tbl_guardian` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
PACK_KEYS = 0
ROW_FORMAT = DEFAULT;

 */
$tablename = 'tbl_student';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

       'reg_no' => array(
        'type' => 'text',
        'length' => 32
        ),
     'tbl_guardian_id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'firstname' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

     'lastname' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

      'othernames' => array(
        'type' => 'text',
        'length' => 50,
        'notnull' => TRUE
        ),
       'gender' => array(
        'type' => 'text',
        'length' => 2,
        'notnull' => TRUE
        ),

        'birthdate' => array(
        'type' => 'date',

        ),
       'religion' => array(
        'type' => 'text',
        'length' => 45,
        'notnull' => TRUE
        )
     );

$name = 'tbl_student_FKIndex1';

$indexes = array(
                'fields' => array(
                    'reg_no' => array(),
                    'tbl_guardian_id' => array(),
                )

        );

?>
