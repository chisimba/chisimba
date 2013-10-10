<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
/**
* Model class for the table tbl_lrs_agree_summary
*
* @author Nic Appleby
* @package Award
*/
class dbindexsummary extends dbTable
{
    /**
     * Method to define the table
     */
     function init()
     {
         parent::init('tbl_award_index_summary');
         $this->objUser = $this->getObject('user', 'security');
     }
}
?>