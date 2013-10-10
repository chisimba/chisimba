<?php
/**
*
* WTM edit event class
*
* This file provides an edit class for the WTM module's event database.
* It creates the editing form for which new events can be enter or existing
* events modified. Its purpose is to allow administrators to edit/add
* event to the database.
* 
* PHP version 5
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the
* Free Software Foundation, Inc.,
* 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*
* @category Chisimba
* @package WTM
* @author Yen-Hsiang Huang <wtm.jason@gmail.com>
* @copyright 2007 AVOIR
* @license http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version CVS: $Id: demo_class_inc.php,v 1.4 2007-08-03 10:33:34 Exp $
* @link http://avoir.uwc.ac.za
*/

/**
* Security check: the $GLOBALS is an array used to control access to certain constants.
* Here it is used to check if the file is opening in engine, if not it
* stops the file from running.
*
* @global entry point $GLOBALS['kewl_entry_point_run']
* @name $kewl_entry_point_run
*/
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}


class editevent extends object 
{
	public $objLanguage;
 
	public $objDBEvents;
 
	/**
    * Constructor method to instantiate language and events DB.
    */  
	public function init()
	{
		$this->objLanguage = $this->getObject('language','language');
		
		$this->objDBEvents = $this->getObject('dbwtm_events','wtm');  
	}
	
	/**
	* Method to load the required html elements.
	*/
	private function loadElements()
	{
		$this->loadClass('form','htmlelements');
		
		$this->loadClass('textinput','htmlelements');
		
		$this->loadClass('textarea','htmlelements'); 
		
		$this->loadClass('label','htmlelements');
		
		$this->loadClass('button','htmlelements');
		
		$this->loadClass('link','htmlelements');
	}
	
	/**
	* Build form method, which constructs the add/edit 
	* events form.
	*/	
	private function buildForm()
	{
		$this->loadElements();
		
		//Create new form object
		$objForm = new form('events', $this->getFormAction());
		
		//Retrieve event ID for the case of editing.
		$id = $this->getParam('id');
		
		//Retrieve building name and ID for the case of adding.
		$refID = $this->getParam('refID');
		$refbuilding = $this->getParam('refbuilding');
		
		//If event id is not empty, get the event details.
		if (!empty($id))
		{
			//Fetch the existing event data.
			$eventData = $this->objDBEvents->listSingle($id);
			$buildingid = $eventData[0]["buildingid"];
			$event = $eventData[0]["event"];
			$date = $eventData[0]["date"];
			$description = $eventData[0]["description"];
			$imagename = $eventData[0]["imagename"];
			$videoname = $eventData[0]["videoname"];
		}
		else
		{
			//Set building ID to the retrieved building ID,
			//which cannot be edited.
			$buildingid = $refID;
			$event = "";
			$date = "";
			$description = "";
			$imagename = "";
			$videoname = "";
		}

        //Building ID text input.
        //Create a new textinput for the name of the event.
        $objBuildingID = new textinput('buildingid', $buildingid, 'hidden');
        //Create a new label for the text labels (Building name).
        $BuildingIDLabel = new label ($refbuilding);
        $objForm->addToForm($BuildingIDLabel ->show() , "<br />");
        $objForm->addToForm($objBuildingID->show() . "<br />");

        //Event name text input.
        $objEvent = new textinput('event', $event);
        $eventLabel = new label ($this->objLanguage->languagetext("mod_wtm_event","wtm"),"event");
        $objForm->addToForm($eventLabel ->show() , "<br />");
        $objForm->addToForm($objEvent->show() . "<br />");
		
		//Event date text input.
        $objDate = new textinput('date', $date);
        $dateLabel = new label ($this->objLanguage->languagetext("mod_wtm_date","wtm"),"date");
        $objForm->addToForm($dateLabel->show() , "<br />");
        $objForm->addToForm($objDate->show() . "<br />");
		
		//Event description text area.
        $objDescription = new textarea('description', $description);
        $descriptionLabel = new label($this->objLanguage->languageText("mod_wtm_description","wtm"),"description");
        $objForm->addToForm($descriptionLabel->show() . "<br />");
        $objForm->addToForm($objDescription->show() . "<br />");
		
		//Event image name text input.
        $objImageName = new textinput('imagename', $imagename);
        $imageNameLabel = new label ($this->objLanguage->languagetext("mod_wtm_imagename","wtm"),"imagename");
        $objForm->addToForm($imageNameLabel->show() , "<br />");
        $objForm->addToForm($objImageName->show() . "<br />");
		
		//Event video name text input.
        $objVideoName = new textinput('videoname', $videoname);
        $videoNameLabel = new label ($this->objLanguage->languagetext("mod_wtm_videoname","wtm"),"videoname");
        $objForm->addToForm($videoNameLabel->show() , "<br />");
        $objForm->addToForm($objVideoName->show() . "<br />");
		
        //Submit button.
        //Create a button for submitting the form.
        $objButton = new button('save');
        // Set the button type to submit.
        $objButton->setToSubmit();
        // Use the language object to label button.
        $objButton->setValue(' '.$this->objLanguage->languageText("mod_wtm_saveEvent", "wtm").' ');
        $objForm->addToForm($objButton->show());
      			
		//Back button.
        $objBackButton = new button();
		$objBackButton->setValue(' '.$this->objLanguage->languageText("mod_wtm_backbutton", "wtm").' ');
		//Define link for the button.
		$mngBackLink = new link($this->uri(array(
									'module'=>'wtm',
									'action'=>'viewEvents',
									'refID'=>$refID,
									'refbuilding'=>$refbuilding
									)));
		//Set the link image/text.
		$mngBackLink->link = $objBackButton->show();
		//Build the link.
		$linkBackManage = $mngBackLink->show();
		$objForm->addToForm($linkBackManage);
		
		return $objForm->show();
	}

	/**
	* Method to retrieve form action to determine
	* if its "addEvent" or "editEvent".
	*/
	private function getFormAction()
	{
		$action = $this->getParam("action", "addEvent");
		if ($action == "editEvent") 
		{
			$id = $this->getParam("id");
			$formAction = $this->uri(array("action" => "updateEvent", "id"=>$id), "wtm" );
		} 
		else
		{
			$formAction = $this->uri(array("action" => "addNewEvent"), "wtm");
		}
		return $formAction;
	}	

	/**
	* Display event form method which calls the build 
	* form method.
	*/
	public function show()
	{
		return $this->buildForm();
	}
}
?>

