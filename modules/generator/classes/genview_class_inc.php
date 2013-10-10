<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check
$this->loadclass("abgenerator", "generator");
$this->loadclass("ifgenerator", "generator");

/**
* 
* Class to generate a Chisimba view template
* Creates a simple view template for a single table
* 
* Usaeage: class gencontroller extends abgenerator implements ifgenerator
*
* @author Derek Keats
* @category Chisimba
* @package generator
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class genview extends abgenerator implements ifgenerator
{
    private $dataClass;
    private $xml;
    
    /**
    * @var string $moduleUri The base Uri to the modules directory 
    * @access Private
    */
    private $moduleUri;
   
    /**
     * 
     * Standard init, calls parent init method to instantiate user
     * 
     */
    function init()
    {
        //Run the parent init to create common objects
        parent::init();
    }
   
	/**
	 * Method to generate the class for the controller
	 */
	function generate($className=NULL)
	{
	    //Load the skeleton file for the class from the XML		
        $this->loadSkeleton('view', 'template');
        //Load the fields into properties
        $this->loadFields();
	    /* NOTE: We cannot insert validateParseCodes() here because 
	             of the way in which the parsecodes are handled, both
	             inside the same loop */
	    $this->insertDataCode();
        //Insert the module clodes
        $this->moduleCode();

        //Clean up unused template tags
        $this->classCode = str_replace('{EDITFORM}', "", $this->classCode);
        $this->classCode = str_replace('{EDITGETFIELDS}', "", $this->classCode);        
        $this->cleanUp();

        $this->prepareForDump();
	    return $this->classCode;
	}
	
	/**
	*
	* Method to load the fields from the edit_template_fields.xml file
	* as properties of the current class for later handling. This file 
	* contains the methods that are used multiple times in building
	* the edit template.
	*
	*/
	function loadFields()
	{
        //Load the XML file of template fields
        $xml = simplexml_load_file($this->generatorBaseDir . "/view/view_template_fields.xml");
        //Extract the id field code using Xpath method
        $item = $xml->xpath("//item[@name = 'id']");
        $this->id = $item[0]->code;
        //Extract the textinput field code using Xpath method
        $item = $xml->xpath("//item[@name = 'textinput']");
        $this->textInput = $item[0]->code;
        //Extract the textarea field code using Xpath method
        $item = $xml->xpath("//item[@name = 'textarea']");
        $this->textArea = $item[0]->code;
        //Get the table name'
        $this->tableName = $this->getParam('tablename', NULL);
    }
    
    function insertDataCode()
    {
        //Get the data fields and structure
        $ar = $this->getFields();
        foreach ($ar as $record) {
            //Get the fieldname
            if (isset($record['fieldname'])) {
                $fieldName = $record['fieldname'];
            } else {
                $fieldName = "{UNDEFINED}";
            }
            $readField = "    \$" . $fieldName . " = \$ar['" . $fieldName . "'];\n";
            //Get the fieldType
            if (isset($record['type'])) {
                $fieldType = $record['type'];
            } else {
                $fieldType = "";
            }
            //Get the fieldLength
            if (isset($record['length'])) {
                $fieldLength = $record['length'];
            } else {
                $fieldLength = "0";
            }
            if ($fieldName=='id') {
                $fldCode = $this->id;
                $fldCode = str_replace('{FIELDNAME}', $fieldName, $fldCode);
                $this->classCode = str_replace('{EDITFORM}', $fldCode . "{EDITFORM}", $this->classCode);
                $this->classCode = str_replace('{EDITGETFIELDS}', $readField . "{EDITGETFIELDS}", $this->classCode);
            } else {
                switch($fieldName){
                    //The ones that we skip because they are internally generated
                	case "dateCreated":
                    case "datecreated":
                    case "creatorid":
                    case "creatorId":
                    case "modifierid":
                    case "modifierId":
                    case "datemodified":
                    case "dateModified":
                    case "updated":
                		break;
                    //The ones we don't want to skip
                	default:
                         switch ($fieldType) {
                            case 'text':
                                if ($fieldLength < 100) {
                                    $fldCode = $this->textInput;
                                } else {
                                    $fldCode = $this->textArea;
                                }
                                break;
                            case 'timestamp':
                                $fldCode = $this->textInput;
                                break;
                            default;
                                $fldCode = $this->textInput;
                                break;
                         } #switch
                        $fldCode = str_replace('{FIELDNAME}', $fieldName, $fldCode);
                        $this->classCode = str_replace('{EDITFORM}', $fldCode . "{EDITFORM}", $this->classCode);
                        $this->classCode = str_replace('{EDITGETFIELDS}', $readField . "{EDITGETFIELDS}", $this->classCode);
                        
                		break;
                } // switch
            } // if
        } // foreach
    } //fn

    /**
    * 
    * Method to prepare the template for the code
    * to insert into. It uses XPATH to extract the code
    * from the XML tree
    * 
    */
    function prepareTemplate()
    {
        $xml = simplexml_load_file($this->getResourcePath("") . "/-----edit-template-items.xml");
        //Initialize the template
        $ret = $xml->xpath("//item[@name = 'initializeTemplate']");
        $this->classCode = $ret[0]->code;
        //Add the heading to the template
        $ret = $xml->xpath("//item[@name = 'createHeading']");
        $this->classCode .= $ret[0]->code;
        //Set up the area for the code to create the edit form
        $ret = $xml->xpath("//item[@name = 'makeeditform']");
        $this->classCode .= $ret[0]->code;
        //Set up the render output code
        $ret = $xml->xpath("//item[@name = 'renderOutput']");
        $this->classCode .= $ret[0]->code;
        //Return a casual true
        return TRUE;
    }
    
    /**
    * 
    * Method to return a simple array of fields in the table
    * 
    */
    function getFields()
    {
        //Get an instance of the schema generator
		$objSchema = $this->getObject('getschema');
		$ar = $objSchema->getFieldSchema($this->tableName);
        return $ar;
    }
}
?>