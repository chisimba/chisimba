<?php
/* ----------- data class extends dbTable for tbl_discussion_default_ratings------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Default Ratings for Posts Table
* This class controls all functionality relating to the tbl_discussion_default_ratings table
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package discussion
* @version 1
*/
class dbdiscussion_default_ratings extends dbTable
{

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_discussion_default_ratings');
    }
    
    /**
    * Method to get the default list of ratings
    * @return array List of Ratings
    */
    function getDefaultList()
    {
        return $this->getAll();
    }

} #end of class
?>
