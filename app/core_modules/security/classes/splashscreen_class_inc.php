<?php
 /**
 * Splashscreen class
 * 
 * Class to handle the display of the splashscreen before login.
 * 
 * PHP version 5
 *  
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * 
 * @category  Chisimba
 * @package   security
 * @author James Scoble <jscoble@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Class to handle the display of the splashscreen before login
*/
class splashscreen extends object
{

    var $objModule;
    var $objHelp;
    var $objSkin;
    var $objLanguage;
    var $objConfig;

    /**
    * Constructor method to define the table
    */
    function init()
    {
        // Get an instance of the config object
        $this->objConfig= $this->getObject('config','config');
        //Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        //Get an instance of the skin
        $this->objSkin = $this->getObject('skin', 'skin');
        //Get an instance of the help object
        $this->objHelp= $this->getObject('helplink','help');
        //Create an instance of the module object
        $this->objModule= $this->getObject('modules','modulecatalogue');
        $this->server = $this->objConfig->serverName();
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

        //the variable to start the form
        $startForm='<form class="login" name="login_form"
          id="form1" method="post" action="'
          .$this->objEngine->uri(array('action' => 'login'), 'security');
        if ($goplace!="") {
            $startForm=$startForm.'&goplace='.$goplace;
        }
        $startForm=$startForm."\">";
        //the link to registration
        $registerLink="<a href='".$this->uri(array('action'=>'register'),'useradmin')."'>".
        $this->objLanguage->languageText('word_register')."</a>\n";
        // the link for resetting passwords
        $resetLink="<a href='".$this->uri(array('action'=>'needpassword'),'useradmin')."'>".
        $this->objLanguage->languageText('mod_security_forgotpassword')."</a>\n";
        //the variable to hold the username textbox
        $userNameBox=$this->objLanguage->languageText("word_username")
            .':<br/><input name="username" type="text" id="username" class="text" />';
        //the variable to hold the password textbox
        $passwordBox=$this->objLanguage->languageText("word_password")
            .':<br/><input name="password" type="password" id="password" class="text" />';
        //the variable to hold the useLDAP checkbox
        $useLdapCheck='<input type="checkbox" name="useLdap" value="yes" class="transparentbgnb">'
            .$this->objLanguage->languageText("phrase_networkid");
        //the variable to hold the login button

        $jsWarning = '<noscript><span class="error"><strong>'.$this->objLanguage->languageText('mod_security_javascriptwarning','security').'</strong></span><br /></noscript>';

        $loginButton= $jsWarning.'<input name="Submit" type="submit" class="button"
          onclick="KEWL_validateForm(\'username\',\'\',\'R\',\'password\',\'\',\'R\');'
          .'return document.KEWL_returnValue" value="'
          .$this->objLanguage->languageText("word_login").'"/>';
        $login=$userNameBox.'&nbsp;&nbsp;&nbsp;'
            .$passwordBox.'&nbsp;&nbsp;&nbsp;'
        .$useLdapCheck.'&nbsp;&nbsp;&nbsp;'.$loginButton;
        //Open and parse the template file for the skin (splash_readfile_template.php)
        $splashFile=$this->objSkin->getSkinLocation().'splashscreen/splash_readfile_template.php';
        $ts=fopen($splashFile,"r") or die($this->objLanguage->languageText("error_splashscrmissing")
            .": ".$splashFile.".");
        $ts_content=fread($ts, filesize($splashFile));

        $this->objSkin->validateSkinSession();
        $skin = '<input type="hidden" name="skinlocation" value="'.$this->objSkin->getSkin().'" />';

        $ts_content=str_replace("[-STARTFORM-]", $startForm, $ts_content);
        $ts_content=str_replace("[-SKIN-]", $skin, $ts_content);
        $ts_content=str_replace("[-USERNAMEBOX-]", $userNameBox, $ts_content);
        $ts_content=str_replace("[-PASSWORDBOX-]", $passwordBox, $ts_content);

        // Display the LDAP checkbox only if this site is using LDAP
        if ($this->objConfig->useLDAP()){
            $ts_content=str_replace("[-USELDAPCHECK-]", $useLdapCheck, $ts_content);
        } else {
            $ts_content=str_replace("[-USELDAPCHECK-]",NULL, $ts_content);
        }
        $ts_content=str_replace("[-LOGINBUTTON-]", $loginButton, $ts_content);
        $ts_content=str_replace("[-LOGIN-]", $login, $ts_content);

        $ts_content=str_replace("[-ENDFORM-]", $skin."</form>", $ts_content);

        // Course Chooser
        $ts_content=str_replace("[-CONTEXTCHOOSER-]", $this->getContextDropDown(), $ts_content);

        //Resource Kit Link
        $ts_content=str_replace('[-RESOURCEKIT-]', $this->uri(array(),'resourcekit'), $ts_content);

        //Put the skin chooser if requested
        $skinChooser=$this->objSkin->putSkinChooser();
        $ts_content=str_replace("[-SKINCHOOSER-]", $skinChooser, $ts_content);
        $languageChooser=$this->objLanguage->putlanguageChooser();
	$ts_content=str_replace("[-LANGUAGECHOOSER-]",$languageChooser, $ts_content);
        // Put registration link only if allowselfregister is true
        if ($this->objConfig->allowSelfRegister()) {
            $ts_content=str_replace("[-REGISTER-]", $registerLink, $ts_content);
        } else {
            $ts_content=str_replace("[-REGISTER-]", NULL, $ts_content);
        }

        // Put the link to reset the user's password
        $ts_content=str_replace("[-NEWPASSWORD-]", $resetLink, $ts_content);

        //Create an instance of the module object
	    $this->objModule=& $this->getObject('modules','modulecatalogue');
        if($this->objModule->checkIfRegistered('stories','stories')){
            $this->objStories= $this->getObject('sitestories', 'stories');
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
	* @author Wesley Nitsckie
	*/
	function getContextDropDown(){
		$objModule =  $this->newObject('modules','modulecatalogue');
		$objDBContext =  $this->newObject('dbcontext','context');
		$dropdown =  $this->newObject('dropdown','htmlelements');
		$str = '';

		$frmContext= $this->newObject('form','htmlelements');
		$frmContext->name='joincontext';
		$frmContext->setAction($this->uri(array('action'=>'joincontext'),'context'));
		$frmContext->setDisplayType(3);

		$objLeaveButton=$this->getObject('geticon','htmlelements');
		$objLeaveButton->setIcon('close');
		$objLeaveButton->alt=$this->objLanguage->languageText("word_leave").' '.$this->objLanguage->languageText("word_course");
		$objLeaveButton->title=$this->objLanguage->languageText("word_leave").' '.$this->objLanguage->languageText("word_course");

		$objLeaveLink=$this->getObject('link','htmlelements');
		$objLeaveLink->href=$this->uri(array('action'=>'leavecontext'));
		$objLeaveLink->link=$objLeaveButton->show();

		if ($objModule->checkIfRegistered('', 'context')){
		// Get Context Code & Title
			$contextObject = $this->getObject('dbcontext', 'context');
			$contextCode = $contextObject->getContextCode();

			$this->loadClass('link', 'htmlelements');
			$contextLink = new link($this->uri(null, 'context'));
			$contextLink->link = $contextObject->getTitle();

			// Set Context Code to 'root' if not in context
			if ($contextCode == ''){
				$contextTitle = $this->objLanguage->languageText('mod_context_lobby');
			} else {
				$contextTitle = $contextLink->show().' '.$objLeaveLink->show();
			}


		$contextTitle =  str_replace('{context}', '<strong>'.$contextTitle.'</strong>', $this->objLanguage->languageText('mod_postlogin_currentlyincontext'));

		$str .= '<p>'.$contextTitle . '</p>';
		}

		$dropdown->name='contextCode';
		$dropdown->cssClass='coursechooser';
		$dropdown->addFromDB($objDBContext->getAll(),'menutext','contextCode',$objDBContext->getContextCode());

		$button=new button();
		$button->setToSubmit();
		$button->setValue($this->objLanguage->languageText('word_go')); //mod_context_entercourse

        $frmContext->addToForm($this->objLanguage->languageText('phrase_selectcourse').':<br/>');
		$frmContext->addToForm($dropdown->show());
		$frmContext->addToForm($button->show());

        if (count($objDBContext->getAll()) == 0) {
            return NULL;
        } else {
            return $frmContext->show();
        }
	}


}  #end of class
?>
