<?php
/* 
 -- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_contact`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_contact` (
  `tbl_guardian_id` VARCHAR(50) NOT NULL ,
  `address` VARCHAR(50) NOT NULL ,
  `mobile_number` VARCHAR(15) NULL ,
  `telephone_number` VARCHAR(15) NULL ,
  `fax` VARCHAR(15) NULL ,
  `email` VARCHAR(50) NULL ,
  PRIMARY KEY (`tbl_guardian_id`) ,
  INDEX `contact_FKIndex1` (`tbl_guardian_id` ASC) ,
  CONSTRAINT `fk_96914cfc-5f5c-11e0-b737-0019d288e6dc`
    FOREIGN KEY (`tbl_guardian_id` )
    REFERENCES `ERD_SMIS`.`tbl_guardian` (`g_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
PACK_KEYS = 0
ROW_FORMAT = DEFAULT;

 */
 $tablename = 'tbl_contact';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'tbl_guardian_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'address' => array(
        'type' => 'text',
        'length' => 50,
        'notnull' => TRUE
        ),
     'phone_number' => array(
        'type' => 'text',
        'length' => 15,
        'notnull' => TRUE
        ),

    'fax' => array(
        'type' => 'text',
        'length' => 32
        ),
     'email' => array(
        'type' => 'text',
        'length' => 32
        )
     );

  $name = 'tbl_contact_FKIndex1';

$indexes = array(
                'fields' => array(
                    'tbl_guardian_id' => array()
                )
        );
?>
