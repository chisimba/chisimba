<?php
/**
*
* WTM view buildings class
*
* This file provides a data viewing class for the WTM module's
* building database. Its purpose is allow administrators to view
* the contents of the database as well as initiating the adding 
* or editing process.
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


class viewbuildings extends object 
{
	public $objLanguage;
 
	public $objDBBuildings;
 
	/**
    * Constructor method to instantiate language and buildings DB.
    */  
	public function init()
	{
		 $this->objLanguage = $this->getObject('language','language');
		 
		 $this->objDBBuildings = $this->getObject('dbwtm_buildings','wtm');
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
		$objForm = new form('buildings', $this->getFormAction());
		
		//Retrieve all buildings from DB.
		$allBuildings = $this->objDBBuildings->listAll();
		
		//Create a table object.
		$buildingsTable = &$this->newObject("htmltable", "htmlelements");

		//Define table properties.
		$buildingsTable->border = 0;
		$buildingsTable->cellspacing = '12';
		$buildingsTable->width = "60%";

		//Create the array for the table header.
		$tableHeader = array();
		$tableHeader[] = $this->objLanguage->languageText("mod_wtm_building", 'wtm');
		$tableHeader[] = $this->objLanguage->languageText("mod_wtm_longcoordinate", 'wtm');
		$tableHeader[] = $this->objLanguage->languageText("mod_wtm_latcoordinate", 'wtm');
		$tableHeader[] = $this->objLanguage->languageText("mod_wtm_xexpand", 'wtm');
		$tableHeader[] = $this->objLanguage->languageText("mod_wtm_yexpand", 'wtm'); 
		$buildingsTable->addHeader($tableHeader, "heading");
		
		//Display all the buildings in the table.
		foreach($allBuildings as $thisBuilding)
		{
			//View icon.
			//Create icon object.
			$iconViewEvent = $this->getObject('geticon','htmlelements');
			//Set icon picture.
			$iconViewEvent->setIcon('view');
			//Set icon alternative text.
			$iconViewEvent->alt = "View Events";
			//Define link for the icon.
			$mngViewLink = new link($this->uri(array(
										'module'=>'wtm',
										'action'=>'viewEvents',
										'refID'=>$thisBuilding["id"],
										'refbuilding'=>$thisBuilding["building"]
										)));
			//Set the link image/text.
			$mngViewLink->link = $iconViewEvent->show();
			//Build the link
			$linkViewManage = $mngViewLink->show();
			
			//Edit icon 
			$iconEdSelect = $this->getObject('geticon','htmlelements');
			$iconEdSelect->setIcon('edit');	
			$iconEdSelect->alt = "Edit building";
			$mngedlink = new link($this->uri(array(
										'module'=>'wtm',
										'action'=>'editBuilding', 
										'id' => $thisBuilding["id"]
										)));
			$mngedlink->link = $iconEdSelect->show();
			$linkEdManage = $mngedlink->show(); 
			
			//Delete icon
			$iconDelete = $this->getObject('geticon', 'htmlelements');
			$iconDelete->setIcon('delete');
			$iconDelete->alt = $this->objLanguage->languageText("mod_wtm_deletebuilding", 'wtm');
			//Create a new confirm link Object
			$objConfirm = &$this->getObject("link", "htmlelements");
			//Create a new confirm object. 
			$objConfirm = &$this->newObject('confirm', 'utilities');
			//Set object to confirm and the path for the confirm implementation and confirm text
			$objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
										'module' => 'wtm',
										'action' => 'deleteBuilding',
										'id' => $thisBuilding["id"]
			)) , $this->objLanguage->languageText('mod_wtm_suredelete', 'wtm'));

			// Add the table rows.
			$buildingsTable->startRow();
			$buildingsTable->addCell($thisBuilding["building"]);
			$buildingsTable->addCell($thisBuilding["longcoordinate"]);
			$buildingsTable->addCell($thisBuilding["latcoordinate"]);
			$buildingsTable->addCell($thisBuilding["xexpand"]);
			$buildingsTable->addCell($thisBuilding["yexpand"]);   
			$buildingsTable->addCell($linkViewManage);
			$buildingsTable->addCell($linkEdManage);
			$buildingsTable->addCell($objConfirm->show());
			$buildingsTable->endRow();
		}
	
		//Add building icon
		$iconSelect = $this->getObject('geticon','htmlelements');
		$iconSelect->setIcon('add');	
		$iconSelect->alt = "Add New Building";
		$mnglink = new link($this->uri(array(
									'module'=>'wtm',
									'action'=>'addBuilding'
									)));
		$mnglink->link = $iconSelect->show();
		$linkManage = $mnglink->show();
		
		//Add the table row for the add icon
		$buildingsTable->startRow();
		$buildingsTable->addCell($linkManage);
		$buildingsTable->endRow();
	  
		$objForm->addToForm($buildingsTable->show());
		
		return $objForm->show();
	}
	
	/**
	* Method to retrieve form action to determine
	* if its "addBuilding" or "editBuilding".
	*/
	private function getFormAction()
	{
		$action = $this->getParam("action", "addBuilding");
		if ($action == "editBuilding") 
		{
			$formAction = $this->uri(array("action" => "updateBuilding"), "wtm" );
		}
		else
		{
			$formAction = $this->uri(array("action" => "addBuilding"), "wtm");
		}
		return $formAction;
		}
		
	/**
	* Display building list method, which calls the
	* build form method.
	*/
	public function show()
	{
		return $this->buildForm();
	}
}
?>
