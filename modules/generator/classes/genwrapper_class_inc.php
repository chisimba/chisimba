<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check
$this->loadclass("abgenerator", "generator");
$this->loadclass("ifgenerator", "generator");
//require_once('modules/generator/classes/abgenerator_class_inc.php');
//require_once('modules/generator/classes/ifgenerator_class_inc.php');

/**
* 
* Class to generate a Chisimba edit template
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
class genwrapper extends abgenerator implements ifgenerator
{
    /**
    * 
    * @param string $wrModule The module in which the class is found
    * 
    */
    private $wrModule;
    
    /**
    * 
    * @param string $classFile The filename of the class being wrapped
    * 
    */
    private $classFile;
    
    /**
    * 
    * @param string $objToWrap The name of the class being wrapped
    * 
    */
    private $objToWrap;
    
    /**
    * 
    * @param string $className The name of the class being created as the wrapper
    * 
    */
    public $className;
    
    public $strClass;
  
    /**
    * @var string $generatorBaseDir The base path to the generators directory 
    * @access Private
    */
    public $generatorBaseDir;
   
    /**
    * 
    * Standard init method reading the parameters from the
    * form that has been submitted
    * 
    */
    public function init()
    {
        //Get the values needed to do the work
        $this->classFile = $this->getParam('filename', NULL);
        $this->wrModule = $this->getParam('modulecode', NULL);
        $this->params = $this->getParam('params', NULL);
        $this->className = $this->getParam('classname', NULL);
        parent::init();
    }
   
	/**
    * 
	* Method to generate the class for the controller
    * 
    * @param string $className The name of the wrapper class 
    * to generate
    * 
	*/
	public function generate($className=NULL)
	{
        //Load the class to be wrapped using require_once
        $this->loadWrapClass();
        
        //Get the name of the class to wrap from the filename
        $this->objToWrap = $this->getWrapClassName();
        //Prepare the contents of the wrapper from the XML file
        $this->prepareWrapper();
        //Replace template element for name of class
        $this->classCode = str_replace('{WRAPCLASS}', 
          $this->objToWrap, $this->classCode);
        $this->classCode = str_replace('{WRAPCLASSINSTANCE}', 
          $this->objToWrap, $this->classCode);
        $this->classCode = str_replace('{WRAPCLASSPARAMS}', 
          $this->getConstructorParams(), $this->classCode);
        $this->classCode = str_replace('{WRAPPERCLASS}', 
          $this->className, $this->classCode);
        
        //Insert the correct code to load the wrapped class
        $modPathRep = "\$this->getResourcePath('" . $this->classFile . "', '" . $this->wrModule . "')";  
        $this->classCode = str_replace('{WRAPCLASSSFULLPATH}', 
          $modPathRep, $this->classCode);
          
        
          
        //Start up the class
        $objWrapee = $this->instantiateClass();
        
        //Get all the properties
        $this->classCode = str_replace('{METHODS}', 
          $this->getMethods($this->objToWrap), $this->classCode);
          
        //Get all the methods
        $this->classCode = str_replace('{PROPERTIES}', 
          $this->getProperties($this->objToWrap), $this->classCode);

        //Clean up unused template tags
        $this->cleanUp();
        $this->prepareForDump();
	    return $this->classCode;
	}
    
    /**
    * 
    * Method to load the class being wrapped
    * 
    */
    private function loadWrapClass()
    {
        //load the class
        require_once($this->getResourcePath($this->classFile, $this->wrModule));
    }
    
    /**
    * 
    * Method to get the name of the class being wrapped. The class is 
    * fopened, and a regex is used to extract the name.
    * 
    * @return string The name of the class being wrapped
    * 
    */
    private function getWrapClassName()
    {
        $f = $this->getResourcePath($this->classFile, $this->wrModule);
        //Read the file into a string
        $fp = fopen($f, "r");
        $this->strClass = fread($fp, filesize($f));
        fclose($fp);
        //Parse the string and extract the class name
        $regExpr = "/(class )(.*)(\{)/isU";
        if (preg_match($regExpr, $this->strClass, $elems)) { 
            $ret = $elems[2];
        } else {
            $ret = "{COULDNOTEXTRACTCLASSNAME}";
        }
        return trim($ret);
    }
    
    /**
    * 
    * Method to instantiate the class being wrapped. It uses the Reflection
    * API to enable the methods and properties to be extracted from it.
    * 
    */
    private function instantiateClass()
    {
        $clName = trim(strtolower($this->objToWrap));
        $this->objWrapped = new ReflectionClass($clName);
    }

    /**
    * 
    * Method to get all properties of the class to be wrapped by
    * making use of the reflection API to do the introspection.
    * 
    * Note: This will not return private and protected properties
    * which is exactly as we want it. We do not want to wrap
    * private properties.
    * 
    * @return string array An array of all the properties of the
    * class being wrapped
    * 
    */
    private function getProperties()
    {
        //Initialize the return string
        $ret="";
        $counter=1;
        //Use reflection API and loop over the properties
        foreach ($this->objWrapped->getProperties() as $reflectProperty) {
            if (!$reflectProperty->isPrivate()) {
                $ret .= "    public \$" . $reflectProperty->getName() . ";\n";
            }
        }
        return $ret;
    }
    
    /**
    * 
    * Method to get all methods of the class to be wrapped
    * 
    * Note: This will not return private and protected methods
    * which is exactly as we want it. We do not want to wrap
    * private methods. This uses the reflection API to carry 
    * out the introspection.
    * 
    * @return string array An array of all the methods of the
    * class being wrapped
    * 
    */
    private function getMethods()
    {
        //Use reflection API and loop over the methods
        foreach ($this->objWrapped->getMethods() as $reflectMethod) {
            //Get the name of the method that we are in
            $method = $reflectMethod->getName();
            //We do not wrap the constructor method of the class
            if ($method !== "__construct") {
                //We do not need to wrap private methods
                if (!$reflectMethod->isPrivate()) {
                    $params = "";
                    $ar = $reflectMethod->getParameters();
                    $prCount = count($ar);
                    $counter = 1;
                    foreach($ar as $param) {
                        $params .= "\$" . $param->getName();
                        //If it allows NULL then add this to the output
                        if ($param->allowsNull) {
                            $params .= "=NULL";
                        }
                        if ($counter !== $prCount) {
                            $params .= ",";
                        }
                        $counter++;
                    }
                    $ret .= "\n    /**\n    *\n    * Wrapper method for " 
                      . $method . " in the " . $this->objToWrap . "\n    * "
                      . "class being wrapped. "
                      . "See that class for details of the \n" 
                      . "    * ". $method . "method.\n    *\n    */\n"
                      . "    public function " . $method . "(" . $params . ")\n    {\n"
                      . "        return \$this->obj" . $this->objToWrap . "wrapper->" 
                      . $method . "(". $params . ");\n    }\n";
                }
            }

        }

        return $ret;
    }
    
    /**
    * 
    * Method to extract the parameters from the method
    * being processed
    * 
    * @param string $mthd The method being parsed
    * 
    */
    function extractParams($mthd)
    {
        
        //Create a regex to match the current pattern
        $regExpr = "/(" . $mthd . "*\()(.*)(\))/isU";
        //echo $regExpr . "<br /><br /><br />";
        if (preg_match($regExpr, $this->strClass, $elems)) { 
            $ret = $elems[2];
            
        } else {
            $ret = NULL;
        }
        /**if ($ret == "") {
            echo "Did not find params in $mthd <br />";
        } else {
            echo "Found $ret in $mthd <br />";
        }*/
        return $ret;
    }

    /**
    * 
    * Method to get the constructor class methods
    * for use in instantiating the class
    * 
    */
    function getConstructorParams()
    {
        return $this->extractParams('__construct');
    }

    /**
    * 
    * Method to prepare the template for the code
    * to insert into. It uses XPATH to extract the code
    * from the XML tree
    * 
    */
    private function prepareWrapper()
    {
        $tmp="";
        $xml = simplexml_load_file($this->getResourcePath("") . "/wrapper-items.xml");
        //Initialize the class
        $ret = $xml->xpath("//item[@name = 'buildClass']");
        $this->classCode .= $ret[0]->code;
        //Add the init method
        $ret = $xml->xpath("//item[@name = 'initializeClass']");
        $tmp .= $ret[0]->code;
        $this->classCode = str_replace('{METHODS}', $tmp . "{METHODS}", $this->classCode);
        //Return a casual true
        return TRUE;
    }
}
?>