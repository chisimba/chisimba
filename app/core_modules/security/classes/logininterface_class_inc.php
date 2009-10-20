<?php
 /**
 * Logininterface class
 *
 * Class to render login box, register links, and do other pre login duties
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
 * @author FSIU
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
*
* Class to render login box, register links, and do other pre login duties
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
            $this->objLanguage = $this->getObject('language', 'language');
            //initialise config obect
            $this->objConfig = $this->getObject('altconfig','config');
            $this->objHelp= $this->getObject('help','help');

        } catch (Exception $e) {
            customException::cleanUp();
        }
    }
    /**
    * Method to render a login box
    * @returns string
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
            $this->loadClass('fieldset','htmlelements');
            $objBox = $this->newObject('alertbox', 'htmlelements');

            // prepare the link for the oAuth providers 
            $box = $this->oauthDisp();
            $fb = $this->fbConnect();

            // Create a Form object
            $objForm = new form('loginform', $formAction);
            $objFields = new fieldset();
            $objFields->setLegend(' ');

            //--Create an element for the username
            $objInput = new textinput('username', '', 'text','15');
            $objInput->extra = "maxlength=255";
            $objLabel = new label($this->objLanguage->languageText('word_username').': ', 'input_username');
            //Add validation for username
            $objForm->addRule('username',$this->objLanguage->languageText("mod_login_unrequired", 'security', 'Please enter a username. A username is required in order to login.'),'required');

            //Add the username box to the form
            $objFields->addContent($objLabel->show().'<br />');
            $objFields->addContent($objInput->show().'<br />');
            //$objForm->addToForm();

            //--- Create an element for the password
            $objInput = new textinput('password', '', 'password', '15');
            $objInput->extra = "maxlength=255";
            $objLabel = new label($this->objLanguage->languageText('word_password'). ': ', 'input_password');
            //Add the password box to the form
            //$objForm->addToForm();
            $objFields->addContent($objLabel->show().'<br />');
            $objFields->addContent($objInput->show());
            //--- Create an element for the network login radio
            $objElement = new checkbox("useLdap");
            $objElement->setCSS("transparentbgnb");
            $objElement->label=$this->objLanguage->languageText("phrase_networkid").' ';
            $ldap = '';
            $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $showLDAPCheckBox=$objSysConfig->getValue('show_ldap_checkbox', 'security');
            
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
            // Set the button type to submit
            $objButton->setToSubmit();
            // Add the button to the form
            $objFields->addContent($ldap.'<br />'.$rem.'<br />'.$box.'<br />'.$fb.'<br />'.$objButton->show().'<br/>');
            //$objForm->addToForm();


            $helpText = strtoupper($this->objLanguage->languageText('word_help','system'));
            $helpIcon = $this->objHelp->show('register', 'useradmin', $helpText);
            $resetLink = new Link($this->uri(array('action'=>'needpassword'),'security'));
            $resetLink->link = $this->objLanguage->languageText('mod_security_forgotpassword');
            // the help link
            $p = '<br/>'.$resetLink->show().'<br />'.$helpIcon;
            $objFields->addContent($p);
            $objForm->addToForm($objFields->show());

            return $objForm->show();
        } catch (Exception $e) {
            customException::cleanUp();
        }
    }

    public function oauthDisp() {
        // displays a set of oAuth providers
        $this->objDbSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        $show = $this->objDbSysconfig->getValue('show_twitter_auth', 'security');
        if(strtolower($show) == 'true') {
            $objIcon = $this->getObject('geticon', 'htmlelements');
            $objIcon->alt = "Sign in with Twitter";
            $this->consumer_key = $this->objDbSysconfig->getValue('twitter_consumer_key', 'security');
            $this->consumer_secret = $this->objDbSysconfig->getValue('twitter_consumer_secret', 'security');
            // create a link to log in with twitter
            $this->objEpiWrapper = $this->getObject ( 'epiwrapper' );
            $twitterObj = new EpiTwitter($this->consumer_key, $this->consumer_secret);
            $twiticon = $objIcon->getLinkedIcon($twitterObj->getAuthenticateUrl(), 'Sign-in-with-Twitter-lighter', 'png');
            $twitter = $twiticon;
            return $twitter.'<br />';
        }
        else {
            return NULL;
        }
    }

    public function fbConnect() {
        $this->objMods = $this->getObject('modules', 'modulecatalogue');
        $this->objDbSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        $show = $this->objDbSysconfig->getValue('show_fbconnect_auth', 'security');
        if($this->objMods->checkIfRegistered('facebookapps') && strtolower($show) == 'true' ) {
             $apikey = $this->objDbSysconfig->getValue('apikey', 'facebookapps');
             $fb = "<script src=\"http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php\" type=\"text/javascript\"></script>
                    <fb:login-button size=\"large\" autologoutlink=\"true\" background=\"white\" length=\"short\" onlogin='window.location=\"index.php?module=security&action=fbconnect\";'></fb:login-button> 
                    <script type=\"text/javascript\"> FB.init(\"$apikey\", \"xd_receiver.htm\", {\"ifUserConnected\":\"index.php?module=security&action=fbconnect\"}); 
                    </script>";
             return $fb;
        }
        else {
            return NULL;
        }
    }
}
?>