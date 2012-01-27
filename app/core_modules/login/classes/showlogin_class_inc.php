<?php
/**
 *
 *
 * Render a login box
 *
 * Class to render login box, which can be rendered in a block, or via
 * an Ajax request to provide it in a modal window.
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
 * @package   login
 * @author    Multiple contributors
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * Render a login box
 *
 * Class to render login box, which can be rendered in a block, or via
 * an Ajax request to provide it in a modal window.
*
* @author Multiple contributors
* @package login
*
*/
class showlogin extends object
{


    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

    /**
     *
     * @var string Object $objUser String for the user object
     * @access public
     *
     */
    public $objUser;

    /**
     * Config object to check system config variables
     *
     * @var string Object $objUser String for the config object
     * @access public
     * 
     */
    public $objConfig;

    /**
    *
    * Intialiser for the login box class.
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        try {
            // Instantiate the helper classes.
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objConfig = $this->getObject('altconfig','config');
            $this->objHelp= $this->getObject('help','help');
            // Load the various HTML classes used to build the form elements.
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('link','htmlelements');
            $this->loadClass('label','htmlelements');
            $this->loadClass('fieldset','htmlelements');
            $this->loadClass ('hiddeninput', 'htmlelements');
            // Guess the module we are in
            $objGuess = $this->getObject('bestguess', 'utilities');
            $curMod = $objGuess->identifyModule();
            if ($curMod == 'prelogin' || $curMod == 'security') {
                $curMod = '_default';
            }
            // Load the various JS values for use by the script
            $this->getScriptValues($curMod);
            // Load the jQuery helper script
            $this->loadHelperScript();
        } catch (Exception $e) {
            customException::cleanUp();
        }

    }

    /**
     *
     * Show the login box or the captcha if login box is disabled
     * by failed logins
     *
     * @param string $module The module we are in or will go to
     * @param boolean $ajaxLogin Whether or not to do ajax login
     * @return string Either the login box or the captcha system
     * @access public
     * 
     */
    public function show($module = NULL, $ajaxLogin=FALSE)
    {
        // Create an instance of the nonce object for checking retries
        $this->objNonce = $this->getObject('nonce', 'login');
        if ($this->objNonce->checkEnabledBySession()) {
            return $this->renderLoginBox($module, $ajaxLogin);
        } else {
            return $this->renderProveHuman();
        }
    }

    /**
    *
    * Render a captcha and form that allows reasonable verification
    * of humans.
    *
    * @return string The rendered form
    * @access public
    *
    */
    public function renderProveHuman()
    {
        $formAction = 'javascript:void(0);';
        // Create a Form object.
        $objForm = new form('captchaform', $formAction);
        // Create a fieldset to render it all into.
        $objFields = new fieldset();
        $objFields->setLegend(' ');
        $objFields->addContent(
          $this->objLanguage->languageText("mod_login_3tries",
          'login'));
        // Add the captcha to the fieldset
        $img = '<br /><img src="index.php?module=login&action=generatecaptcha" />';
        $objFields->addContent($img . '<br />');
        // Get a text input for the captcha
        $objInput = new textinput('captcha', '', 'text','15');
        $objInput->setId('captcha');
        $objFields->addContent($objInput->show());
        //--- Create a submit button
        $objButton = new button(
          'submit', $this->objLanguage->languageText(
          "mod_login_verifyhuman", "verify", "Verify human"));
        $objButton->setToSubmit();
        $objButton->setId('captchaButton');
        $objFields->addContent($objButton->show());
        $objForm->addToForm($objFields->show());
        return '<div id="human_wrapper">' . $objForm->show() . '</div>';
    }

    /**
     *
     * Render a login box
     *
     * @param string $module The module we are in or will go to
     * @param boolean $ajaxLogin Whether or not to do ajax login
     * @return string The login box
     * @access public
     *
     */
    public function renderLoginBox($module = NULL, $ajaxLogin=FALSE)
    {
        try {
            // Set the formaction depending on whether it is going to use ajax or not.
            if (!$ajaxLogin) {
                // Set the action for the login form depending on if there is a module or not.
                if($module != NULL) {
                    $formAction = $this->uri(array('action' => 'login', 'mod' => $module), 'security');
                } else {
                    $formAction = $this->uri(array('action' => 'login'), 'login');
                }
            } else {
                // We want an ajax login.
                $formAction = 'javascript:void(0);';
            }
            // Create a Form object.
            $objForm = new form('loginform', $formAction);
            $objFields = new fieldset();
            $objFields->setLegend(' ');
            //--Create an element for the username
            $objInput = new textinput('username', '', 'text','15');
            $objInput->extra = 'maxlength="255"';
            $objInput->setCss('required minlength(2)');
            $objLabel = new label($this->objLanguage->languageText('word_username').': ', 'input_username');
            //Add the username box to the form
            $objFields->addContent($objLabel->show().'<br />');
            $objFields->addContent($objInput->show().'<br />');
            //--- Create an element for the password
            $objInput = new textinput('password', '', 'password', '15');
            $objInput->extra = 'maxlength="255"';
            $objInput->setCss('required');
            $objLabel = new label($this->objLanguage->languageText('word_password'). ': ', 'input_password');
            $objFields->addContent($objLabel->show().'<br />');
            $objFields->addContent($objInput->show());

            //--- Create an element for the network login radio
            $objElement = new checkbox("useLdap");
            $objElement->setCSS("transparentbgnb");
            $objElement->label=$this->objLanguage->languageText("phrase_networkid").' ';
            $ldap = '';
            $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $showLDAPCheckBox=$objSysConfig->getValue('show_ldap_checkbox', 'security');
            // Get a nonce
            $objNonce = $this->getObject('nonce', 'login');
            $nonce = $objNonce->storeNonce();
            // Create a hidden field for the nonce
            $objNonce = new hiddeninput ( 'nonce', $nonce );
            $objNonce->extra = ' id=\'nonce\'';
            $nonce = $objNonce->show();
            //----------------------------------------------------------------------------------------Checking this is a violation of the principle of chain of responsiblity @todo fix it
            if ($this->objConfig->getuseLDAP() && $showLDAPCheckBox == 'true') {
                $ldap .= $objElement->label.' '.$objElement->show();

            }
            //--- Create an element for the remember me checkbox
            $objRElement = new checkbox("remember");
            $objRElement->setCSS("transparentbgnb noborder");
            $objRElement->label=$this->objLanguage->languageText("phrase_rememberme", "security");
            $rem = $objRElement->label.' '.$objRElement->show() . "<br />";
            //--- Create a submit button
            $objButton = new button('submit',$this->objLanguage->languageText("word_login"));
            // Add the login icon
            $objButton->setIconClass("user");
            // Set the button type to submit
            $objButton->setToSubmit();
            // Give the button an ID for jQuery to grab.
            $objButton->setId('loginButton');
            // Add the button to the form ----------------------------------------------------------- Note LDAP breaks the COR pattern
            $objFields->addContent($ldap . '<br />' . $nonce . $rem 
              . "<div class='loginbuttonwrap'>".$objButton->show()
              .'</div>');
            $helpText = strtoupper($this->objLanguage->languageText('word_help','system'));
            $helpIcon = $this->objHelp->show('register', 'useradmin', $helpText);
            $resetLink = new Link($this->uri(array('action'=>'needpassword'),'security'));
            $resetLink->link = $this->objLanguage->languageText('mod_security_forgotpassword');
            // the help link
            $p = '<br/>'.$resetLink->show().'<br />'.$helpIcon;
            $objFields->addContent($p);
            $objForm->addToForm($objFields->show());
            return '<div id="login_block_wrapper">' 
              .  $objForm->show() . '</div>';
        } catch (Exception $e) {
            customException::cleanUp();
        }
    }
    
    /**
     *
     * Render a login box that can be used with jQuery to create
     * a dropdown login.
     * 
     * @param string $module The from which it is being accessed, if any
     * @param boolean $ajaxLogin Whether it should use ajax login or not
     * @return string  The rendered login box
     * @access public
     * 
     */
    public function renderLoginAsDropdown($module = NULL, $ajaxLogin=FALSE)
    {
        $objUser = $this->getObject('user', 'security');
        if($objUser->isLoggedIn()) {
            $lia = $this->objLanguage->languageText("mod_login_loggedinas","login", "Logged in as");
            $ret = $lia .": <em>" . $objUser->fullName() . "</em>";
        } else {
            $lWord = $this->objLanguage->languageText("word_login");
            $ret = "<a href=\"javascript:void(0);\" class=\"LOGIN_DROP\">$lWord</a>";
            $ret .="<div id='LOGIN_BLOCK' style='display: none'>";
            $ret .= $this->renderLoginBox($module, $ajaxLogin);
            $ret .= "</div>";
        }
        return $ret;
    }

    /**
     *
     * Load the javascript that assists the functionality and interface
     * elements of this module
     *
     * @access private
     * @return VOID
     * 
     */
    private function loadHelperScript()
    {
        $this->appendArrayVar('headerParams', 
          $this->getJavaScriptFile('loginsupport.js', 
          'login'));
    }

    /**
     *
     * Load the javascript script values that assists the functionality and
     * interface elements of this module
     *
     * @param string $curMod The current module
     * @access private
     * @return VOID
     *
     */
    private function getScriptValues($curMod='_default')
    {
        $loadingImage='<img src="skins/_common/icons/loading_bar.gif" alt=""Loading..." />';
        $ret = '<script type="text/javascript">
            // <![CDATA[
                loadingImage = \'' . $loadingImage . '\';
                theModule = \'' . $curMod . '\';
                failedMsg = \'' . $this->objLanguage->languageText('phrase_invalid_login', 'security', "Login failed") . '\';
            // ]]>
            '
        . '</script>
        ';
        return $this->appendArrayVar('headerParams', $ret);
    }
}
?>