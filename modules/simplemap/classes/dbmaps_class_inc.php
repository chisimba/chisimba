<?php
// security check - must be included in all Chisimba scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* 
* Simplemap module to access the maps database table tbl_simplemap_maps
*
* @author Derek Keats
* @category Chisimba
* @package timeline
* @copyright UWC
* @licence GNU/GPL
*
*/
class dbmaps extends dbtable
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
    		parent::init('tbl_simplemap_maps');
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
    * Save method for tbl_simplemap_maps
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
        //Retrieve the value of $url
        $url = $this->getParam('url', NULL);
        //Retrieve the value of gLat
        $gLat = $this->getParam('glat', NULL);
        //Retrieve the value of gLong
        $gLong = $this->getParam('glong', NULL);
        //Retrieve the value of width
        $width = $this->getParam('width', NULL);
        //Retrieve the value of height
        $height = $this->getParam('height', NULL);
        //Retrieve the value of $magnify
        $magnify = $this->getParam('magnify', NULL);
        //Retrieve the value of $mapType
        $mapType = $this->getParam('maptype', NULL);
        //If coming from edit use the update code
        if ($mode=="edit") {
            $ar = array(
              'title' => $title,
              'description' => $description,
              'url' => $url,
              'glat' => $gLat,
              'glong' => $gLong,
              'magnify' => $magnify,
	          'height' => $height,
	          'width' => $width,
              'maptype' => $mapType,
              'modified' => $this->now(),
              'modifierid' => $this->objUser->userId()
            );
            $this->update('id', $id, $ar);
        } else {
            $ar = array(
              'title' => $title,
              'description' => $description,
              'url' => $url,
              'glat' => $gLat,
              'glong' => $gLong,
              'magnify' => $magnify,
	          'height' => $height,
	          'width' => $width,
              'maptype' => $mapType,
              'created' => $this->now(),
              'creatorid' => $this->objUser->userId()
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