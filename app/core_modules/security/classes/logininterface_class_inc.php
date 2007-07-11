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

    	} catch (Exception $e) {
    		customException::cleanUp();
    	}
    }
    /**
    * Method to render a login box
    */
    public function renderLoginBox($module = NULL)
    {
    	try {
    		//set the action for the login form
    		if($module != NULL)
    		{
    			$formAction = $this->objEngine->uri(array('action' => 'login', 'mod' => $module), 'security');
    		}
    		else {
    			$formAction = $this->objEngine->uri(array('action' => 'login'), 'security');
    		}
    		//Load up the various HTML classes
    		$this->loadClass('button', 'htmlelements');
    		$this->loadClass('textinput', 'htmlelements');
    		$this->loadClass('checkbox', 'htmlelements');
    		$this->loadClass('link','htmlelements');
    		$this->loadClass('label','htmlelements');

    		// Create a Form object
    		$objForm = new form('loginform', $formAction);
    		//Set the displayType to 3 for freeform
    		$objForm->displayType=3;

    		//--Create an element for the username
    		$objInput = new textinput('username', '', '','15');
    		$objLabel = new label($this->objLanguage->languageText('word_username'), 'input_username');
    		//Add validation for username
    		$objForm->addRule('username',$this->objLanguage->languageText("mod_login_unrequired", 'login'),'required');
    		//Add the username box to the form
    		$objForm->addToForm($objLabel->show().': '.$objInput->show());

    		//--- Create an element for the password
    		$objInput = new textinput('password', '', 'password', '15');
    		$objLabel = new label($this->objLanguage->languageText('word_password'), 'input_password');
    		//Add the password box to the form
    		$objForm->addToForm('<br/>'.$objLabel->show() . ': ' . $objInput->show().'<br/>');

    		//--- Create an element for the network login radio
    		$objElement = new checkbox("useLdap");
    		$objElement->setCSS("transparentbgnb");
    		$objElement->label=$this->objLanguage->languageText("phrase_networkid").' ';
    		$ldap = '';
    		if ($this->objConfig->getuseLDAP()) {
    			$ldap .= $objElement->label.' '.$objElement->show();
    		}


    		//--- Create a submit button
    		$objButton = new button('submit',$this->objLanguage->languageText("word_login"));
    		// Set the button type to submit
    		$objButton->setToSubmit();
    		// Add the button to the form
    		$objForm->addToForm($ldap.'<br/>'.$objButton->show().'<br/>');

    		$helpText = strtoupper($this->objLanguage->languageText('word_help','system'));
        	$helpIcon = $this->objHelp->show('register', 'useradmin', $helpText);
        	$resetLink = new Link($this->uri(array('action'=>'needpassword'),'security'));
        	$resetLink->link = $this->objLanguage->languageText('mod_security_forgotpassword');
        	// the help link
        	$p = $resetLink->show().'<br />'.$helpIcon;
        	$objForm->addToForm($p);

    		return $objForm->show();
    	} catch (Exception $e) {
    		customException::cleanUp();
    	}
    }
}
?>