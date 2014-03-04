<?php
/* ----------- data class extends dbTable for tbl_blog------------*/// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
*
* Model class for the table loggedinusers
*
*/
class dbkngon extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init()
    {
        parent::init('tbl_loggedinusers');
        $this->USE_PREPARED_STATEMENTS=True;
    }

    /**
    * Method to see if a user is online
    */
    public function isLoggedIn($userId)
    {
        return $this->valueExists('userId', $userId);
    }

} #end of class
?>