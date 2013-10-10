<?php
/* -------------------- Splashscreen CLASS ----------------*/

/**
* Class to handle the display of the splashscreen before login
*/
class splashscreenrender extends object
{

    var $objModule;
    var $objHelp;
    var $objSkin;
    var $objLanguage;
    var $objConfig;
    var $userNameBox;
    var $passwordBox;
    var $useLdapCheck;
    var $resetLink;
    var $registerLink;
    var $skin;
    var $startForm;
    var $loginButton;

    /**
    * Constructor method to define the table
    */
    function init()
    {
        try
        {
        	// Get an instance of the config object
        	$this->objConfig=& $this->getObject('altconfig','config');
        	//Get an instance of the language object
        	$this->objLanguage = &$this->getObject('language', 'language');
        	//Get an instance of the skin
        	$this->objSkin = &$this->getObject('skin', 'skin');
        	//Get an instance of the help object
        	$this->objHelp=& $this->getObject('helplink','help');
        	//Create an instance of the module object
        	$this->objModule=& $this->getObject('modules','modulecatalogue');
        	// Create an instance of the help object
        	$this->objHelp =& $this->getObject('help', 'help');
        	
        	$this->loadClass('form', 'htmlelements');
        	$this->loadClass('dropdown', 'htmlelements');
        	$this->loadClass('button', 'htmlelements');
        }
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }


    /**
    * Method to put the splashscreen on the login page
    * @author Sean Legassick and James Scoble
    * @param string $goplace Intercept any querystring parameter
    * for go place so that the user can be transferred to the
    * URL in goplace after login. This is used when coming in
    * from static content.
    */

    function putSplashScreen($goplace = '') {
        // Create help object
        $helpText = $this->objLanguage->languageText('mod_useradmin_help','useradmin');
        $helpIcon = $this->objHelp->show('register', 'useradmin', $helpText);


        //the variable to start the form
        $startForm='<form class="login" name="login_form"
          id="form1" method="post" action="'
          .$this->objEngine->uri(array('action' => 'login'), 'security');
        if ($goplace!="") {
            $startForm=$startForm.'&goplace='.$goplace;
        }
        $startForm=$startForm.'">';
        //the link to registration
        $registerModule='userregistration';
        $registerLink="<a href='".$this->uri(array('action'=>'register'),$registerModule)."'>".
        $this->objLanguage->languageText('word_register')."</a>\n";
        // the link for resetting passwords
        $resetLink="<a href='".$this->uri(array('action'=>'needpassword'),'security')."'>".
        $this->objLanguage->languageText('mod_security_forgotpassword')."</a>\n";

        //the variable to hold the username textbox
        $userNameBox=$this->objLanguage->languageText("word_username")
            .': <input name="username" type="text" id="username" class="text" size="15"/>';
        //the variable to hold the password textbox
        $passwordBox=$this->objLanguage->languageText("word_password")
            .': <input name="password" type="password" id="password" class="text" size="15" />';
        //the variable to hold the useLDAP checkbox
        $useLdapCheck='<input type="checkbox" name="useLdap" value="yes" class="transparentbgnb" />'
            .$this->objLanguage->languageText("phrase_networkid");
        //the variable to hold the login button

        $jsWarning = '<noscript><span class="error"><strong>'.$this->objLanguage->languageText('mod_security_javascriptwarning','security').'</strong></span><br /></noscript>';

        $loginButton= $jsWarning.'<input name="Submit" type="submit" class="button"
          onclick="KEWL_validateForm(\'username\',\'\',\'R\',\'password\',\'\',\'R\');'
          .'return document.KEWL_returnValue" value="'
          .$this->objLanguage->languageText("word_login").'" />';
        $login=$userNameBox.'&nbsp;&nbsp;&nbsp;'
            .$passwordBox.'&nbsp;&nbsp;&nbsp;'
        .$useLdapCheck.'&nbsp;&nbsp;&nbsp;'.$loginButton;
        //Open and parse the template file for the skin (splash_readfile_template.php)
        $splashFile=$this->objSkin->getSkinLocation().'splashscreen/splash_readfile_template.php';
        $ts=fopen($splashFile,"r") or die($this->objLanguage->languageText("error_splashscrmissing",'security')
            .": ".$splashFile.".");
        $ts_content=fread($ts, filesize($splashFile));

        $this->objSkin->validateSkinSession();
        $skin = '<input type="hidden" name="skinlocation" value="'.$this->objSkin->getSkin().'" />';

        $ts_content=str_replace("[-STARTFORM-]", $startForm, $ts_content);
        $ts_content=str_replace("[-HELP-]", $helpIcon, $ts_content);
        $ts_content=str_replace("[-SKIN-]", $skin, $ts_content);
        $ts_content=str_replace("[-USERNAMEBOX-]", $userNameBox, $ts_content);
        $ts_content=str_replace("[-PASSWORDBOX-]", $passwordBox, $ts_content);

	$ts_content=str_replace("[-SITENAME-]", $this->objConfig->getSiteName(), $ts_content);

        // Display the LDAP checkbox only if this site is using LDAP
        if ($this->objConfig->getuseLDAP()){
            $ts_content=str_replace("[-USELDAPCHECK-]", $useLdapCheck, $ts_content);
        } else {
            $ts_content=str_replace("[-USELDAPCHECK-]",NULL, $ts_content);
        }
        $ts_content=str_replace("[-LOGINBUTTON-]", $loginButton, $ts_content);
        $ts_content=str_replace("[-LOGIN-]", $login, $ts_content);

        $ts_content=str_replace("[-ENDFORM-]", $skin."</form>", $ts_content);

        // Course Chooser
        $ts_content=str_replace("[-CONTEXTCHOOSER-]", $this->getContextDropDown(), $ts_content);
		$ts_content=str_replace("[-CONTEXT-]", ucwords($this->objLanguage->code2Txt('mod_context_context','context')), $ts_content);

        //Resource Kit Link
        $ts_content=str_replace('[-RESOURCEKIT-]', $this->uri(array(),'resourcekit'), $ts_content);

        //Put the skin chooser if requested
        $skinChooser=$this->objSkin->putSkinChooser();
        $ts_content=str_replace("[-SKINCHOOSER-]", $skinChooser, $ts_content);
        $languageChooser=$this->objLanguage->putlanguageChooser();
    $ts_content=str_replace("[-LANGUAGECHOOSER-]",$languageChooser, $ts_content);
        // Put registration link only if allowselfregister is true
        if ($this->objConfig->getallowSelfRegister()) {
            $ts_content=str_replace("[-REGISTER-]", $registerLink, $ts_content);
        } else {
            $ts_content=str_replace("[-REGISTER-]", NULL, $ts_content);
        }

        // Put the link to reset the user's password
        $ts_content=str_replace("[-NEWPASSWORD-]", $resetLink, $ts_content);


        if($this->objModule->checkIfRegistered('stories','stories')){
            $this->objStories=& $this->getObject('sitestories', 'stories');
            $ts_content=str_replace('[-PRELOGINSTORIES-]', $this->objStories->fetchCategory('prelogin'), $ts_content);
            $ts_content=str_replace('[-PRELOGINSTORIESFOOTER-]', $this->objStories->fetchCategory('preloginfooter', NULL, FALSE), $ts_content);
        } else {
            $ts_content=str_replace('[-PRELOGINSTORIES-]', ' ', $ts_content);
            $ts_content=str_replace('[-PRELOGINSTORIESFOOTER-]', ' ', $ts_content);
        }

        return $ts_content;
    }

    /**
    *  Method to get the dropdown that contains all the public courses
    * @author Tohir Solomons
    */
    function getContextDropDown()
    {
        try {
        $objContext =& $this->getObject('dbcontext', 'context');
        $courses = $objContext->getListOfPublicContext();
        if (count($courses)==0) {
            $msg = $this->objLanguage->code2Txt('mod_context_nocontexts','context');
            return "<span class='noRecordsMessage'>$msg</span>";
            
        } else {
            $form = new form('joincontext', $this->uri(array('action'=>'joincontext'), 'context'));
            $dropdown = new dropdown ('contextCode');
            foreach ($courses AS $course)
            {
                $dropdown->addOption($course['contextcode'], $course['menutext']);
            }
            $dropdown->setSelected($objContext->getContextCode());
            $button = new button ('submitform', ucwords($this->objLanguage->code2Txt('mod_context_joincontext', 'context')));
            $button->setToSubmit();
            
            $form->addToForm($dropdown->show().'<br />'.$button->show());
            
            return $form->show();
        }

    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }


}  //end of class
?>