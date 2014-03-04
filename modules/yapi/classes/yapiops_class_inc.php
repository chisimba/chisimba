<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check
/**
*
* Class for the Yahoo API.
*
* @author Paul Scott <pscott@uwc.ac.za>
* @category chisimba
* @package yapi
* @copyright Paul Scott 2009
*
*/
class yapiops extends object
{
    public $objLanguage;
    public $objConfig;
    public $objSysConfig;

    private $ysession;
    private $consumerKey;
    private $consumerKeySecret;
    private $applicationId;

    public $yuser;
    public $profile;
    public $presence;
    public $connections;
    public $updates;
    public $connectionUpdates;

    public function init() {
        include ($this->getResourcePath ( 'lib/Yahoo.inc', 'yapi' ));
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        // Get the sysconfig variables for the Yahoo API user to set up the connection.
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );

        // All these need to come from dbsysconfig...
        // Make sure you obtain application keys before continuing by visiting:
        // http://developer.yahoo.com/dashboard/

        // Your consumer key goes here.
        $this->consumerKey = $this->objSysConfig->getValue('consumer_key', 'yapi');

        // Your consumer key secret goes here.
        $this->consumerKeySecret = $this->objSysConfig->getValue('consumer_secret', 'yapi');

        // Your application ID goes here.
        $this->applicationId = $this->objSysConfig->getValue('application_id', 'yapi');

        // Get a session first. If the viewer isn't sessioned yet, this call
        // will redirect them to log in and authorize your application to
        $this->ysession = YahooSession::requireSession($this->consumerKey, $this->consumerKeySecret,
        $this->applicationId);

        // Get the currently sessioned user. That means the user who is
        // currently viewing this page.
        $this->yuser = $this->ysession->getSessionedUser();

        // Load the profile for the current user.
        $this->profile = $this->yuser->loadProfile();

        // Fetch the presence for the current user.
        $this->presence = $this->yuser->getPresence();

        // Access the connection list for the current user.
        $start = 0; $count = 100; $total = 0;
        $this->connections = $this->yuser->getConnections($start, $count, $total);

        // Retrieve the updates for the current user.
        $this->updates = $this->yuser->listUpdates();

        // Retrieve the updates for the connections of the current user.
        $this->connectionUpdates = $this->yuser->listConnectionUpdates();
    }

    public function executeYQL($query) {
        $rsp = $this->ysession->query($query);
        $results = $rsp->query->results;

        return $results;
    }

    public function debugOutput($object) {
        return str_replace(array(" ", "\n"), array("&nbsp;", "<br>"), htmlentities(print_r($object, true), ENT_COMPAT, "UTF-8"));
    }

}
?>