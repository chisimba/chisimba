<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* A block class to produce a registration block
*
* @author Nic Appleby
* 
* $Id$
*
*/
class block_register extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
    	try {
    		$this->objLanguage = & $this->getObject('language', 'language');
			$this->objUser = $this->getObject('user', 'security');
			$this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
			if($this->objUser->isLoggedIn() && $this->getParam('module', NULL)!=="cmsadmin") {
				$this->blockType="invisible";
			} else { 
    			$this->title = $this->objLanguage->languageText("word_registration");
			}
    	} catch (Exception $e) {
    	    throw customException($e->getMessage());
    	    exit();
    	}
    }
    
    /**
    * Standard block show method. It uses the renderform
    * class to render the login box
    */
    public function show()
    {
    	try {
    		if($this->objUser->isLoggedIn() && $this->getParam('module', NULL)!=="cmsadmin") {
    			return NULL;
    		} else {
    		    $regModule = $this->objSysConfig->getValue('REGISTRATION_MODULE', 'security');
    		    if(empty($regModule)){
    		        $regModule = 'userregistration';
    		    }
	    		$regLink = &$this->newObject('link','htmlelements');
	    		$regLink->link = $this->objLanguage->languageText('word_register');
	    		$regLink->link($this->uri(array('action' => 'showregister'), $regModule));
	    		return $regLink->show();
    		}
    	} catch (Exception $e) {
    		throw customException($e->getMessage());
    		exit();
    	}
    }
}
?>