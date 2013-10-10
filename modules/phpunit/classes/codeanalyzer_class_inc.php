<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 * This object will handle the code pattern matching to extract the methods
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert <charl.mert@gmail.com>
 */

class codeanalyzer extends object
{
	/**
	 * objMdb2
	 * Contains the MDB2 analyzer that logically extracts Data Managment methods agaist the MDB2 implementation.
	 * 
	 * @var objMdb2
	 */
	public $objMdb2;

    /**
     * _abstractionLayer
     * Holds the current database abstraction layer state to extract data management methods for
     *
     * @var _abstractionLayer
     */
    public $_abstractionLayer;

    /**
     * _className
     * Stateful, holds the current class name being operated on.
     *
     * @var _className
     */
    public $_className;

    /**
     * _startLine
     * Stateful, holds the current methods starting line (used when retrieving the function's content)
     *
     * @var _startLine
     */
    public $_startLine;

    /**
     * _endLine
     * Stateful, holds the current methods end line (used when retrieving the function's content)
     *
     * @var _endLine
     */
    public $_endLine;

    /**
     * _methodContent
     * Stateful, holds all of the class method function code in an array using the method name as the key
     *
     * @var _methodContentArr
     */
    public $_methodContentArr;

	/**
	 * Class Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function init()
	{
		try {
			$this->objMdb2 =$this->getObject('cp_mdb2', 'phpunit');
			$this->objConfig =$this->getObject('altconfig', 'config');
			$this->objLanguage =$this->getObject('language', 'language');
            $this->objModFile = $this->getObject ('modulefile', 'modulecatalogue');
            
            $this->_className = '';

			//Default abstraction layer to build for is MDB2
			$this->_abstractionLayer = 'MDB2';

		} catch (Exception $e){
			throw customException($e->getMessage());
			exit();
		}
	}

	/**
	 *
	 * Method to return a list of all the classes in the given module class directory
	 * @param string $path The path to the directory
	 * @return Array an array of classfiles and fullpaths arr['filename'] = 'fullpathtofile'
	 */

	public function getAllClasses($path) {
		if ($path == '') {
			return FALSE;
		}
		$status = TRUE;

		$arr = array();

		$dp = opendir($path);
		while (($file=readdir($dp))!=false )
		{
			if ($file!="." && $file!="..")
			{
				if (!is_dir($path."/".$file))
				{
					if (strtolower(end(explode('.', $file))) == 'php') {
						$arr[$file] = $path."/".$file ;
					}
				} else {
					//NOT Needed : Chisimba classes are all in the classes directory
					//Recursively iterating subdirectories
					//$display .= $this->getAllClasses($path . $file);
				}
			}
		}

		return $arr;
	}

	/**
	 *
	 * Method to get all methods of the class to be analyzed
	 *
	 * Note: This will not return private and protected methods
	 * which is exactly as we want it. We do not want to 
	 * private methods. This uses the reflection API to carry
	 * out the introspection.
	 *
     * @param $classFile The full path to the class to extract methods from
     * @param $className The name of the class (Will be regex'd if not specified).
     *
	 * @return string array An array of all the methods of the
	 * class being ped
	 *
	 */
	public function getAllMethods($classFile, $moduleName)
	{
		//Load the class to be reflected using require_once
		$this->loadClass($classFile, $moduleName);

		//Get the name of the class to from the filename
		$this->_className = $this->getClassName($classFile);
		//Start up the class
		$objee = $this->instantiateClass($this->_className);

		//Get all the methods
		//$this->classCode = str_replace('{METHODS}',
		$methods = $this->getMethods($classFile);//, $this->classCode;//);

		//Get all the properties
		//$this->classCode = str_replace('{PROPERTIES}',
		//$this->getProperties($this->_className), $this->classCode);

		return $methods;
	}


    /**
    *
    * Method to load the class to be reflected
    * If a duplicate class is loaded the class name is temporarily changed
    */
    public function loadClass($classFile, $moduleName)
    {
        if ($classFile == ''){
            echo "Exception Caught: Bad Class File : '$classFile'\n";
        }

        /*
        //Catching possible conflicts (Namespaces only available in PHP5.3!)
        $reflectClassFileName = end(explode('/', $classFile));
        $loadedClasses = get_required_files();
        foreach ($loadedClasses as $cl){
            $loadedClassFileName = end(explode('/', $cl));
            //log_debug ($loadedClassFileName . ' | '. $reflectClassFileName);
            if ($loadedClassFileName == $reflectClassFileName) {
                //Conflict Caught:
                log_debug ("PHPUnit: CAUGHT Duplicate File Exception $classFile");
                //Moving to temp area and renaming to fix require_once namespace issue
                $this->phpunitBasePath = $this->objConfig->getcontentBasePath()."phpunit/$moduleName/";
                $stamp = date('Ymd');
                $tmpClassFile = $this->phpunitBasePath . $stamp.'_'.$reflectClassFileName;
                echo $tmpClassFile; 
                copy($classFile, $tmpClassFile);
                $classFile = $tmpClassFile;
            }
        }
        */
        
        //load the class
        log_debug ("PHPUnit: CAUGHT Duplicate File Exception $classFile");
        require_once($classFile);
    }


   /**
    *
    * Method to get the name of the class being wrapped. The class is
    * fopened, and a regex is used to extract the name.
    *
    * @return string The name of the class being wrapped
    *
    */
    public function getClassName($classFile)
    {
        $f = $classFile;
        //Read the file into a string
        $fp = fopen($f, "r");
        $this->strClass = fread($fp, filesize($f));
        fclose($fp);

        //Removing the code comments for pure classname extraction
        $regExp = '/(\/\*.*\*\/)/isU';
        $this->strClass = preg_replace($regExp, '', $this->strClass);
        
        //Parse the string and extract the class name
        $regExpr = "/(class )(.*)(\{)/isU";
        if (preg_match($regExpr, $this->strClass, $elems)) {
            $clName = explode(' ', $elems[2]);
            $ret = $clName[0];
        } else {
            $ret = "{COULDNOTEXTRACTCLASSNAME}";
        }
        return trim($ret);
    }


    /**
    * 
    * Method to instantiate the class being ped. It uses the Reflection
    * API to enable the methods and properties to be extracted from it.
    *
    * @param string className The name of the class to reflect
    * 
    */
    public function instantiateClass($className)
    {
        $this->objReflection = new ReflectionClass($className);
    }

    /**
    * 
    * Method to get all properties of the class to be ped by
    * making use of the reflection API to do the introspection.
    * 
    * Note: This will not return private and protected properties
    * which is exactly as we want it. We do not want to 
    * private properties.
    * 
    * @return string array An array of all the properties of the
    * class being ped
    * 
    */
    public function getProperties()
    {
        //Initialize the return string
        $ret="";
        $counter=1;
        //Use reflection API and loop over the properties
        foreach ($this->objReflection->getProperties() as $reflectProperty) {
            if (!$reflectProperty->isPrivate()) {
                $ret .= "    public \$" . $reflectProperty->getName() . ";\n";
            }
        }
        return $ret;
    }
    
    /**
    * 
    * Method to get all methods of the class.
    * This function also populates the methodContentArr with the methods source code.
    *
    * @param string $classFile The full path to the class file
    * @return string array An array of all the methods of the class
    * 
    */
    public function getMethods($classFile)
    {
        $arrMethods = array();
        
        //Use reflection API and loop over the methods
        foreach ($this->objReflection->getMethods() as $reflectMethod) {
            //Get the name of the method that we are in
            $method = $reflectMethod->getName();

            //var_dump($this->isMethodInFile($method, $classFile));

            //Only grabbing methods from the current class (reflection API gets parent methods as well)
            if ($this->isMethodInFile($method, $classFile)) {
                //Get the method parameters
                if (!$reflectMethod->isPrivate() &&
                    $method != '') {

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
                }

                //Adding the method name as key and it's parameter list as the value
                $arrMethods[$method] = $params;
            }
            
        }

        return $arrMethods;
    }


    /**
    *
    * Method to check weather the specified method exists in the target file
    * This function also populates the methodContentArr with the methods source code.
    *
    * @return boolean TRUE if exists and FALSE if not
    *
    */
    public function isMethodInFile($method, $classFile)
    {
        //Reading the classes
        $contents = file_get_contents($classFile);

        //Checking if method is declared in the file
        $regEx = "/.*$method.*\(.*\).*\{/isU";
        if (preg_match($regEx, $contents)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
    *
    * Method to get the contents of a given methodName in the class file
    * TODO: Build regex function grepper for cases where code resides in one line (really paranoid ;-).
    *
    * @param string $methodName The name of the method you want the content code.
    * @param string $classFile The full path to the class file.
    * @return string array An array of all the methods of the class
    *
    */
    public function getMethodContent($methodName, $classFile)
    {
        $arrMethods = array();
        $methodContent = '';

        //Use reflection API and loop over the methods
        foreach ($this->objReflection->getMethods() as $reflectMethod) {
            //Get the name of the method that we are in
            $method = $reflectMethod->getName();

            if ($methodName == $method) {
                $startLine = $reflectMethod->getStartLine();
                $endLine = $reflectMethod->getEndLine();

                //Reading the functions code into a string to be returned.
                $contents = file_get_contents($classFile);
                $contents = explode("\n", $contents);

                for ($i = $startLine; $i < $endLine; $i++) {
                    if (isset($contents[$i])) {
                        $methodContent .= $contents[$i] . "\n";
                    }
                }
            }
            
        }

        return $methodContent;
    }

    /**
    * 
    * Method to extract the parameters from the method
    * being processed
    * 
    * @param string $mthd The method being parsed
    * 
    */
    public function extractParams($mthd)
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
    public function getConstructorParams()
    {
        return $this->extractParams('__construct');
    }

	/**
	 * Method to return the Data Management Methods responsible for the adding of data
     * for the current Database Abstraction Layer
	 *
	 * @param $classFile The full path to the class file to extract from
	 * @access public
	 * @return HTML
	 */
	public function getDataAddMethods($classFile, $moduleName)
	{
        $dataMethods = array();
        
		if ($this->_abstractionLayer == 'MDB2') {
            //Extracting all the methods for the given classFile
            $methods = $this->getAllMethods($classFile, $moduleName);

            foreach ($methods as $method=>$params) {
                $methodContent = $this->getMethodContent($method, $classFile);

                //Extracting the Data Mgmt Methods based on the MDB2 code pattern class
                //Get Add Methods:
                if ($this->objMdb2->isAddMethod($methodContent)) {
                    $dataMethods[$method] = $params;
                }

            }

		}

		return $dataMethods;
	}



    /**
     * Method to return the Data Management Methods responsible for the adding of data
     * for the current Database Abstraction Layer
     *
     * @param $classFile The full path to the class file to extract from
     * @access public
     * @return HTML
     */
    public function getDataEditMethods($classFile, $moduleName)
    {
        $dataMethods = array();

        if ($this->_abstractionLayer == 'MDB2') {
            //Extracting all the methods for the given classFile
            $methods = $this->getAllMethods($classFile, $moduleName);

            foreach ($methods as $method=>$params) {
                $methodContent = $this->getMethodContent($method, $classFile);

                //Extracting the Data Mgmt Methods based on the MDB2 code pattern class
                //Get Add Methods:
                if ($this->objMdb2->isEditMethod($methodContent)) {
                    $dataMethods[$method] = $params;
                }

            }

        }

        return $dataMethods;
    }
    

    /**
     * Method to return the Init / Constructor Methods responsible for initializing the class
     *
     * @param $classFile The full path to the class file to extract from
     * @access public
     * @return HTML
     */
    public function getInitMethods($classFile, $moduleName)
    {
        $dataMethods = array();

        if ($this->_abstractionLayer == 'MDB2') {
            //Extracting all the methods for the given classFile
            $methods = $this->getAllMethods($classFile, $moduleName);

            foreach ($methods as $method=>$params) {
                $methodContent = $this->getMethodContent($method, $classFile);

                //Extracting the Data Mgmt Methods based on the MDB2 code pattern class
                //Get Add Methods:
                //TODO: Finish codeAnalyzer's isInitMethod pattern matcher for detecting init / contructors based on reflection
                //if ($this->isInitMethod($methodContent)) {
                //    $dataMethods[$method] = $params;
                //}

                if (strtolower($method) == 'init') {
                    $dataMethods[$method] = $params;
                }

            }

        }

        return $dataMethods;
    }


       /**
        * This method performs a code pattern match againstthe reflected class
        * to check for initialization code
        *
        * TODO: Analyze init code data for patterns to match.
        *
        * @param $methodSource The string contents of the method
        * @access public
        * @return boolean
        */
        public function isInitMethod($methodSource)
        {
            //Checking for insert usage
            $regEx = '/.*newObject\(.*/isU';
            if (preg_match($regEx, $methodSource)) {
                return TRUE;
            }

            return FALSE;
        }

    /**
    *
    * Method to extract the modules actual Dependancies
    * being processed
    *
    * @param string $moduleName The name of the module
    *
    */
    public function getDependancies($moduleName)
    {
        $depArr = array();

        $registerFile = $this->objConfig->getModulePath() . $moduleName . '/register.conf';
        $regData = $this->objModFile->readRegisterFile($registerFile);
        $depArr = $regData['DEPENDS'];

        return $depArr;
    }

    /**
    *
    * Method to extract tables used by the module
    * being processed
    *
    * @param string $moduleName The name of the module
    *
    */
    public function getTables($moduleName)
    {
        $depArr = array();

        $registerFile = $this->objConfig->getModulePath() . $moduleName . '/register.conf';
        $regData = $this->objModFile->readRegisterFile($registerFile);
        $depArr = $regData['TABLE'];

        return $depArr;
    }



	/**
	 * Method to return the Data Management Methods for the current Database Abstraction Layer
	 *
	 * @access public
	 * @return HTML
	 */
	public function useMdb2Layer()
	{
		$this->_abstractionLayer = 'MDB2'; 
		return TRUE;
	}

}

?>
