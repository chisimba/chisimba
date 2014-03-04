<?php
/* ----------- data class extends dbTable for tbl_commenttype------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_commenttype
*/
class dbcommenttype extends dbTable
{

    /**
    * 
    * @var string $errCode Code to return if there is an error
    * 
    */
    var $errCode;
    
    /**
    * 
    * @var object $objLanguage String to hold the instance of the 
    * language object
    * 
    */
    var $objLanguage;
    
    /**
    * 
    * @var object $objUser String to hold instance of the user object
    * 
    */
    var $objUser;

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_commenttype');
        $this->objUser = & $this->getObject("user", "security");
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
    }

    /**
    * Save method for editing a record in this table
    *@param string $mode: edit if coming from edit, add if coming from add
    */
    function saveRecord($mode, $userId)
    {
        $id=$this->getParam('id', NULL);
        $type = $this->getParam('type', NULL);
        $title = $this->getParam('title', NULL);
        $modified = $this->getParam('modified', NULL);

        // if edit use update
        if ($mode=="edit") {
            $this->update("id", $id, array(
            'type' => $type,
            'title' => $title,
            'datemodified' => date("Y/m/d H:i:s"),
	    'modifierid' => $this->objUser->userId()));

        }#if
        // if add use insert
        if ($mode=="add") {
            if ($this->checkIfExists($type)) {
                $this->errCode=$this->objLanguage->languageText("mod_commenttypeadmin_exists",'commenttypeadmin')
                  . ": " . $type;
                return FALSE;
            } else {
                $this->insert(array(
                  'type' => $type,
                  'title' => $title,
                  'datecreated' => date("Y/m/d H:i:s"),
		  'creatorid' => $this->objUser->userId()));
                return TRUE;
            }
        }#if
    }#function
    
    /**
    * Method to get all items from the table.
    *      Override in derived classes to implement access restrictions
    *
    * @param string $filter a SQL WHERE clause (optional)
    * @return array |FALSE Rows as an array of associative arrays, or FALSE on failure
    */
    function getTypes($filter = null)
    {
        return $this->getAll($filter);
	
    }
    
    /**
    * 
    * Method to check if a type exists
    * 
    * @param string $type The type to check if it exists
    * @return TRUE|FALSE and set an error code if TRUE
    * 
    */
    function checkIfExists($type)
    {
        if ($this->valueExists('type', $type)) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }
} #end of class
?>
