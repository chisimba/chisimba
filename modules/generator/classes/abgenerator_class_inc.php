<?php 
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
* Abstract class defining methods and properties that must be present
* in a generator class that extends it. This is the base class that all
* generator objects must extend. It provides some standard functionality
* that cuts across all code generator ojects so that the code
* does not have to be rewritten each time
* 
* Usaeage: class mygeneratorclass extends abgenerator implements ifgenerator
* 
* @author Derek Keats 
* @category Chisimba
* @package generator
* @copyright AVOIR
* @licence GNU/GPL
*/
abstract class abgenerator extends object
{
    /**
    * 
    * @var string $classCode String to hold the content of the 
    * class being created
    * 
    */
    public $classCode;
    
    /**
    * 
    * @var string object $objUser String to hold the user object
    * 
    */
    public $objUser;
    
    /**
    * 
    * @var string array $unDeclaredMethods String to hold the array of undeclared methods
    * 
    */
    public $unDeclaredMethods;
    /**
    * @var string $generatorBaseDir The base path to the generators directory 
    * @access Private
    */
    public $generatorBaseDir;
    
    /**
     * 
     * Standard init method, instantiates user object
     * 
     */
    public function init()
    {
    	//Get an instance of the user object
        $this->objUser = $this->getObject('user', 'security');
        //Get the base dir of the generators and set it here
        $this->generatorBaseDir = $this->getResourcePath("generators") ."/";
    }
    
    /**
     * 
     * Method to read the object skeleton
     * 
     */
    public function loadSkeleton($classItem, $objectType='class')
    {
        //Load the XML class template
        $xml = simplexml_load_file($this->generatorBaseDir 
          . $classItem . "/" . $classItem . "_" . $objectType 
          . "_skeleton.xml"); 
        //Loop through and include the code
        foreach($xml->item as $item) {
            $this->classCode .= $item->code;
        }
        //Put in the start php <?php code
        $this->startphp();
        //Put in the end php code
        $this->endphp();
        return TRUE;
    }
    

    /**
    *
    * Method to load an XML file for processing
    *
    */
    function readXmlFile($filePath)
    {
        return simplexml_load_file($filePath); 
    }        
    
    /**
     * 
     * Method to get the properties of the class and insert them
     * into the building up class code
     * 
     * @param string $classItem The class type being built (eg controller, db, etc)
     * @param string $itemType The item type being inserted (must be either properties or methods)
     * @access Public
     * 
     */
     public function insertItem($classItem, $objectType, $itemType)
     {
         //Load the XML class template
         $fileName = $this->generatorBaseDir
          . $classItem . "/" . $classItem . "_" . $objectType  . "_" 
          . $itemType . ".xml";
			$xml = simplexml_load_file($fileName);
         //Initialize the string that we are reading into
         $classInsert=""; 
         //Loop through and include the code
         foreach($xml->item as $item) {
             $classInsert .= $item->code;
         }
         $pattern = "{" . strtoupper($itemType) . "}";
         //Insert the classProperties in place of the parsecode {PROPERTIES}
         $this->classCode = str_replace($pattern, $classInsert, $this->classCode);
     }
     
    /**
    * 
    * Method to return the module author as the logged in user and insert
    * it into the code of the class being built in place of the {AUTHOR} 
    * parsecode
    * 
    * @access Public
    * 
    */
    public function author()
    {
        $author = $this->objUser->fullName();
        $this->classCode = str_replace("{AUTHOR}", $author, $this->classCode);
        return TRUE;
    }
    
    /**
    * 
    * Method to return the module code and insert it into the code of the 
    * class being built in place of the {MODULECODE} parsecode
    *  
    * @access Public
    * 
    */
    public function modulecode()
    {
    	//Get the module code from parameter
        $moduleCode = $this->getItem('modulecode');
        //Do the replace
        $this->classCode = str_replace("{MODULECODE}", $moduleCode, $this->classCode);
        return TRUE;
    }
    
    /**
    * 
    * Method to return the module name and insert it into the code of the 
    * class being built in place of the {MODULENAME} parsecode
    *  
    * @access Public
    * 
    */
    public function modulename()
    {
    	//Get the module code from parameter
        $moduleName = $this->getItem('modulename');
        //Do the replace
        $this->classCode = str_replace("{MODULENAME}", $moduleName, $this->classCode);
        return TRUE;
    }
    
    /**
    * 
    * Method to return the classname name and insert it into the code of the 
    * class being built in place of the {CLASSNAME} parsecode
    *  
    * @access Public
    * 
    */
    public function classname()
    {
    	//Get the module code from parameter
        $classname = $this->getItem('classname');
        //Do the replace
        $this->classCode = str_replace("{CLASSNAME}", $classname, $this->classCode);
        return TRUE;
    }

    /**
    * 
    * Method to return the module description and insert it into the code of the 
    * class being built in place of the {MODULENAME} parsecode
    *  
    * @access Private
    * 
    */
    public function moduledescription()
    {
    	//Get the module description from parameter
        $moduleDescription = $this->getItem('moduledescription');
        //Do the replace
        $this->classCode = str_replace("{MODULEDESCRIPTION}", $moduleDescription, $this->classCode);
        return TRUE;
    }
    
    /**
    * 
    * Method to return the copyright and insert it into the code of the 
    * class being built in place of the {COPYRIGHT} parsecode
    *  
    * @access Private
    * 
    */
    public function copyright()
    {
        //Get the module sopyright from parameter
        $copyRight = $this->getItem('copyright');
        //Insert the copyright
        $this->classCode = str_replace('{COPYRIGHT}', $copyRight, $this->classCode);
    }
    
    /**
    * 
    * Method to return the email address of the author and 
    * insert it into the code of the class being built in place of 
    * the {EMAIL} parsecode
    *  
    * @access Private
    * 
    */
    public function email()
    {
        //Get the module sopyright from parameter
        $emailAddy = $this->objUser->email();
        //Insert the copyright
        $this->classCode = str_replace('{EMAIL}', $emailAddy, $this->classCode);
    }
    
            
    /**
    * 
    * Method to return the databasetable and insert it into the code of the 
    * class being built in place of the {DATABASETABLE} parsecode
    *  
    * @access Private
    * 
    */
    public function databasetable()
    {
        //Get the module sopyright from parameter
        $databaseTable = $this->getItem('databasetable');
        //Insert the database table instantiation
        $this->classCode = str_replace('{DATABASETABLE}', $databaseTable, $this->classCode);
    }
    
    /**
    * 
    * Method to return the databaseclass and insert it into the code of the 
    * class being built in place of the {DATABASECLASE} parsecode
    *  
    * @access Private
    * 
    */
	function databaseclass()
	{
	    //Read the database class
        $databaseclass = $this->getItem('databaseclass');
        //Insert the database classname
        $this->classCode = str_replace('{DATABASECLASS}', $databaseclass, $this->classCode);
	}  

    /**
     * 
     * Method to insert the start of the PHP code. Note that this
     * method must be here in order to comply with the parsecodes
     * in XXX_class_parsecodes.xml.
     * 
     * @access Private
     * 
     */
     private function startphp()
     {
        //Put in the start php <?php code
        $this->classCode = str_replace('{STARTPHP}', '<?php', $this->classCode);
        return TRUE;
     }
     
    /**
     * 
     * Method to insert the start of the PHP code. Note that this
     * method must be here in order to comply with the parsecodes
     * in XXX_class_parsecodes.xml.
     * 
     * @access Private
     * 
     */
     private function endphp()
     {
        //Put in the start php <?php code
        $this->classCode = str_replace('{ENDPHP}', '?>', $this->classCode);
        return TRUE;
     }
     
    /**
    * 
    * Format the code for display as HTML
    * 
    */
	public function prepareForDump()
	{
		$this->classCode = htmlentities($this->classCode);
	    $this->classCode = str_replace(' ', '&nbsp;', $this->classCode);
//	    //$this->classCode = nl2br($this->classCode);
        return TRUE;
	}
	
	/**
	 * 
	 * A method to get a method from a particular XML methods template
	 * 
	 * @param string $classItem The name of the class to get (e.g. controller, dbtable, etc)
	 * @param string $methodName The name of the method to extract from the class
	 * 
	 */
	public function getMethod($classItem, $methodName)
	{
	    if ($this->methodXml == NULL || $this->methodXML="") {
	    	$this->methodXml = simplexml_load_file($this->generatorBaseDir
              . $classItem . "class_methods.xml");
	    }
	    $xPathParam = "//item[@name = '" . $methodName . "']";
	    $ret = $xml->xpath($xPathParam);
	}
	
   
    /**
     * 
     * Method to cleanup unused {TAGS} in from the XML template.
     * It reads an XML file containing the tags to be cleaned up. By
     * using an XML file, we can keep the tags out of the code and
     * keep this code quite simple.
     * 
     */
    public function cleanUp()
    {
    	$chk = $this->getParam('bypasscleanup', FALSE);
    	if ($chk !== 'TRUE') {
	        //Load the XML template tagnames for scrubbing
	        $xml = simplexml_load_file($this->getResourcePath("") . "/template-tagnames.xml");
	        //Loop through and clean up any unused tags in the code
	        foreach($xml->tag as $tag) {
	            $this->classCode = str_replace($tag->tagtext, NULL, $this->classCode);
	        }
    	}	        
    }
    
    /**
     * 
     * Method to insert the logger code into the init statement
     * by replacing the {LOGGER} template code.
     * 
     * @TODO bring this in line with the new approach ===================================================
     * 
     */
    public function logger()
    {
        $str = "        //Get the activity logger class\n"
          . "        \$this->objLog=\$this->newObject('logactivity', 'logger');\n"
          . "        //Log this module call\n"
          ."        \$this->objLog->log();\n";
        $this->classCode = str_replace("{LOGGER}", $str, $this->classCode);
        return TRUE;
    }
    
    /**
    * 
    * Method to return the module author as the logged in user
    * 
    * @return string The full name of the author
    * 
    */
    protected function getAuthor()
    {
        return $this->objUser->fullName();
    }
    
    /**
     * 
     * Method to validate that all the placeholders (parsecodes of form {CODE}) 
     * have a method in the class corresponding to them
     * 
     * @return FALSE | array of missing codes
     *  
     */
     function validateParseCodes()
     {
        //Initialize the ret array
        $ret = array();
     	$arParseCodes = $this->getParseCodes();
     	$arMethods = get_class_methods($this);
     	foreach ($arParseCodes as $cd) {
     		$cd = strtolower($cd);
     	    if (!in_array($cd, $arMethods)) {
   				$ret[]=$cd;
		    }
     	}
     	if (count($ret) > 0) {
     		$this->unDeclaredMethods=$ret;
     	    return FALSE;
     	} else {
     	    return TRUE;
     	}
     }
     
     /**
     *
     * Method to return the parsecodes (place holders) from the text
     * so they can be validated to make sure that the class doing the 
     * parsing has a method conforming to the parsecode (in lower case)
     *
     */
     function getParseCodes()
     {
        $regExpr = "/\{([A-Z]*)\}/sU";
		if (preg_match_all($regExpr, $this->classCode, $elems)) { 
        	foreach ($elems[1] as $elem) {
        	    $ret[]=$elem;
        	}
        } else {
            $ret = NULL;
        }
        return $ret;
     }
     
     /**
     *
     * Method to return the value of an item by looking in params
     * and then in the session cookies.
     *
     */
     public function getItem($item)
     {
        //Get the item from parameter
        $value = $this->getParam($item, NULL);
        //If there is no parameter, check the session cookies
        if ($value == NULL) {
            $value = $this->getSession($item, strtoupper($item) . '{_UNSPECIFIED}');
        }  else {
            //Serialize the variable to the session since we are geting it from a param
			$this->setSession($item, $value);
        }
        return $value;
     }
} 
?>