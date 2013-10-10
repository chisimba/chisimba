<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}
// end security check
/**
 * The mentors controller manages the mentors medule
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @copyright 2010, University of the Witwatersrand
 * @license GNU GPL
 * @package mentors
 */

class mentors extends controller {

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
    * @var object $objDbUser: The user class in the buddie module
    * @access public
    */
    public $objDbUsers;

    /**
    * @var string $userId: The user id of the current logged in user
    * @access public
    */
    public $userId;

    /**
    * @var object $objDisplay: The mentors display object
    * @access public
    */
    public $objDisplay;

    /**
    * @var object $objWiki: The wiki db object
    * @access public
    */
    public $objWiki;

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
        $this->objDbUsers = $this->getObject('dbusers', 'buddies');
        $this->objDisplay = $this->newObject('mentorsdisplay', 'mentors');
        $this->objFuncs = $this->newObject('mentorfuncs', 'mentors');
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

            case 'makementor':
                $mentorId = $this->getParam('mentorId');
                $mentorName = $this->objUser->fullname($mentorId);

                $userId = $this->getParam('userId');
                $studentName = $this->objUser->fullname($userId);

                $result = $this->objFuncs->addMentor($mentorId, $userId);
                $how = $this->getParam('how');

                $searchField = $this->getParam('searchField');

                if($result == 'Already mentoring user'){
                    $notification = $result;
                } else {
                    $notification = $mentorName.' is now mentoring '.$studentName;
                }
                return $this->nextAction('admin', array(
                    'how' => $how,
                    'searchField' => $searchField,
                    'notification' => $notification,
                ), 'mentors');
                break;

            case 'deletementor':
                $mentorId = $this->getParam('mentorId');
                $mentorName = $this->objUser->fullname($mentorId);

                $userId = $this->getParam('userId');
                $studentName = $this->objUser->fullname($userId);

                $result = $this->objFuncs->delete($mentorId, $userId);
                $how = $this->getParam('how');

                $searchField = $this->getParam('searchField');

                if($result == 'No relationship'){
                    $notification = $result;
                } else {
                    $notification = $mentorName.' has been removed as a mentor of '.$studentName;
                }
                return $this->nextAction('admin', array(
                    'how' => $how,
                    'searchField' => $searchField,
                    'notification' => $notification,
                ), 'mentors');
                break;

            case 'admin':

                $how = $this->getParam('how');
		
                $searchField = $this->getParam('searchField');

                $notification = $this->getParam('notification');

                if(is_null($searchField)){
                    $searchField = 'A';
                    $how = 'firstname';
                }
                if ($searchField == "listall") {
                    $allUsers = $this->objDbUsers->listAll();
                } else {
                    $allUsers = $this->objDbUsers->listSelected($how, $searchField);
                }

                $content = $this->objDisplay->admin($allUsers, $notification, $searchField);
                $this->setVarByRef('templateContent', $content);
                return 'display_tpl.php';
                break;
            
            default:
            case 'viewposts':
                $content = $this->objDisplay->displayPosts($this->userId);
                $this->setVarByRef('templateContent', $content);
                return 'display_tpl.php';
                break;
        }
    }
}