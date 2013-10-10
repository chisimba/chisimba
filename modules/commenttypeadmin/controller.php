<?php
/* ----------- controller class extends controller for tbl_storycategory------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
*
* Controller class for the table tbl_storycategory
*
* @author Derek Keats
* @package comment
* @version $Id: controller.php 10795 2008-10-03 14:05:29Z joconnor $
* @copyright 2005 GNU GPL
*
*/
class commenttypeadmin extends controller
{

    /**
    * 
    * @var string $action The action parameter from the querystring 
    * 
    */
    var $action;
    
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
    * 
    * @var object $objDb String to hold instance of the dbcomment database object
    * 
    */
    var $objDb;

    /**
    *
    * @var object $objConfig String to hold instance of the config object
    *
    */
    var $objConfig;


    var $objFile;
    
    /**
    * 
    * Standard constructor method 
    * 
    */
    function init()
    {
        //Retrieve the action parameter from the querystring
        $this->action = $this->getParam('action', Null);
        //Create an instance of the database class for this module
        $this->objDb = & $this->getObject('dbcommenttype');
        //Create an instance of the User object
        $this->objUser =  & $this->getObject("user", "security");
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
        //Create an instance of the language object
        $this->objConfig = &$this->getObject("altconfig", "config");
        
        $this->objFile =& $this->getObject('upload', 'filemanager');

    }

    /**
    * 
    * Standard dispatch method 
    * 
    */
    function dispatch()
    {
        switch ($this->action) {
            case null:
            case "view":
                  $ar = $this->objDb->getAll();
                  $this->setVarByRef('ar', $ar);
                  return "main_tpl.php";
                  break;

            case 'edit':
                 $this->getForEdit('edit');
                 $this->setVar('mode', 'edit');
                 return "edit_tpl.php";
                 break;

            case 'delete':
                 // retrieve the confirmation code from the querystring
                 $confirm=$this->getParam("confirm", "no");
                 if ($confirm=="yes") {
                     $this->objDb->delete("id", $this->getParam('id', Null));
                     $ar = $this->objDb->getAll();
                     $this->setVarByRef('ar', $ar);
                     return "main_tpl.php";
                 }
                  break;

            case 'add':
                $this->setVar('mode', 'add');
                return "edit_tpl.php";
                break;

            case 'save':
                	$this->objDb->saveRecord($this->getParam('mode', Null), $this->objUser->userId()); 
                    $ar=array();
                    $ar = $this->objDb->getAll();
                    $this->setVarByRef('ar', $ar);
                    return "main_tpl.php";
                break;

            //---------------
            //Added 2006/07/24 by Serge Meunier - Allowing uploading of icons
            case 'upload':
            	 $typeId = $this->getParam('id');
            	 $typeRow = $this->objDb->getRow('id',$typeId);
            	 $type = $typeRow['type'];
            	 $this->setVarByRef('commentType',$type);
                 return "upload_tpl.php";
                 break;
                 
            case 'doupload':
				 $type = $this->getParam('type', NULL);
				 return $this->uploadFile($type);
                 break;
            //---------------

            default:
                $this->setVar('str', $this->objLanguage->languageText("phrase_actionunknown").": ".$action);
                return 'dump_tpl.php';

        }#switch
    } # dispatch


    /**
    *
    * Method to retrieve the data for edit and prepare 
    * the vars for the edit template.
    * 
    * @param string $mode The edit or add mode @values edit | add
    * 
    */
    function getForEdit($mode)
    {
        $this->setvar('mode', $mode);
        // retrieve the PK value from the querystring
        $keyvalue=$this->getParam("id", NULL);
        if (!$keyvalue) {
            die($this->objLanguage->languageText("modules_badkey").": ".$keyvalue);
        }
        // Get the data for edit
        $this->setVar('ar', $this->objDb->getRow('id', $keyvalue));
    }#getForedit
    
    /**
    *
    * Method to upload an icon file
    *
    * @param string $type: The comment type for the icon to upload
    * @author Serge Meunier
    * Added 2006/07/24
    *
    */
    function uploadFile($type)
    {
        // upload a new file
       $path = $this->objConfig->getskinRoot() . "_common/icons/";
       $filename = "comment" . $type . ".gif";
      // $filename = $this->objFile->uploadFile('.gif');

	//print_r($_FILES['fileupload']['tmp_name']);
	$fullPath = $path.$filename;
	if (file_exists($fullPath)) {
		unlink($path.$filename);
	}
	$temppath = $_FILES['fileupload']['tmp_name'];
	
	//to remove old icons
	
         
	chmod(move_uploaded_file($temppath,$path.$filename),0777);
	chmod($path.$filename,0777);
	


  


        return $this->nextAction('view');
    }
} #end of class
?>
