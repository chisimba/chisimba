<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}
// end security check
/**
 * The mentors controller manages the fullprofile module
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @copyright 2010, University of the Witwatersrand
 * @license GNU GPL
 * @package fullprofile
 */

class fullprofile extends controller {

    /**
    * @var object $objLanguage: The language class in the language module
    * @access public
    */
    public $objLangauge;

    /**
    * @var object $objUser: The user class in the security module
    * @access public
    */
    public $objUser;

    /**
    * @var string $userId: The user id of the current logged in user
    * @access public
    */
    public $userId;

    /**
    * @var object $objDisplay: The groups display object
    * @access public
    */
    public $objDisplay;

    /**
    * Method to initialise the controller
    *
    * @access public
    * @return void
    */
    public function init()
    {
        $this->objLanguage = $this->getObject( 'language', 'language' );
        $this->objUser = $this->newObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->objDisplay = $this->newObject('fpdisplay', 'fullprofile');
        $this->objFuncs = $this->newObject('fpfuncs', 'fullprofile');
        $this->objDbFullprofile = $this->newObject('dbfullprofile', 'fullprofile');
    }

    /**
    * Method the engine uses to kickstart the module
    *
    * @access public
    * @param string $action: The action to be performed
    * @return void
    */
    function dispatch( $action )
    {
        switch($action){

            default:
            case 'search':
                $searchTerm = $this->getParam('searchterm');
                if(!is_null($searchTerm) && $searchTerm != ""){
                    $result = $this->objDbFullprofile->searchUser($searchTerm);
                } else {
                    $result = NULL;
                }
                $formHtml = $this->objDisplay->showSearchForm($searchTerm, $result);

                $this->setVarByRef('templateContent', $formHtml);
                return 'display_tpl.php';
                break;

            case 'viewprofile':
                $this->setVar('JQUERY_VERSION', '1.4.2');
		$ccms_stylesheet = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/ccms-styles.css').'"/>';
		$this->appendArrayVar('headerParams', $ccms_stylesheet);
		$this->appendArrayVar('headerParams', $this->getJavascriptFile('js/tabinit.js', ''));
                $userId = $this->getParam('userid');
                if(is_null($userId) || $userId == ""){
                    $userId = $this->userId;
                }
                $content = $this->objDisplay->showFullProfile($userId);
                $this->setVarByRef('templateContent', $content);
                return 'display_tpl.php';
                break;

            case 'addfriend':
                $fuserid = $this->getParam('fuserid');
                $userid = $this->getParam('userid');

                $return = $this->objFuncs->addFriend($userid, $fuserid);

                $this->nextAction('viewprofile', array('userid'=>$fuserid), 'fullprofile');
                break;

            case 'removefriend':
                $id = $this->getParam('id');
                $userId = $this->getParam('userid');

                $return = $this->objFuncs->removeFriend($id);
                $this->nextAction('viewprofile', array('userid'=>$userId), 'fullprofile');
                break;
        }
    }
}

?>