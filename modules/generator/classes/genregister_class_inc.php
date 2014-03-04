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
* Class to generate a Chisimba register.conf (register.xml) file
* 
* Useage: class genregister extends abgenerator implements ifgenerator
*
* @author Derek Keats
* @category Chisimba
* @package generator
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class genregister extends abgenerator implements ifgenerator
{
	public $registerCode;
    
    /**
     * 
     * Standard init, calls parent init method to instantiate user
     * 
     */
    function init()
    {
        parent::init();
		//Initialize the register classcode
		$this->classCode='';
    }
    
	/**
	 * Method to generate the class for the controller
	 */
	function generate($className=NULL)
	{
		//Load the skeleton file for the register.conf from the XML		
        $this->loadSkeleton('register', 'conf');
        //Load the register.conf basic
        $this->coreparams();
        
        //Make sure we are not missing any parsecodes
        if ($this->validateParseCodes() !==TRUE) {
            foreach ($this->unDeclaredMethods as $missingMethod) {
                echo "The handler has no method corresponding to: $missingMethod <br />";
            }
            die();
        }
        //Insert the module description
        $this->moduledescription();
        //Put in the author
        $this->author();
        //Put in the module code
        $this->modulecode();
        //Put in the module name
        $this->modulename();        
        //Put in the menu category
        $this->menucategory();
        //Put in sidemenu category if exists
        $this->sidemenucategory();
        //Put today in for the module releasedate
        $this->modulereleasedate();
        //Put in whether the module is context aware or not
		  $this->contextaware();
		  //Put in whether the module has a dependency on contex or not
		  $this->dependscontext();
		  //Insert the basic language elements
		  $this->languageelems();
        //Clean up unused template tags
        $this->cleanUp();
        //Parse for dumping to screen
        $this->prepareForDump();
        //Return the template
	     return $this->classCode;
	}
	
	/**
	 * 
	 * Method to load the basic properties
	 * 
	 */
	function coreparams()
	{
	    //Insert the core parameter properties
        $this->insertItem('register', 'conf', 'basic');
	}
	
    /**
    * 
    * Method to return the menucategory and insert it into the code of the 
    * register.conf being built in place of the {MENUCATEGORY} parsecode
    *  
    * @access Public
    * 
    */
    public function menucategory()
    {
    	//Get the module code from parameter
        $menucategory = $this->getParam('menucategory', NULL);
        //If there is no parameter, check the session cookies
        if ($menucategory == NULL) {
            $menucategory = $this->getSession('menucategory', '{MENUCATEGORY_UNSPECIFIED}');
        } else {
            //Serialize the variable to the session since we are geting it from a param
			$this->setSession('menucategory', $menucategory);
        }
        //Do the replace
        $this->classCode = str_replace("{MENUCATEGORY}", $menucategory, $this->classCode);
        return TRUE;
    }
	
    /**
    * 
    * Method to return the sidemenucategory and insert it into the code of the 
    * register.conf being built in place of the {SIDEMENUCATEGORY} parsecode
    *  
    * @access Public
    * 
    */
    public function sidemenucategory()
    {
    	//Get the module code from parameter
        $sidemenucategory = $this->getParam('sidemenucategory', NULL);
        //If there is no parameter, check the session cookies
        if ($sidemenucategory == NULL) {
            $sidemenucategory = $this->getSession('sidemenucategory', NULL);
        } else {
            //Serialize the variable to the session since we are geting it from a param
			$this->setSession('sidemenucategory', $sidemenucategory);
        }
        //Do the replace
        if ($sidemenucategory !== NULL) {
            $this->classCode = str_replace("{SIDEMENUCATEGORY}", $sidemenucategory, $this->classCode);
        } else {
            $this->classCode = str_replace("SIDEMENU_CATEGORY: {SIDEMENUCATEGORY}", "COMMENT: No sidemenu category (insert SIDEMENU_CATEGORY here if this changes)", $this->classCode);
        }
        return TRUE;
    }
	
    /**
    * 
    * Method to return the modulereleasedate and insert it into the code of the 
    * register.conf being built in place of the {MODULERELEASEDATE} parsecode
    *  
    * @access Public
    * 
    */
    public function modulereleasedate()
    {
    	//Get the module code from parameter
        $modulereleasedate = $this->getParam('modulereleasedate', NULL);
        //If there is no parameter, check the session cookies
        if ($modulereleasedate == NULL) {
            $modulereleasedate = $this->getSession('modulereleasedate', '{MODULERELEASEDATE_UNSPECIFIED}');
        } else {
            //Serialize the variable to the session since we are geting it from a param
			$this->setSession('modulereleasedate', $modulereleasedate);
        }
        //Do the replace
        $this->classCode = str_replace("{MODULERELEASEDATE}", $modulereleasedate, $this->classCode);
        return TRUE;
    }
    
    /**
    * 
    * Method to return the contextaware and insert it into the code of the 
    * register.conf being built in place of the {CONTEXTAWARE} parsecode
    *  
    * @access Public
    * 
    */
    public function contextaware()
    {
    	//Get the module code from parameter
        $contextaware = $this->getParam('contextaware', NULL);
        //If there is no parameter, check the session cookies
        if ($contextaware == NULL) {
            $contextaware = $this->getSession('contextaware', '{CONTEXTAWARE_UNSPECIFIED}');
        } else {
            //Serialize the variable to the session since we are geting it from a param
			$this->setSession('contextaware', $contextaware);
        }
        //Do the replace
        $this->classCode = str_replace("{CONTEXTAWARE}", $contextaware, $this->classCode);
        return TRUE;
    }
    
    /**
    * 
    * Method to return the dependscontext and insert it into the code of the 
    * register.conf being built in place of the {DEPENDSCONTEXT} parsecode
    *  
    * @access Public
    * 
    */
    public function dependscontext()
    {
    	//Get the module code from parameter
        $dependscontext = $this->getParam('dependscontext', NULL);
        //If there is no parameter, check the session cookies
        if ($dependscontext == NULL) {
            $dependscontext = $this->getSession('dependscontext', '{DEPENDSCONTEXT_UNSPECIFIED}');
        } else {
            //Serialize the variable to the session since we are geting it from a param
			$this->setSession('dependscontext', $dependscontext);
        }
        //Do the replace
        $this->classCode = str_replace("{DEPENDSCONTEXT}", $dependscontext, $this->classCode);
        return TRUE;
    }
    
    /**
    *
    * Method for inserting language elements. TODO
    *
    */
    public function languageelems()
    {
        $str = "TEXT: mod_" . $this->getItem('modulecode') . "_about_title|Title of the module|" 
          . $this->getItem('modulename') . "\n";
        $str .= "TEXT: mod_" . $this->getItem('modulecode') . "_about|Description of the module|" 
          . $this->getItem('moduledescription') . "\n";
        $str .= "TEXT: mod_" . $this->getItem('modulecode') . "_about|Description of the module|" 
          . $this->getItem('moduledescription') . "\n";
          
        $this->classCode = str_replace("{LANGUAGEELEMS}", $str, $this->classCode);

    }
}
?>