<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 * This object will handle the code generation for the chisimba based phpunit test classes.
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert <charl.mert@gmail.com>
 */

class generate extends object
{
    /**
     * Code Analyzer Object
     *
     * @var object
     */
    public $objCodeAnalyzer;

    /**
     * Checklist Code Objects. Stores all the objects collected for the specified classes.
     *
     * @var object
     */
    public $classesArr;


	/**
	 * Class Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function init()
	{
		try {
			$this->objCodeAnalyzer = $this->getObject('codeanalyzer', 'phpunit');
			$this->objConfig = $this->getObject('altconfig', 'config');
            $this->objDbManager = $this->objEngine->getDbManagementObj();
			$this->objLanguage =$this->getObject('language', 'language');
            $this->classesArr = array();
		} catch (Exception $e){
			throw customException($e->getMessage());
			exit();
		}
	}


	/**
	 * Method to kick off the test case code generation
	 *
	 * @param moduleClassPath The path to the modules classes
	 * @access public
	 * @return HTML
	 */
	public function generateChecklistClass($moduleClassPath, $moduleName)
	{
		//Loop through the selected classes
		if (is_dir($moduleClassPath)) {

			$filesArr = $this->objCodeAnalyzer->getAllClasses($moduleClassPath);

            //Populating the classes array to be used later when generating the main checklist skeleton
            foreach($filesArr as $file=>$fpath) {
                $this->classesArr[$file] = $fpath;
            }            
            $templateCode = '<?PHP';
            //Generating template code for the logical Data Add Methods
            //--$methods = $this->objCodeAnalyzer->getInitMethods($fpath);
            //--$templateCode .= $this->generateChecklistInitCode($methods, $fpath, $moduleName);
            $methods = 'init';
            $templateCode .= $this->generateChecklistInitCode($moduleName);

            //Generating template code that will resolve and alert on missing dependancies
            $templateCode .= $this->generateChecklistDependacyCheckCode($moduleName);

			foreach($filesArr as $file=>$fpath) {

                //files array was posted using filename with .php replaced with _php
				$postKey = str_replace('.', '_', $file);
				$postFile = $this->getParam($postKey, '');

				//Using selected target classes to generate for
				if ($postFile == 'on') {
					//Extracting MDB2 Data Management Methods
    	            //TODO: Get Selected Method Pattern Groups and generate
					$this->objCodeAnalyzer->useMdb2Layer();

                    //Generating template code for the logical Data Add Methods
                    $addMethods = $this->objCodeAnalyzer->getDataAddMethods($fpath, $moduleName);
                    //Generating template code for the logical Data Edit Methods
                    $editMethods = $this->objCodeAnalyzer->getDataEditMethods($fpath, $moduleName);

                    //Logical Add Data Management
                    if (count($addMethods)  >= 1) {

                        $templateCode .= '

    /*
     * =============== Logical Add Data Methods ======='.end(explode('/', $fpath)).'========
     */
        '."\n";
                        $templateCode .= $this->generateChecklistTemplateCode($addMethods, $fpath, $moduleName);
                    }
                    
                    //Logical Edit Data Management
                    if (count($editMethods)  >= 1) {
                        $templateCode .= '

    /*
     * =============== Logical Edit Data Methods ======='.end(explode('/', $fpath)).'========
     */
        '."\n";
                        $templateCode .= $this->generateChecklistTemplateCode($editMethods, $fpath, $moduleName);
                    }

                    $templateCode .= $this->generateChecklistTableFieldCheckCode($moduleName);

				}

				$counter++;
			}

            //Closing CheckList Container Class
            $templateCode .= '}'."\n?>";

            $saveFileName = $moduleName.'Checklist.php';
            $this->saveChecklist($saveFileName, $moduleName, $templateCode);


		} else {
			//Error: Mofule Path Doesn't Exist
			echo "Module Path ($moduleClassPath) doesn't exist or isn't readable chmod 755 && chwon -R www-data:www-data /packages/<your-package>";
		}

		//Extract methods

		//Generate checklist and phpunit test files.

		//$this->obj->deleteMapping($mappingId);

		return TRUE;
	}

    /**
     * Method to generate checklist template code
     *
     * @param $methods An array of full methods
     * @access public
     * @return HTML
     */
    public function generateChecklistTemplateCode($methods, $fpath, $moduleName)
    {
        $templateCode = '';

        $className = $this->objCodeAnalyzer->getClassName($fpath);

        if (count($methods) >= 1) {
            foreach ($methods as $method=>$params) {
                $decl = "    public function $moduleName".'_'."$className".'_'."$method($params) {";
                $templateCode .= $decl . "\n";
                $templateCode .= '        return $this->'.$className.'->' . $method .'(' .$params . ');' . "\n";
                $templateCode .= "    }\n\n";
            }
        }
        
        return $templateCode;
    }


    /**
     * Method to generate dependancy check code
     *
     * @param $methods An array of full method prototype declarations: e.g. "function someMethod ()"[0]
     * @access public
     * @return HTML
     */
    public function generateChecklistDependacyCheckCode($moduleName)
    {
        $genClassName = $moduleName . 'Checklist';

        $templateCode = '';
        
        $deps = $this->objCodeAnalyzer->getDependancies($moduleName);
        if (is_array($deps)) {
            foreach ($deps as $dep) {
                $templateCode .= '

    /*
     * Check '. $dep .' dependancy:
     */
    public function is_'.$dep.'ModuleInstalled(){
        return $this->objModule->checkIfRegistered(\''.$dep.'\');
    }

    ';
                
            }
        }
    
        return $templateCode;
    }


    /**
     * Method to generate checklist initialization code
     *
     * @param $methods An array of full method prototype declarations: e.g. "function someMethod ()"[0]
     * @access public
     * @return HTML
     */
    public function generateChecklistInitCode($moduleName)
    {
        $genClassName = $moduleName . 'Checklist';

        $templateCode = '
$GLOBALS[\'kewl_entry_point_run\'] = true;
require_once \'classes/core/engine_class_inc.php\';

class '.$genClassName.'
{
    public $eng;
    public $security = array();
    public $session;

    //Generated checklist members
';
    if (!empty($this->classesArr)) {
        foreach ($this->classesArr as $file=>$fpath) {
            $objName = preg_replace('/_class.*/', '', $file);
            $templateCode .= '    public $' . $objName . ';' . "\n";
        }
    } else {
        echo "Init Code Generator Exception Caught: Empty Class Array | Can't create objects";
        exit();
    }
    
$templateCode .= '

    public function '.$genClassName.'() {
        $this->eng = new engine;
        $this->objModule = $this->eng->getObject(\'modules\', \'modulecatalogue\');

        //Generated checklist member initialization
';

    foreach ($this->classesArr as $file=>$fpath) {
        $objName = preg_replace('/_class.*/', '', $file);
        $templateCode .= '        $this->' . $objName . ' = $this->eng->getObject(\''.$objName.'\', \''.$moduleName.'\');'."\n";
    }

$templateCode .= '
    }
';
        return $templateCode;
    }



    /**
     * Method to generate dependancy check code
     *
     * @param $methods An array of full method prototype declarations: e.g. "function someMethod ()"[0]
     * @access public
     * @return HTML
     */
    public function generateChecklistTableFieldCheckCode($moduleName)
    {
        $genClassName = $moduleName . 'Checklist';

        $templateCode = '';


        $tables = $this->objCodeAnalyzer->getTables($moduleName);
        if (is_array($tables)) {
            foreach ($tables as $tbl) {
                //Getting a list of fields
                //TODO: Update Pear MDB2_Driver_mysql to include the listTableFields method
                $fields = $this->objDbManager->listTableFields($tbl);
                
                foreach ($fields as $field) {
                    $nspace = $moduleName .'_'. $tbl .'_'. $field;

                    $templateCode .= '

    /*
     * Methods for validating the Table Fields
     * Module: '.$moduleName.'
     * Table: '.$tbl.'
     * Field: '.$field.'
     */

    public function '.$nspace.'($record){
        if (isset($record[\''.$field.'\'])){
            return $record[\''.$field.'\'];
        } else {
            return FALSE;
        }
    }
';
                }
            }
        }

        return $templateCode;
    }


    /**
     * Method to write the checklist to a file in 'usrfiles/phpunit/<yourmodule>/'
     *
     * @param $methods An array of full method prototype declarations: e.g. "function someMethod ()"[0]
     * @access public
     * @return HTML
     */
    public function saveChecklist($fileName, $moduleName, $checklistSource)
    {
        $this->phpunitBasePath = $this->objConfig->getcontentBasePath()."phpunit/$moduleName/";
        
        //Ensuring the base directory exists
        if(!file_exists($this->phpunitBasePath))
        {
            mkdir($this->phpunitBasePath, 0777, true);
        }

        //Writting the file to disk
        $fp = fopen($this->phpunitBasePath . $fileName, 'w') or log_debug('PHPUnit: Could\'nt Save file: ['.$this->phpunitBasePath.$fileName.']');
        fwrite($fp, $checklistSource);
        fclose($fp);

        return TRUE;
    }




}

?>
