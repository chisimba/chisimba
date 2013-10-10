<?php
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
/**
 *
 */
/**
 * @access     public
 */
class twitsocial extends controller {

    /**
     *
     * @var langauge an object reference.
     */
    public $objLanguage;

    public $objCurl;
    public $objTwitOps;
    protected $objDbTwit;
    public $objBg;

    /**
     * Method that initializes the objects
     *
     * @access private
     * @return nothing
     */
    public function init() {
        $this->objUser = $this->getObject ( 'user', 'security' );
        $this->objLanguage = $this->getObject ( 'language', 'language' );
        $this->objCurl = $this->getObject('curl', 'utilities');
        $this->objDbTwit = $this->getObject('dbtwits');
        $this->objTwitOps = $this->getObject('twitops');
        $this->objBg = $this->getObject('background', 'utilities');
    }

    /**
     * Method to handle the messages.
     *
     * @param  string  $action the message.
     * @access private
     * @return string  the template file name.
     */
    function dispatch($action) {
        switch ($action) {
            default :
                // let us show something lightweight. Don't want to burdon the db yet
                $this->nextAction('getloctags');

                break;
            case 'getsocial' :
                // set the thing going
                $url = "http://twitter.com/statuses/friends/paulscott56.xml";
                $parentid = $this->objUser->PKId($this->objUser->userId());
                $ret = $this->objTwitOps->curlTwitter($url, $parentid);
                // now we get all the unchecked users and check em for 2nd level followers
                $checks = $this->objDbTwit->getUnchecked();
                //check the connection status
                $status = $this->objBg->isUserConn();
                //keep the user connection alive even if the browser is closed
                $callback = $this->objBg->keepAlive();
                //This is where you call your long running process
                $this->objTwitOps->recursiveCheck($checks);
                //fork the process and create the child process and call the callback function when done
                $call2 = $this->objBg->setCallBack($this->objUser->email(), "Your social graph is ready!", "Your twitter 6 degrees has completed");

                $this->setVarByRef('msg', $this->objLanguage->languageText("mod_twitsocial_all_done", "twitsocial"));
                return 'alldone_tpl.php';
                break;

            case 'getloctags' :
                $this->requiresLogin();
                // get tags from all the descriptions
                $tagcloud = $this->objDbTwit->locTagCloud();
                $this->setVarByRef('tagcloud', $tagcloud);

                return 'tagcloud_tpl.php';

                break;

            case 'viewlocusers' :
                $this->requiresLogin();
                $loc = $this->getParam('tag');
                $users = $this->objDbTwit->getUsersByLoc($loc);
                $ret = $this->objTwitOps->layoutLocUsers($users);
                $this->setVarByRef('ret', $ret);

                return 'userview_tpl.php';
          }
    }

    public function requiresLogin() {
        return FALSE;
    }
} //end of class
?>