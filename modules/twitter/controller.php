<?php
/**
 *
 * Twitter interface elements
 *
 * Twitter is a module that creates an integration between your Chisimba
 * site using your Twitter account.
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
 * @category  Chisimba
 * @package   helloforms
 * @author    Derek Keats dkeats@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 19774 2010-11-17 22:41:31Z charlvn $
 * @link      http://avoir.uwc.ac.za
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
* Controller class for Chisimba for the module twitter
*
* @author Derek Keats
* @package twitter
*
*/
class twitter extends controller
{

    /**
    *
    * @var string $objConfig String object property for holding the
    * configuration object
    * @access public;
    *
    */
    public $objConfig;

    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;
    /**
    *
    * @var string $objLog String object property for holding the
    * logger object for logging user activity
    * @access public
    *
    */
    public $objLog;

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $objSysConfig;

    /**
     * Instance of the altconfig class of the config module.
     *
     * @access private
     * @var    object
     */
    private $objAltConfig;

    /**
    *
    * Intialiser for the twitter controller
    * @access public
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        // Create an instance of the twitterremote class
        $this->objTwitterRemote = $this->getObject('twitterremote', 'twitter');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
        // Load Zend Framework
        $this->getObject('zend', 'zend');
        // Module configuration
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        // System configuration
        $this->objAltConfig = $this->getObject('altconfig', 'config');
    }


    /**
     *
     * The standard dispatch method for the twitter module.
     * The dispatch method uses methods determined from the action
     * parameter of the  querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'jqshow');
        // retrieve the mode (edit/add/translate) from the querystring
        $mode = $this->getParam("mode", null);
        // retrieve the sort order from the querystring
        $order = $this->getParam("order", null);
        // retrieve the type of timeline type
        $timeline = $this->getParam('timeline', 'public');
        $this->setVarByRef('timeline', $timeline);
        /*
        * Convert the action into a method (alternative to
        * using case selections)
        */
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting
        * from action
        */
        return $this->$method();
    }


    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    private function __testupdate()
    {
        $str="<h1>Version 2: update successful on Thursday Feb 14, 2008</h1>";
        $this->setVarByRef('str', $str);
        return "dump_tpl.php";
    }

    /**
    *
    * Method corresponding to the demo action. It fetches the default user
    * twitter status and displays it.
    *
    * @access private
    *
    */
    private function __demo()
    {
        $str="<h1>WORKING HERE</h1>";
        $objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
        $objUserParams->readConfig();
        $userName = $objUserParams->getValue("twittername");
        $password = $objUserParams->getValue("twitterpassword");
        if (!$userName == NULL && !$password == NULL) {
            $this->objTwitterRemote->initializeConnection($userName, $password);
            $status = "Testing storage of twitter un and pwd in userparams in Chisimba.";
            if ($this->objTwitterRemote->updateStatus($status)) {
                $str .= "Tweeted: " . $status;
            } else {
                $str .= "Tweety was muted.";
            }
        } else {
            $str .= $this->objLanguage->languageText("mod_twitter_unpwnull", "twitter");
        }
        $this->setVarByRef('str', $str);
        return "dump_tpl.php";
    }


    /**
    *
    * Method corresponding to the demo action. It fetches the default user
    * twitter status and displays it.
    *
    * @access private
    *
    */
    private function __jqshow()
    {
        $objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
        $objUserParams->readConfig();
        $userName = $objUserParams->getValue("twittername");
        $password = $objUserParams->getValue("twitterpassword");
        if (!$userName == NULL) {
            $jqTwit = $this->getObject("jqtwitter","twitter");
            $jqJuitter = $this->getObject("jqjuitter","twitter");
            $jqJuitter->loadJuitterPlugin();
            $jqJuitter->loadJuitterSystem();
            // Note that the CSS should always be loaded first
            $jqTwit->loadTweetCss();
            $jqJuitter->loadJuitterCss();
            $jqTwit->loadTweetPlugin();
            $jqTwit->initializeTweetPlugin($userName);
            $this->setVar('searchForm', $jqJuitter->loadJuitterSearchForm());
            $this->setVar('jqDiv', $jqJuitter->loadJuitterDiv());
        }
        return "jqtweet_tpl.php";
    }

    private function __exper()
    {
        $jqJuitter = $this->getObject("jqjuitter","twitter");
        $jqJuitter->loadJuitterCss();
        $jqJuitter->loadJuitterPlugin();
        $jqJuitter->loadJuitterSystem();
        return "experimental_tpl.php";
    }

    private function __tweet()
    {
        $objBox = $this->getObject("tweetbox", "twitter");
        $str = $objBox->show();
        return "tweet_tpl.php";
    }

    private function __sendtweet()
    {
        $objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
        $objUserParams->readConfig();
        $userName = $objUserParams->getValue("twittername");
        $password = $objUserParams->getValue("twitterpassword");
        $latitude = $objUserParams->getValue("latitude");
        $longitude = $objUserParams->getValue("longitude");
        if (!$userName == NULL && !$password == NULL) {
            $this->objTwitterRemote->initializeConnection($userName, $password);
            $status = $this->getParam("tweet", NULL);
            $this->objTwitterRemote->updateStatus($status, $latitude, $longitude);
        }
    }

    private function __demoget() {
        $objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
        $objUserParams->readConfig();
        $userName = $objUserParams->getValue("twittername");
        $password = $objUserParams->getValue("twitterpassword");
        $this->objTwitterRemote->initializeConnection($userName, $password);
        $str = $this->objTwitterRemote->showStatus();
        //htmlentities($this->objTwitterRemote->getStatus());
        $this->setVarByRef('str', $str);
        return "dump_tpl.php";
    }

    /**
     * Action starting the OAuth authentication process with Twitter.
     *
     * @access private
     */
    private function __authenticate()
    {
        $config = array();
        $config['callbackUrl'] = $this->uri(array('action'=>'token'), 'twitter', '', FALSE, TRUE, TRUE);
        $config['consumerKey'] = $this->objSysConfig->getValue('mod_twitter_consumer_key', 'twitter');
        $config['consumerSecret'] = $this->objSysConfig->getValue('mod_twitter_consumer_secret', 'twitter');
        $config['siteUrl'] = 'http://twitter.com/oauth';

        $consumer = new Zend_Oauth_Consumer($config);
        $token = $consumer->getRequestToken();
        $_SESSION['TWITTER_REQUEST_TOKEN'] = serialize($token);
        $consumer->redirect();
    }

    /**
     * Action completing the OAuth authentication process with Twitter.
     *
     * @access private
     */
    private function __token()
    {
        $config = array();
        $config['consumerKey'] = $this->objSysConfig->getValue('mod_twitter_consumer_key', 'twitter');
        $config['consumerSecret'] = $this->objSysConfig->getValue('mod_twitter_consumer_secret', 'twitter');
        $config['siteUrl'] = 'http://twitter.com/oauth';

        $consumer = new Zend_Oauth_Consumer($config);
        $token = $consumer->getAccessToken($_GET, unserialize($_SESSION['TWITTER_REQUEST_TOKEN']));
        $this->objSysConfig->changeParam('mod_twitter_token', 'twitter', serialize($token));
        unset($_SESSION['TWITTER_REQUEST_TOKEN']);

        header('Location: ' . $this->uri());
    }

    /**
     * Action updating the authenticated users's status on Twitter.
     *
     * @access private
     */
    private function __update()
    {
        $config = array();
        $config['consumerKey'] = $this->objSysConfig->getValue('mod_twitter_consumer_key', 'twitter');
        $config['consumerSecret'] = $this->objSysConfig->getValue('mod_twitter_consumer_secret', 'twitter');
        $config['siteUrl'] = 'http://twitter.com/oauth';

        $token = unserialize($this->objSysConfig->getValue('mod_twitter_token', 'twitter'));
        $client = $token->getHttpClient($config);
        $client->setUri('http://twitter.com/statuses/update.json');
        $client->setMethod(Zend_Http_Client::POST);
        $client->setParameterPost('status', file_get_contents('php://input'));
        $response = $client->request();
    }

    /**
    *
    * Method to return an error when the action is not a valid
    * action method
    *
    * @access private
    * @return string The dump template populated with the error message
    *
    */
    private function __actionError()
    {
        $this->setVar('str', "<h3>"
          . $this->objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $action . "</h3>");
        return 'dump_tpl.php';
    }

    /**
    *
    * Method to check if a given action is a valid method
    * of this class preceded by double underscore (__). If it __action
    * is not a valid method it returns FALSE, if it is a valid method
    * of this class it returns TRUE.
    *
    * @access private
    * @param string $action The action parameter passed byref
    * @return boolean TRUE|FALSE
    *
    */
    private function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Method to convert the action parameter into the name of
    * a method of this class.
    *
    * @access private
    * @param string $action The action parameter passed byref
    * @return stromg the name of the method
    *
    */
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }

    /*------------- END: Set of methods to replace case selection ------------*/



    /**
    *
    * This is a method to determine if the user has to
    * be logged in or not. Note that this is an example,
    * and if you use it view will be visible to non-logged in
    * users. Delete it if you do not want to allow annonymous access.
    * It overides that in the parent class
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin()
    {
        $action=$this->getParam('action','NULL');
        switch ($action)
        {
            case 'view': case 'update':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
     }
}
?>
