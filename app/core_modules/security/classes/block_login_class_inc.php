<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* A block class to produce a login box in a block
*
* @author Derek Keats
* 
* $Id$
*
*/
class block_login extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
    * @var string object $objLanguage String to hold the language object
    */
    private $objLanguage;
    /**
    * @var string object $objUser String to hold the language object
    */
    private $objUser;
    public $blockType;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
    	try {
			$this->objLanguage =  $this->getObject('language', 'language');
			$this->objUser = $this->getObject('user', 'security');
			if($this->objUser->isLoggedIn() && $this->getParam('module', NULL)!=="cmsadmin") {
				$this->blockType="invisible";
			} else {
			    $this->title = $this->objLanguage->languageText("word_login");
			}
    	} catch (customException $e) {
    		customException::cleanUp();
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
				$objLogin =  $this->getObject('logininterface', 'security');
				return $objLogin->renderLoginBox();			    
			}
		} catch (customException $e) {
			customException::cleanUp();
		}
    }
}
?>