<?php
/* -------------------- stories class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Module class to handle administration of KEWL.NextGen stories
 * Stories are text elements that can be placed in a KEWL page
 * by code or in sequence by location. It is meant for site-level
 * display of text, such as on the post login screen for example.
 *
 * @author Derek Keats
 *
 */
class stories extends controller
{
    /*
    Variables for creating the user, language object, etc
    */
    var $objUser;
    var $objButtons; #DEPRECATED
    var $objConfig;

    /**
     * Intialiser for the adminGroups object
     *
     * @param byref $ string $engine the engine object
     */
    function init()
    {
        $this->objButtons =  $this->getObject('navbuttons', 'navigation');
        $this->objUser =  $this->getObject('user', 'security');
        $this->objLanguage =  $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig =  $this->getObject('altconfig', 'config');
        // Create an instance of the database language class
        $this->objDbStories =  $this->getObject('dbstories');
        // Create an instance of the interface supporting class
        $this->objInterface =  $this->getObject('storyinterface');
        $this->objDate = $this->getObject('dateandtime','utilities');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    /**
     * *The standard dispatch method for the module. The dispatch() method must
     * return the name of a page body template which will render the module
     * output (for more details see Modules and templating)
     */
    function dispatch($action)
    {
        // retrieve the mode from the querystring
        $mode = $this->getParam("mode", null);
        // retrieve the sort order from the querystring
        $order = $this->getParam("order", null);
        switch ($action) {
            case null:
            case "view":
                $filter = NULL;
                $ar = $this->objDbStories->fetchStories($filter);
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;
            case "edit":
                $this->setvar('mode', "edit");
                return 'editform_tpl.php';
                break;
            case "add":
                $this->setvar('mode', 'add');
                return 'editform_tpl.php';
                break;
            case "translate":
                $this->setvar('mode', 'translate');
                return 'editform_tpl.php';
                break;
            case "save":
                $mode = $this->getParam("mode", NULL);
                $this->objDbStories->saveStories($mode);
                $this->setForShow();
                //return "main_tpl.php";
                return $this->nextAction(null);
                break;
            case "delete":
                // retrieve the confirmation code from the querystring
                $confirm=$this->getParam("confirm", "no");
                if ($confirm=="yes") {
                    $this->deleteItem();
                    return $this->nextAction(NULL);
                }
                break;
            case "viewstory":
                $id = $this->getParam('id', NULL);
                $objStories =  $this->getObject('sitestories');
                $this->setVar('str', $objStories->fetchStory($id));
                return 'dump_tpl.php';
            case "readmore":
            	return 'showstories_tpl.php';
            case "getfullstory":
            	$id = $this->getParam('id', null);
            	$objStories =  $this->getObject('sitestories');
            	$textRow = $objStories->getRow('id', $id);
				$mainText = $textRow['maintext'];

				$ret .= "<div id=\"$id\">".$mainText;
	            $ret .= "<a href=\"javascript:getTrimStory('$id');\">[Read Less]</a>";
	            $ret .= "</div>";
				echo $ret;
            	break;
            case "gettrunctstory":
            	$id = $this->getParam('id', null);
            	$objStories =  $this->getObject('sitestories');
            	$textRow = $objStories->getRow('id', $id);
				$mainText = $textRow['maintext'];
				$mainText = substr($mainText, 0, 150);
            	$mainText = $mainText."...";
            	
				$ret .= "<div id=\"$id\">".$mainText;
	            $ret .= "<a href=\"javascript:getFullStory('$id');\">[Read More]</a>";
	            $ret .= "</div>";
            	echo $ret;
            	break;
            case "getallstories":
            	$objStories =  $this->getObject('sitestories');
            	$limit = $this->getParam('limit', null);
            	$ret = $objStories->createAllStories($limit);
            	echo $ret;
            	break;
            case "getlessstories":
            	$objStories =  $this->getObject('sitestories');
            	$limit = $this->getParam('limit', null);
            	$ret = $objStories->fetchPreLoginCategory('prelogin', $limit);
            	echo $ret;
            	break;
            default:
                $this->setVar('str', "<h3>"
                  . $this->objLanguage->languageText("phrase_unrecognizedaction",'stories')
                  .": " . $action . "</h3>");
                return 'dump_tpl.php';
                break;
        }
    }

    /**
    * Method to prepare and set the vars for the output template
    */

    function setForShow()
    {
        $order = $this->getParam("order", null);
        // Define a variable to print the list
        $this->setvar('outStr', $this->listStories($order));
    }

    /**
    * Method to list all atories with descriptions
    */
    function listStories($order=NULL)
    {

        $sql="SELECT id, category, parentId, language, title, isActive,  expirationDate FROM tbl_stories ";
        $filter=NULL;
        if ($order) {
            $filter=" ORDER BY ".$order;
        }
        $rs=$this->objDbStories->getArray($sql.$filter);
        return $this->objInterface->displayData($rs, "stories", TRUE, "id");
    }



    /**
    * Method to prepare the data for the delete confirm templage
    */
    function prepForDelete()
    {
        $this->setvar('nobutton', $this->objButtons->button("confirm", "delete_no.png"));
        $this->setvar('yesbutton', $this->objButtons->button("confirm", "delete_yes.png"));
        $this->setvar('id',$this->getParam("id", NULL));
    }



        /**
    * Method to delete a group
    * @param string $keyvalue The group to be deleted
    */
    function deleteItem()
    {
        $id=$this->getParam("id", NULL);
        if (!$id) {
             die($this->objLanguage->languageText("modules_badkey",'stories').": ".$keyvalue);
        } else {
            $this->objDbStories->delete("id", $id);
        }
    }

    /**
    * Method to take a datetime string and reformat it as text.
    * @param string $date The date in datetime format.
    * @return string $ret The formatted date.
    */
    function formatDate($date)
    {
        $ret = substr($date,8,2);
        $ret .= ' '.$this->objDate->monthFull(substr($date,5,2));
        $ret .= ' '.substr($date,0,4);

        $time = substr($date,11,5);
        if(!empty($time) && $time!=0){
            $ret .= ' '.$time;
        }
        return $ret;
    }
    
    function requiresLogin($action)
    {
    	$notrequiredAction = array('getfullstory', 'readmore', 'gettrunctstory', 'getallstories', 'getlessstories');
    	if (in_array($action, $notrequiredAction)) {
    		return FALSE;
    	} else {
    		return TRUE;
    	}
    }
}
?>