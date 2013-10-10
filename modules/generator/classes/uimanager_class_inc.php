<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* Class to read the XML templates for the generator UI and render
* the interface for user input and post processing
* 
* @author Derek Keats
* @category Chisimba
* @package generator
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class uimanager extends object
{
   /**
   *
   * @var string $objFormTable The table used to hold the form elements
   *
   * @access public 
   *
   */
   public $objFormTable;
   
   /**
   *
   * @var string $formXml The XML tree read from the XML template
   *
   * @access public 
   *
   */
   public $formXml;
   
   
   /**
   *
   * @var string $name The name of the module being created
   *
   * @access public 
   *
   */
   public $name;
   
    /**
    * @var string $generatorBaseDir The base path to the generators directory 
    * @access Private
    */
    private $generatorBaseDir;
   
    /**
    * 
    * Constructor class to initialize language 
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
        // Create an instance of the button object
        $this->loadClass('button', 'htmlelements');
        //Get the base dir of the generators and set it here
        $this->generatorBaseDir = $this->getResourcePath("generators") ."/";
    }
    


    /**
    *
    * Method to read the form to build the UI for the particular
    * itemType (e.g. controller, dbtable). 
    * It looks in modules/generators/$objectType for a file called
    * $objectType_ui_form.xml (e.g. controller_ui_form)
    * The form XML is read into memory for processing
    *
    * @param string $itemType The type of item being generated 
    *
    */
    public function readFormXml($objectType)
    {
        //Load the XML  
        $this->formXml = simplexml_load_file($this->generatorBaseDir 
          . $objectType . "/" . $objectType . "_ui_form.xml"); 
    }


    /**
    *
    * Method to genearte a form from the XML read into memory
    * using readFormXml.
    *
    */
    public function generateForm()
    {
        //Create an instance of the form class
        $objForm = new form( $this->getFormName() );
        //Set the action for the form to the uri with paramArray
        $objForm->setAction( $this->getFormAction() );
        //Set the displayType to 3 for freeform so we can make our own table
        $objForm->displayType=3;
        //Put the layout in a table
        $myTable = $this->newObject('htmltable', 'htmlelements');
        $myTable->cellspacing="2";
        $myTable->width="98%";
        $myTable->attributes="align=\"center\"";
        //Add the form title as the header
		// Add the heading to the content
		$objH = $this->getObject('htmlheading', 'htmlelements');
		//Heading <h3>
		$objH->type=3;
		$objH->str=$this->formXml->form[0]->title;
		$objForm->addToForm( $objH->show() );
		$ov = "<em>" . $this->formXml->form[0]->overview . "</em><br /><br />";
		$objForm->addToForm( $ov);
        foreach ($this->formXml->form[0]->items[0]->item as $formElement) {
        	//Create a tempvar for the form element in case there are 
        	//   radio and other array types in it.
        	$this->tmp = $formElement;
	        //Create an element for the input of the item and add it to the table
	        $myTable->startRow();
            $myTable->addCell(" ");
            $myTable->addCell($formElement->description);
            $myTable->endRow();
	        $myTable->startRow();
	        $myTable->addCell($formElement->label);
	        $method = trim($formElement->type);
	        $myTable->addCell($this->$method($formElement->name));
	        $myTable->endRow();
        }
        //Add the table
        $objForm->addToForm( $myTable->show() );
        //Render the form & return it
        return $objForm->show();
    }

    /**
    *
    * Method to genearte a form action from the XML by using XPATH
    * to read it from the DOM
    *
    * @return string The action for use in setting up the form
    *
    */
    public function getFormAction()
    {
	    $action = $this->formXml->form[0]->action;
	    $task = $this->formXml->form[0]->task;
	    $page = $this->formXml->form[0]->page;
        //Set up the form action to generate the item
        $paramArray=array(
          'action'=>$action,
          'task'=>$task,
          'objecttype'=>$this->getParam('objecttype', NULL),
          'page'=>$page);
        return $this->uri($paramArray);
    }

    /**
    *
    * Method to return the name of the form
    * 
    * @return The name of the form
    *
    */
    public function getFormName()
    {
      $this->formXml->form[0]->name;
    }


	/**
	 * 
	 * Method to create a text input based on data in the 
	 * XML form template
	 * 
	 * @param string $name The name for the textinput
	 * @return string The HTML code for the textinput
	 * 
	 */
	public function textinput($name)
	{
    	//Check for serialized element
    	$this->$name = $this->getSession($name, NULL);
        //Create an element for the input ofthe element value
        $objElement = new textinput($name);
        //Set the field type to text
        $objElement->fldType="text";
        $objElement->size = 60;
        if (isset($this->$name)) {
            return $this->$name;
        } else {
            //Add the named element to the form
            return $objElement->show();
        }
	}
	
	/**
	 * 
	 * Method to allow plain text to appear on the interface
	 * based on data in the XML form template
	 * 
	 * @param string $name The name for the plain text
	 * @return string The HTML code for the plain text
	 * 
	 */
	public function plaintext($name)
	{
    	//Check for serialized element
    	$this->$name = $this->getSession($name, NULL);
        if (isset($this->$name)) {
            return $this->$name;
        } else {
            //Add the named element to the form
            return $this->$name;
        }
	}
	
	/**
	 * 
	 * Method to create a radio button based on data in the 
	 * XML form template
	 * 
	 * @param string $name The name for the radio button
	 * @return string The HTML code for the radio button
	 * 
	 */
	 function radiobutton($name)
	 {
	    //Check for serialized element
    	$this->$name = $this->getSession($name, NULL);
        //Create an element for the input ofthe element value
        $objElement = new radio($name);
        //Get the item using Xpath
		$xPathParam = "//form/items/item[@elementname = '" . $name . "']";
		$opAr = $this->formXml->xpath($xPathParam);
        //Get an array of options and values
        $ret="";
        foreach ($opAr[0]->options[0] as $option) {
        	$v = trim( " " . $option->value[0]); #crude conversion to string
        	$l = " " . $option->txt[0] . " ";
        	$objElement->addOption($v, $l);
        }
        //Set the selected element
        if ($this->name !==NULL) {
            $objElement->setSelected($this->name);
        }
	    return $objElement->show();
	 }
	 
	 /**
	 *
	 * Method dot create a dropdown list of tables from the database
	 *
	 * @param string $name The name for the dropdown
	 * @return string The HTML code for the dropdown
	 *
	 */
	 public function dropdownfromtable($name)
	 {
	    //Check for serialized element
    	$this->$name = $this->getSession($name, NULL);
        //Get an instance of the schema generator
		$objSchema = $this->getObject('getschema');
		$ar = $objSchema->listDbTables();
        $this->loadClass('dropdown', 'htmlelements');
		$objDropDown = new dropdown('tablename');
		//$objDropDown->name=;
		$objDropDown->cssId = 'input_tablename';
		foreach ($ar as $entry) {
		    $objDropDown->addOption($entry, $entry);
		}
		return $objDropDown->show();
	 }

    /**
    * 
    * Method to return submit generator button to the form
    * 
    * @access public
    * @return the upload button for the form
    * 
    */ 
    public function submitbutton($name)
    {

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