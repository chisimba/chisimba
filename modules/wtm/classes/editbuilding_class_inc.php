<?php
/**
*
* WTM edit building class
*
* This file provides an edit class for the WTM module's building database.
* It creates the editing form for which new buildings can be enter or existing
* buildings modified. Its purpose is to allow administrators to edit/add
* building to the database.
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


class editbuilding extends object 
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
		
		$this->loadClass('textinput','htmlelements');
		
		$this->loadClass('label','htmlelements');
		
		$this->loadClass('button','htmlelements');
		
		$this->loadClass('link','htmlelements');
	}
	
	/**
	* Build form method, which constructs the add/edit 
	* buildings form.
	*/	
	private function buildForm()
	{
		$this->loadElements();
		
		$objForm = new form('buildings', $this->getFormAction());
		
		//Retrieve building id for the case of editing.
		$id = $this->getParam('id');
		
		//If building id is not empty, get the building details.
		if (!empty($id))
		{
			//Fetch the existing building data
			$buildingData = $this->objDBBuildings->listSingle($id);
			$building = $buildingData[0]["building"];
			$longcoordinate = $buildingData[0]["longcoordinate"];
			$latcoordinate = $buildingData[0]["latcoordinate"];
			$xexpand = $buildingData[0]["xexpand"];
			$yexpand = $buildingData[0]["yexpand"];
		}
		else
		{
			$building = "";
			$longcoordinate = "";
			$latcoordinate = "";
			$xexpand = "";
			$yexpand = "";
		}

        //Building name text input.
        //Create a new textinput for the name of the building.
        $objBuilding = new textinput('building', $building);
        //Create a new label for the text labels.
        $buildingLabel = new label ($this->objLanguage->languagetext("mod_wtm_building","wtm"),"building");
        $objForm->addToForm($buildingLabel->show() , "<br />");
        $objForm->addToForm($objBuilding->show() . "<br />");
		
		//Longitude coordinate text input.
        $objLongCoordinate = new textinput('longcoordinate', $longcoordinate);
        $longCoordinateLabel = new label ($this->objLanguage->languagetext("mod_wtm_longcoordinate","wtm"),"longcoordinate");
        $objForm->addToForm($longCoordinateLabel->show() , "<br />");
        $objForm->addToForm($objLongCoordinate->show() . "<br />");
		
		//Latitude coordiante text input.
        $objLatCoordinate = new textinput('latcoordinate', $latcoordinate);
        $latCoordinateLabel = new label ($this->objLanguage->languagetext("mod_wtm_latcoordinate","wtm"),"latcoordinate");
        $objForm->addToForm($latCoordinateLabel->show() , "<br />");
        $objForm->addToForm($objLatCoordinate->show() . "<br />");
		
		//X-expand text input.
        $objXexpand = new textinput('xexpand', $xexpand);
        $xexpandLabel = new label ($this->objLanguage->languagetext("mod_wtm_xexpand","wtm"),"xexpand");
        $objForm->addToForm($xexpandLabel->show() , "<br />");
        $objForm->addToForm($objXexpand->show() . "<br />");
		
		//Y-expand text input.
        $objYexpand = new textinput('yexpand', $yexpand);
        $yexpandLabel = new label ($this->objLanguage->languagetext("mod_wtm_yexpand","wtm"),"yexpand");
        $objForm->addToForm($yexpandLabel->show() , "<br />");
        $objForm->addToForm($objYexpand->show() . "<br />");
		
        //Submit button
        //Create a button for submitting the form
        $objSubmitButton = new button('save');
        //Set the button type to submit
        $objSubmitButton->setToSubmit();
        //Use the language object to label button
        $objSubmitButton->setValue(' '.$this->objLanguage->languageText("mod_wtm_savebuilding", "wtm").' ');
        $objForm->addToForm($objSubmitButton->show());
		
		//Back button
        $objBackButton = new button();
		$objBackButton->setValue(' '.$this->objLanguage->languageText("mod_wtm_backbutton", "wtm").' ');
		//Define link for the button
		$mngBackLink = new link($this->uri(array(
									'module'=>'wtm',
									'action'=>'viewBuildings' 
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
	* if its "addBuilding" or "editBuilding".
	*/
	private function getFormAction()
	{
		$action = $this->getParam("action", "addBuilding");
		if ($action == "editBuilding") 
		{
			$id = $this->getParam("id");
			$formAction = $this->uri(array("action" => "updateBuilding", "id"=>$id), "wtm" );
		} 
		else
		{
			$formAction = $this->uri(array("action" => "addNewBuilding"), "wtm");
		}
		return $formAction;
	}	
	
	/**
	* Display building form method which calls the build 
	* form method.
	*/
	public function show()
	{
		return $this->buildForm();
	}
}
?>

