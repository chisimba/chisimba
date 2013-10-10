<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check


/**
* 
* Class to render common template components for the database generator pages
* 
* @author Derek Keats
* @category Chisimba
* @package generator
* @copyright AVOIR
* @licence GNU/GPL
*
*/
abstract class abtpcommon extends object
{
    /**
    * 
    * @var string object $objLanguage A string to hold the language object
    * 
    */
    public $objLanguage;
    
    /**
    * 
    * Constructor class to initialize language and load form elements
    * 
    */
    public function init() 
    {
        //Create an instance of the language object
        $this->objLanguage = & $this->getObject('language', 'language');
        //Load the form class 
        $this->loadClass('form','htmlelements');
        //Load the textinput class 
        $this->loadClass('textinput','htmlelements');
        //Load the radio class 
        $this->loadClass('radio','htmlelements');
    }
    
    /**
    *
    * Method to provide a dropdown list of tables
    * @access public
    * @return the table dropdown text input for the form
    *
    */
    public function getTablesAsDropDown()
    {
        //Get an instance of the schema generator
		$objSchema = $this->getObject('getschema');
		$ar = $objSchema->listDbTables();
		$objDropDown = $this->getObject('dropdown', 'htmlelements');
		$objDropDown->name='tablename';
		$objDropDown->cssId = 'input_tablename';
		foreach ($ar as $entry) {
		    $objDropDown->addOption($entry, $entry);
		}
		return $objDropDown->show();
    }
    
    /**
    * 
    * Method to return modulecode text input to the form
    * 
    * @access public
    * @return the module code text input for the form
    * 
    */ 
    public function getModuleCodeElement()
    {
    	//Check for serialized element
    	$this->modulecode = $this->getSession('modulecode', NULL);
        //Create an element for the input of module code
        $objElement = new textinput ("modulecode");
        //Set the field type to text
        $objElement->fldType="text";
        $objElement->size=40;
        if (isset($this->modulecode)) {
            //$objElement->value=$this->modulecode;
            return $this->modulecode;
        } else {
            //Add the $title element to the form
            return $objElement->show();
        }
    }
    
    /**
    * 
    * Method to return sideMenuCategory text input to the form
    * 
    * @access public
    * @return the module code text input for the form
    * 
    */ 
    public function getModuleCopyrightElement()
    {
    	//Check for serialized element
    	$this->copyright = $this->getSession('copyright', NULL);
        //Create an element for the input of module code
        $objElement = new textinput ("copyright");
        //Set the field type to text
        $objElement->fldType="text";
        $objElement->size=40;
        if (isset($this->copyright)) {
            return $this->copyright;
        } else {
            //Add the copyright element to the form
            return $objElement->show();
        }
    }
    
    
    /**
    * 
    * Method to return database classname input to the form
    * 
    * @access public
    * @return the database classtext input for the form
    * 
    */ 
    public function getModuleDataTableElement()
    {
    	//Check for serialized element
    	$this->databaseclass = $this->getSession('databaseclass', NULL);
        //Create an element for the input of module code
        $objElement = new textinput ("databaseclass");
        //Set the field type to text
        $objElement->fldType="text";
        $objElement->size=40;
        if (isset($this->databaseclass)) {
            return $this->databaseclass;
        } else {
            //Add the $title element to the form
            return $objElement->show();
        }
    }
    
    /**
    * 
    * Method to return modulename text input to the form
    * 
    * @access public
    * @return the module code text input for the form
    * 
    */ 
    public function getModuleNameElement()
    {
    	//Check for serialized element
    	$this->modulename = $this->getSession('modulename', NULL);
        //Create an element for the input of module code
        $objElement = new textinput ("modulename");
        //Set the field type to text
        $objElement->fldType="text";
        $objElement->size=40;
        if (isset($this->modulename)) {
            return $this->modulename;
        } else {
            //Add the modulename element to the form
            return $objElement->show();
        }
    }
    
    /**
    * 
    * Method to return modulename text input to the form
    * 
    * @access public
    * @return the module code text input for the form
    * 
    */ 
    public function getModuleDescriptionElement()
    {
    	//Check for serialized element
    	$this->moduledescription = $this->getSession('moduledescription', NULL);
        //Create an element for the input of module code
        $objElement = new textinput ("moduledescription");
        //Set the field type to text
        $objElement->fldType="text";
        $objElement->size=40;
        if (isset($this->moduledescription)) {
            return $this->moduledescription;
        } else {
            //Add the $title element to the form
            return $objElement->show();
        }
    }
    
    /**
    * 
    * Method to return upload (submit) button to the form
    * 
    * @access public
    * @return the upload button for the form
    * 
    */ 
    public function getSubmitButton()
    {
        // Create an instance of the button object
        $this->loadClass('button', 'htmlelements');
        // Create a submit button
        $objElement = new button('submit');	
        // Set the button type to submit
        $objElement->setToSubmit();	
        // Use the language object to add the word save
        $objElement->setValue(' ' . $this->objLanguage->languageText("mod_generator_generate", 
  		  "generator").' ');
        // return the button to the form
        return "&nbsp;" . $objElement->show()  
          . "<br />&nbsp;";
    }
    
}
?>