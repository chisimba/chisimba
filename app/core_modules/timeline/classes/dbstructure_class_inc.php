<?php
// security check - must be included in all Chisimba scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* 
* timeline: 
* Timeline module
*
* @author Derek Keats
* @category Chisimba
* @package timeline
* @copyright UWC
* @licence GNU/GPL
*
*/
class dbstructure extends dbtable
{

    
    /**
    *
    * @param string object $objUser A property to hold an instance of the user object
    *
    */
    public $objUser;
  

    
    /**
    *
    * Constructor for the module dbtable class for tbl_timeline_structure
    * It sets the database table via the parent dbtable class init
    * method, and instantiates required objects.
    *
    */
    public function init()
    {
        try {
    		parent::init('tbl_timeline_structure');
        	//Instantiate the user object
        	$this->objUser = $this->getObject('user', 'security');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }
  
    /**
    *
    * Save method for tbl_timeline_structure
    * @param string $mode: edit if coming from edit, add if coming from add
    *
    */
    public function saveData($mode)
    {
        //Retrieve the value of the primary keyfield $id
        $id = $this->getParam('id', NULL);
        //Retrieve the value of $title
        $title = $this->getParam('title', NULL);
        //Retrieve the value of $description
        $description = $this->getParam('description', NULL);
        //Retrieve the value of $focusdate
        $focusdate = $this->getParam('focusdate', NULL);
        //Retrieve the value of $intervalpixels
        $intervalpixels = $this->getParam('intervalpixels', NULL);
        //Retrieve the value of $intervalunit
        $intervalunit = $this->getParam('intervalunit', NULL);
        //Retrieve the value of $tlheight
        $tlheight = $this->getParam('tlheight', NULL);
        //Retrieve the value of $theme
        $theme = $this->getParam('theme', NULL);
        //Retrieve the value of $bgcolor
        $bgcolor = $this->getParam('bgcolor', NULL);
        //Retrieve the value of $isdeleted
        $isdeleted = $this->getParam('isdeleted', NULL);

        //If coming from edit use the update code
        if ($mode=="edit") {
            $ar = array(
              'title' => $title,
              'description' => $description,
              'focusdate' => $focusdate,
              'intervalpixels' => $intervalpixels,
              'intervalunit' => $intervalunit,
              'tlheight' => $tlheight,
              'theme' => $theme,
              'bgcolor' => $bgcolor,
              'modified' => $this->now(),
              'modifierid' => $this->objUser->userId(),
              'isdeleted' => $isdeleted
            );
            $this->update('id', $id, $ar);
        } else {
            $ar = array(
              'title' => $title,
              'description' => $description,
              'focusdate' => $focusdate,
              'intervalpixels' => $intervalpixels,
              'intervalunit' => $intervalunit,
              'tlheight' => $tlheight,
              'theme' => $theme,
              'bgcolor' => $bgcolor,
              'created' => $this->now(),

              'creatorid' => $this->objUser->userId(),

              'isdeleted' => $isdeleted
            );
            $this->insert($ar);
        }

    }

    /**
    * Method to retrieve the data for edit and prepare the vars for
    * the edit template.
    *
    * @param string $mode The mode should be edit or add
    */
    public function getForEdit()
    {
        $order = $this->getParam("order", NULL);
        // retrieve the group ID from the querystring
        $keyvalue=$this->getParam("id", NULL);
        if (!$keyvalue) {
          die($this->objLanguage->languageText("modules_badkey").": ".$keyvalue);
        }
        // Get the data for edit
        $key="id";
        return $this->getRow($key, $keyvalue);
    }
  
    /**
    *
    * Delete a record from tbl_timeline_structure. Use cautiously as it can delete
    * all records by accident if the wrong key is used.
    *
    * @param string $key The key of the record to delete
    * @param string $keyValue The value of the key where deletion should take place
    *
    */
    public function deleteRecord($key, $keyValue)
    {
       $this->delete($key, $keyValue);
    }
 
}
?>