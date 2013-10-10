<?php
/**
*
* WTM view events class
*
* This file provides a data viewing class for the WTM module's
* events database. Its purpose is allow administrators to view
* the contents of the database as well as initiating the adding
* and editing process.
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


class viewevents extends object 
{
	public $objLanguage;
 
	public $objDBEventss;
 
	/**
    * Constructor method to instantiate language and buildings DB.
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
		 
		 $this->loadClass('link','htmlelements');
		 
		 $this->loadClass('textinput','htmlelements');
		 
		 $this->loadClass('label','htmlelements');
		 
		 $this->loadClass('textarea','htmlelements');
		 
		 $this->loadClass('button','htmlelements');
	}
	
	/**
	* Build form method, which constructs the view
	* all buildings form.
	*/	
	private function buildForm()
	{
		$this->loadElements();
		
		//Create new form object.
		$objForm = new form('events', $this->getFormAction());
		
		//Retrieve all events from DB
		$allEvents = $this->objDBEvents->listAll();
		
		//Create a table object
		$eventsTable = &$this->newObject("htmltable", "htmlelements");
		
		//Define the table properties.
		$eventsTable->border = 0;
		$eventsTable->cellspacing = '12';
		$eventsTable->width = "60%";

		//Create the array for the table header.
		$tableHeader = array();
		$tableHeader[] = $this->objLanguage->languageText("mod_wtm_event", 'wtm');
		$tableHeader[] = $this->objLanguage->languageText("mod_wtm_date", 'wtm');
		$tableHeader[] = $this->objLanguage->languageText("mod_wtm_description", 'wtm');
		$tableHeader[] = $this->objLanguage->languageText("mod_wtm_imagename", 'wtm'); 
		$tableHeader[] = $this->objLanguage->languageText("mod_wtm_videoname", 'wtm'); 
		$eventsTable->addHeader($tableHeader, "heading");
		
		//Retrieve building ID and building name for 
		//the sorting parameter.
		$refID = $this->getParam('refID');
		$refbuilding = $this->getParam('refbuilding');
		
		//Display all the events in the table.
		foreach($allEvents as $thisEvent)
		{
			if ($thisEvent["buildingid"] == $refID)
			{
				//Edit icon.
				//Create icon object.
				$iconEdSelect = $this->getObject('geticon','htmlelements');
				//Set icon picture.
				$iconEdSelect->setIcon('edit');	
				//Set icon alternative text.
				$iconEdSelect->alt = "Edit event";
				//Define link for the icon.
				$mngedlink = new link($this->uri(array(
											'module'=>'wtm',
											'action'=>'editEvent', 
											'id' => $thisEvent["id"],
											'refID'=>$refID,
											'refbuilding'=>$refbuilding
											)));
				//Set the link image/text.
				$mngedlink->link = $iconEdSelect->show();
				//Build the link
				$linkEdManage = $mngedlink->show(); 
				
				//Delete icon.
				$iconDelete = $this->getObject('geticon', 'htmlelements');
				$iconDelete->setIcon('delete');
				$iconDelete->alt = $this->objLanguage->languageText("mod_wtm_deleteEvent", 'wtm');
				//Create a new confirm link Object
				$objConfirm = &$this->getObject("link", "htmlelements");
				//Create a new confirm object. 
				$objConfirm = &$this->newObject('confirm', 'utilities');
				//Set object to confirm and the path for the confirm implementation and confirm text
				$objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
											'module' => 'wtm',
											'action' => 'deleteEvent',
											'id' => $thisEvent["id"],
											'refID' => $refID
				)) , $this->objLanguage->languageText('mod_wtm_suredelete', 'wtm'));

				//Add the table rows.
				$eventsTable->startRow();
				$eventsTable->addCell($thisEvent["event"]);
				$eventsTable->addCell($thisEvent["date"]);
				$eventsTable->addCell($thisEvent["description"]);
				$eventsTable->addCell($thisEvent["imagename"]);
				$eventsTable->addCell($thisEvent["videoname"]);
				$eventsTable->addCell($linkEdManage);
				$eventsTable->addCell($objConfirm->show());
				$eventsTable->endRow();
			}
		}
	
		//Add event icon.
		$iconSelect = $this->getObject('geticon','htmlelements');
		$iconSelect->setIcon('add');	
		$iconSelect->alt = "Add New Event";
		$mnglink = new link($this->uri(array(
									'module'=>'wtm',
									'action'=>'addEvent',
									'refID'=>$refID,
									'refbuilding'=>$refbuilding
									)));
		$mnglink->link = $iconSelect->show();
		$linkManage = $mnglink->show();
		
		//Add the table row for the add icon.
		$eventsTable->startRow();
		$eventsTable->addCell($linkManage);
		$eventsTable->endRow();
		
		//Back button.
        //Create a back button.
        $objBackButton = new button();
		//Define the back button link.
		$mngBackLink = new link($this->uri(array(
									'module'=>'wtm',
									'action'=>'viewBuildings' 
									)));
		//Set back button text.
		$objBackButton->setValue(' '.$this->objLanguage->languageText("mod_wtm_backbutton", "wtm").' ');
		$mngBackLink->link = $objBackButton->show();
		$linkBackManage = $mngBackLink->show();
			  
		//Add the button to the form
		$objForm->addToForm($eventsTable->show());
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
			$formAction = $this->uri(array("action" => "updateEvent"), "wtm" );
		}
		else
		{
			$formAction = $this->uri(array("action" => "addEvent"), "wtm");
		}
		return $formAction;
		}

	/**
	* Display event list method, which calls the
	* build form method.
	*/
	public function show()
	{
		return $this->buildForm();
	}
}
?>
