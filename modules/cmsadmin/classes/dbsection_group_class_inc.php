<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Data access class for the sectiongroup table for publicportal module.
*
* @package publicportal
* @category sems
* @copyright AVOIR
* @license GNU GPL
* @author Serge Meunier
*/

class dbsection_group extends dbTable
{

   /**
    * Class Constructor
    *
    * @access public
    * @return void
    */
    public function init()
    {
        try {
            parent::init('tbl_cms_section_group');

       } catch (Exception $e){
            throw customException($e->getMessage());
            exit();
       }
    }
}
?>