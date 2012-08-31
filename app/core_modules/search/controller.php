<?php

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller class for the Lucene implementation of Chisimba
 *
 * @author    Paul Scott, Tohir Solomons
 * @copyright AVOIR UWC
 * @access    public
 * @package   search
 */
class search extends controller
{

	/**
	 * Language object
	 *
	 * @var object
	 */
	public $objLanguage;



    /**
	* This is a method to determine if the user has to be logged in or not
    */
    public function requiresLogin() // overides that in parent class
    {
       return FALSE;
    }


	/**
	 * Constructor - public init function
	 * This is the standard initialisation method for the framework
	 *
	 * @param void
	 * @return void
	 */
	public function init()
	{
		// instantiate objects
        try{
			//the language object
        	$this->objLanguage = $this->getObject('language','language');
        }
        //catch any exceptions that may have occured and pass them to the error handler
        catch (customException $e){
        	//output the standard error page
       		echo customException::cleanUp($e);
       		//kill the script to suppress any further errors
        	exit();
        }

	}

	/**
	* The Dispatch  method that the framework needs to evoke the controller
	*
	* @param string $action Action to be Taken
	* @return mixed template
	*/
	public function dispatch($action)
	{
	    try{
            $this->setLayoutTemplate('search_layout_tpl.php');
            switch ($action)
            {
                // Display Search Results
                default:
                    return 'search_results.php';
                
                // Test Indexer
                case 'tohirtest':
                    $objIndexData = $this->getObject('indexdata');
                    $objIndexData->tohirTest();
                    
                // Builde Includes List
                case 'buildincludes':
                    return $this->buildIncludes();
            }
	    }
		catch (customException $e){
       		echo customException::cleanUp($e);
        	exit();
	    }
	}
	
	/**
     * Method to build a list of include files
     *
     * This module uses the adaptor pattern for including the Zend_Lucene_Search files
     * which also facilitates smooth upgrading when new versions become available.
     *
     * The update process is:
     * - Create a sub directory in the resources folder corresponding with the release version of the Zend Framework
     * - Only copy the search directory from the ZendFramework/library/Zend to the subdirectory in the above step
     * - Open each file in the search directory, and comment out the require_once lines
     * - Update the $subFolderVersion variable below to match the subdirectory/version
     * - Run index.php?module=search&action=buildincludes
     * - Copy the results
     * - Open core_modules/search/resources/luceneincludelist.php
     * - Remove Old stuff and paste new ones into there
     * - In luceneincludelist.php, the exception and interface classes need to be on top
     */
	private function buildIncludes()
	{
		$objBuildIncludes = $this->getObject('buildincludes');
		
		$subFolderVersion = '1.7.3';
		
		$folder = $this->getResourcePath($subFolderVersion);
		
		$results = $objBuildIncludes->scanDirectory($folder);
        
        echo "// Load Exception Class<br />";
        echo "require_once('Exception.php');<br /><br />";
		
        echo "// Rest of Classes<br />";
		foreach ($results as $item)
		{
			$item = str_replace($folder, $subFolderVersion, $item);
			
			echo 'require_once(\''.$item.'\');<br />';
		}
	}
	
}
?>
