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
* Class to generate a Chisimba dbtable class
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
class gendbtable extends abgenerator implements ifgenerator
{
    private $dataClass;
    public $arrayOfFields=array();
    
     /**
     * 
     * Standard init, calls parent init method to instantiate user
     * 
     */
    public function init()
    {
        parent::init();
    }
    
	/**
	 * Method to generate the class for the controller
	 */
	public function generate($className=NULL)
	{
		//Create an instance of the db schema object for getting list of fields
		$objDbSchema = $this->getObject("getschema", "generator");
		$this->arrayOfFields = $objDbSchema->getFieldNamesAsArray($this->getParam('tablename', NULL));
		//Load the skeleton file for the class from the XML		
        $this->loadSkeleton('dbtable', 'class');
        //Insert the properties
        $this->insertItem('dbtable', 'class', 'properties');
        //Insert the properties
        $this->insertItem('dbtable', 'class', 'methods');
        //Make sure we are not missing any parsecodes
        if ($this->validateParseCodes() !==TRUE) {
            foreach ($this->unDeclaredMethods as $missingMethod) {
                echo "The handler has no method corresponding to: $missingMethod <br />";
            }
            die();
        }
		//Insert the save code in place of {SAVECODE}
		$this->savecode();
        //Insert the module name
        $this->modulename();
        //Insert the module code
        $this->modulecode();
        //Insert the module description
        $this->moduledescription();
        //Insert the copyright
        $this->copyright();
        //Insert the email address of the suthor
        $this->email();
        //Insert the database table
        $this->databasetable();
        //Insert the database class info
        $this->dbtableclassname();
        //Insert the table name into the init method
        $this->addInitTable();
        //Addt he author
        $this->author();
        //Clean up unused template tags
        $this->cleanUp();
        $this->prepareForDump();
	    return $this->classCode;
	}
	
	function dbtableclassname()
	{
	    $tableName = $this->getParam('tablename', NULL);
	    if ($tableName !== NULL) {
	        $ar = explode("_", $tableName);
	        $rep = "db" . $ar[count($ar)-1];
	    } else {
	        $rep = "db_{UNSPECIFIED}";
	    }
	    //Do the replace
        $this->classCode = str_replace("{DBTABLECLASSNAME}", $rep, $this->classCode);
	}
	
	/**
	 * 
	 * Method ot insert the code for saving in place of {SAVECODE}
	 * 
	 * @access Private
	 * 
	 */
	public function savecode()
	{
	    //Insert the save methods (must have the array set first)
        $this->classCode = str_replace('{SAVECODE}', $this->getSaveMethods(), $this->classCode);
	}
	
    /**
    * 
    * Method to return the save methods for the data
    * 
    * @return String $ret The formatted code
    * 
    */
    private function getSaveMethods()
    {
        //Initialize the return string
        $ret="";
        //Loop and make the getparam statements
        foreach ($this->arrayOfFields as $fieldName) {
            $ret .= $this->makeGetParam($fieldName);
        }
        //Insert the save when coming from edit
        $ret .= $this->makeSave();
        return $ret;
    }
    
    /**
    * 
    * Method to make the getParam methods
    * 
    * @param string $fieldName THe name of the field to get
    * @return string $ret The formatted getparam statement
    * 
    */
    private function makeGetParam($fieldName)
    {
        switch ($fieldName) {
            case "id":
                $ret = "        //Retrieve the value of the primary keyfield \$" . $fieldName . "\n"
                  . "        \$" . $fieldName . " = \$this->getParam('" 
                  . $fieldName . "', NULL);\n";
                break;
            //Things that are formatted depending on add or edit                
            case "updated":
            case "dateModified":
            case "datemodified":
            case "modified":
            case "creatorId":
            case "creatorid":
            case "modifierId":
            case "modifierid":
            case "dateCreated":
            case "datecreated":
            case "created":
                $ret = NULL;
                break;
            //The default method to return the getting of the value
            default:
                $ret = "        //Retrieve the value of \$" . $fieldName . "\n"
                  . "        \$" . $fieldName . " = \$this->getParam('" 
                  . $fieldName . "', NULL);\n";
                break;
        } #switch
        return $ret;
    }
    
    /**
    * 
    * Method to make the save part of the code
    * 
    * @return string $ret The save part of the code
    * 
    */
    private function makeSave()
    {
        //Set up the top of the edit/save area
        $ret = "\n        //If coming from edit use the update code\n"
          . "        if (\$mode==\"edit\") {\n"
          . "            \$ar = array(\n";
        //How many fields are there
        $fldCount = count($this->arrayOfFields);
        //Set the index pointer to 0
        $iIndex = 0;
        //Loop over the items
        foreach ($this->arrayOfFields as $fieldName) {
            $ret .= $this->makeSaveFromEditItem($fieldName, $fldCount, $iIndex);
            $iIndex++;
        }
        $ret .= "            \$this->update('id', \$id, \$ar);\n"
          . "        } else {\n"
          . "            \$ar = array(\n";
        //Set the index pointer to 0
        $iIndex = 0;
        //Loop over the items
        foreach ($this->arrayOfFields as $fieldName) {
            $ret .= $this->makeSaveFromAddItem($fieldName, $fldCount, $iIndex);
            $iIndex++;
        }
        $ret .= "            \$this->insert(\$ar);\n        }\n";
        return $ret;
    }
    
    /**
    * 
    * Method to make the individual components of the save from edit. It 
    * uses the fldCount and iIndex to determine whether to continue
    * with a comma or end up with a bracket.
    * 
    * @param string $fieldName The name of the field
    * @param string $fldCount The number of fields
    * @param string $iIndex The position in the array of fields being processed
    * 
    */
    private function makeSaveFromEditItem($fieldName, $fldCount, $iIndex)
    {
        //Index and field count should be real numbers not array index
        $iIndex = $iIndex + 1; 
        $comma="";
        //Close up the array if we are at the end, else add , and new line
        if ($iIndex < $fldCount) {
            $comma = ",\n";
        } else {
            $comma = "\n            );\n";
        }
        switch ($fieldName) {
            //The id field is generated
            case "id":
            //These do not change on an edit
            case "creatorId":
            case "creatorid":
            case "dateCreated":
            case "datecreated":
            case "creationdate":
            case "creationDate":
            case "created":
                $ret = NULL;
                break;
            case "dateModified":
            case "datemodified":
            case "modified":
                $ret = "              '" . $fieldName 
                  . "' => \$this->now()"
				  . $comma . "\n";
                break;
            case "modifierId":
            case "modifierid":
            case "modifiedBy":
            case "modifiedby":
                $ret = "              '" . $fieldName 
                  . "' => \$this->objUser->userId()" 
                  . $comma . "\n";
                break;
            //The default method to return the getting of the value
            default:
                $ret = "              '" . $fieldName 
                  . "' => \$" . $fieldName . $comma;
                break;
        }
        return $ret;
    }

    /**
    * 
    * Method to make the individual components of the save frmo add. It 
    * uses the fldCount and iIndex to determine whether to continue
    * with a comma or end up with a bracket.
    * 
    * @param string $fieldName The name of the field
    * @param string $fldCount The number of fields
    * @param string $iIndex The position in the array of fields being processed
    * 
    */
    private function makeSaveFromAddItem($fieldName, $fldCount, $iIndex)
    {
        //Index and field count should be real numbers not array index
        $iIndex = $iIndex + 1; 
        //Close up the array if we are at the end, else add , and new line
        if ($iIndex < $fldCount) {
            $comma = ",\n";
        } else {
            $comma = "\n            );\n";
        }
        switch ($fieldName) {
            //Id is generated
            case "id":
            case "modifierId":
            case "modifierid":
            case "modified":
            case "datemodified":
            case "modificationdate":
                $ret = NULL;
                break;
            case "creatorId":
            case "creatorid":
                $ret = "              '" . $fieldName 
                  . "' => \$this->objUser->userId()" 
                  . $comma . "\n";
                break;
            case "created":
            case "datecreated":
            case "creationdate":
             	$ret = "              '" . $fieldName 
                  . "' => \$this->now()" 
				  . $comma . "\n";
            	break;
            //The default method to return the getting of the value
            default:
                $ret = "              '" . $fieldName 
                  . "' => \$" . $fieldName . $comma;
                break;
        }
        return $ret;
    }
    /**
    * 
    * Method to add the table name to the init method
    * 
    */
    private function addInitTable()
    {
        $this->classCode = str_replace("{DATABASETABLE}", $this->getParam("tablename", NULL), $this->classCode);
    }
    
}
?>