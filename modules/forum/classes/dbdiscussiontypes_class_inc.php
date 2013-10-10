<?php
  // security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Discussion Forum Types of Topics Table
* This class controls all functionality relating to the tbl_forum_discussiontype table
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package forum
* @version 1
*/
class dbDiscussionTypes extends dbTable
 {

	/**
	* Constructor method to define the table(default)
	*/
	function init()
	{
		parent::init('tbl_forum_discussiontype');
    }
    
    function getDiscussionTypes()
    {
        return $this->getAll();//'ORDER BY type_name'
    }
    
 }
 ?>