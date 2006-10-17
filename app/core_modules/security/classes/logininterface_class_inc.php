<?php

/**
* 
* Class to render login box, register links, and do other
* pre login duties
*
* @version $Id$
* @copyright 2003 
**/
class loginInterface extends object
{

    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;
    
    /**
     * Config object to check system config variables
     *
     * @var object
     */
    public $objConfig;

    public function init()
    {
    	try {
    		// Create an instance of the language object
    		$this->objLanguage = &$this->getObject('language', 'language');
    		//initialise config obect
    		$this->objConfig = &$this->getObject('altconfig','config');
    		$this->objHelp=& $this->getObject('help','help');
        	
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
    /**
    * Method to render a login box
    */
    public function renderLoginBox()
    {
    	try {
    		//set the action for the login form
    		$formAction = $this->objEngine->uri(array('action' => 'login'), 'security');
    		//Load up the various HTML classes
    		$this->loadClass('button', 'htmlelements');
    		$this->loadClass('textinput', 'htmlelements');
    		$this->loadClass('checkbox', 'htmlelements');
    		$this->loadClass('link','htmlelements');

    		// Create a Form object
    		$objForm = &new form('loginform');
    		//Set the action for the form to the uri with paramArray
    		$objForm->setAction($formAction);
    		//Set the displayType to 3 for freeform
    		$objForm->displayType=2;

    		//--Create an element for the username
    		$objElement = &new textinput("username",NULL,NULL,15);
    		$objElement->label = $this->objLanguage->languageText("word_username");
    		//Add validatoin for username
    		$objForm->addRule('username',$this->objLanguage->languageText("mod_login_unrequired"),'required');
    		//Add the username box to the form
    		$objForm->addToForm($objElement->label . ":<br />" . $objElement->show());

    		//--- Create an element for the password
    		$objElement = &new textinput("password",NULL,'password',15);
    		$objElement->label = $this->objLanguage->languageText("word_password");
    		//Add the password box to the form
    		$objForm->addToForm($objElement->label . ":<br />" . $objElement->show());

    		//--- Create an element for the network login radio
    		$objElement = &new checkbox("useLdap");
    		$objElement->setCSS("transparentbgnb");
    		$objElement->label=$this->objLanguage->languageText("phrase_networkid");
    		$ldap = '';
    		if ($this->objConfig->getuseLDAP()) {
    			$ldap .= $objElement->show(). " " . $objElement->label;
    		}
    		

    		//--- Create a submit button
    		$objElement = &new button('submit');
    		// Set the button type to submit
    		$objElement->setToSubmit();
    		// Use the language object to add the word save
    		$objElement->setValue(' '.$this->objLanguage->languageText("word_login").' ');
    		// Add the button to the form
    		$objForm->addToForm($ldap.$objElement->show());
    		
    		$helpText = $this->objLanguage->languageText('mod_useradmin_help','useradmin');
        	$helpIcon = $this->objHelp->show('register', 'useradmin', $helpText);
        	$resetLink = &new Link($this->uri(array('action'=>'needpassword'),'useradmin'));
        	$resetLink->link = $this->objLanguage->languageText('mod_security_forgotpassword');
        	// the help link
        	$p = $resetLink->show().'<br /><br />'.$helpIcon;
        	$objForm->addToForm($p);

    		return $objForm->show();
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}
?>