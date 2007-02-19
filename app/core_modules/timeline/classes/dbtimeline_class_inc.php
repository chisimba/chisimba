<?php
// security check - must be included in all Chisimba scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* 
* timeline: 
* Time lines module
*
* @author Derek Keats
* @category Chisimba
* @package timeline
* @copyright UWC
* @licence GNU/GPL
*
*/
class dbtimelines extends dbtable
{

    
    /**
    *
    * @param string object $objUser A property to hold an instance of the user object
    *
    */
    public $objUser;
  

    
    /**
    *
    * Constructor for the module dbtable class for tbl_timeline_timelines
    * It sets the database table via the parent dbtable class init
    * method, and instantiates required objects.
    *
    */
    public function init()
    {
        try {
    		parent::init('tbl_timeline_timelines');
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
    * Save method for tbl_timeline_timelines
    * @param string $mode: edit if coming from edit, add if coming from add
    *
    */
    public function saveData($mode)
    {
        //Retrieve the value of the primary keyfield $id
        $id = $this->getParam('id', NULL);
        //Retrieve the value of $timelineid
        $timelineid = $this->getParam('timelineid', NULL);
        //Retrieve the value of $start
        $start = $this->getParam('start', NULL);
        //Retrieve the value of $end
        $end = $this->getParam('end', NULL);
        //Retrieve the value of $isduration
        $isduration = $this->getParam('isduration', NULL);
        //Retrieve the value of $title
        $title = $this->getParam('title', NULL);
        //Retrieve the value of $timelinetext
        $timelinetext = $this->getParam('timelinetext', NULL);
        //Retrieve the value of $isdeleted
        $isdeleted = $this->getParam('isdeleted', NULL);

        //If coming from edit use the update code
        if ($mode=="edit") {
            $ar = array(
              'timelineid' => $timelineid,
              'start' => $start,
              'end' => $end,
              'isduration' => $isduration,
              'title' => $title,
              'timelinetext' => $timelinetext,
              'modified' => $this->now(),

              'isdeleted' => $isdeleted
            );
            $this->update('id', $id, $ar);
        } else {
            $ar = array(
              'timelineid' => $timelineid,
              'start' => $start,
              'end' => $end,
              'isduration' => $isduration,
              'title' => $title,
              'timelinetext' => $timelinetext,
              'created' => $this->now(),

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
    * Delete a record from tbl_timeline_timelines. Use cautiously as it can delete
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