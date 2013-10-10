<?php
/**
* hivaids class extends controller
* @package hivaids
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Controller class for hivaids module
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class hivaids extends controller
{
    /**
    * Method to construct the class.
    */
    public function init()
    {
        try{
            $this->hivTools = $this->getObject('hivaidstools', 'hivaids');
            $this->hivTracking = $this->getObject('hivtracking', 'hivaids');
            $this->repository = $this->getObject('repository', 'hivaids');
            $this->dbVideos = $this->getObject('dbvideos', 'hivaids');
            $this->dbUsers = $this->getObject('dbusers', 'hivaids');
            $this->dbSuggestions = $this->getObject('dbsuggestions', 'hivaids');
            $this->dbLinks = $this->getObject('dblinks', 'hivaids');

            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objUser = $this->getObject('user', 'security');
            $this->objUserAdmin = $this->getObject('useradmin_model2','security');

            //Get the activity logger class and log this module call
            $action = $this->getParam('action');
            if(!($action == 'tracking' || $action == 'settracking')){
                $objLog = $this->getObject('logactivity', 'logger');
                $objLog->log();
            }
        }catch(Exception $e){
            throw customException($e->message());
            exit();
        }
    }

    /**
    * Standard dispatch function
    *
    * @access public
    * @param string $action The action to be performed
    * @return string Template to be displayed
    */
    public function dispatch($action)
    {
        switch($action){
            /* ** Video repository actions ** */
            case 'repository':
                $data = $this->dbVideos->getVideos();
                $display = $this->repository->show($data);
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            case 'preview':
                $fileId = $this->getParam('fileid');
                $display = $this->repository->preview($fileId);
                $this->setVar('suppressLayout', TRUE);
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            case 'addvideo':
                $id = $this->getParam('id');
                $fileId = $this->getParam('fileid');
                $description = '';
                if(!empty($id)){
                    $data = $this->dbVideos->getVideo($id);
                    $description = $data['description'];
                }
                $display = $this->repository->upload($id, $fileId, $description);
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            case 'savevideo':
                $save = $this->getParam('save');
                $id = $this->getParam('id');
                if(isset($save) && !empty($save)){
                    $this->dbVideos->addVideo($id);
                }
                return $this->nextAction('repository');

            case 'deletevideo':
                $id = $this->getParam('id');
                $this->dbVideos->deleteVideo($id);
                return $this->nextAction('repository');

            case 'videolist':
                $data = $this->dbVideos->getVideos();
                $display = $this->repository->listVideos($data);
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            /* ** User and Registration actions ** */
            case 'showregister':
                $display = $this->hivTools->showRegistration();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            case 'register':
                $id = $this->saveRegister();
                return $this->nextAction('confirm', array('newId' => $id), 'userregistration');

            /* ** Tracking and monitoring actions ** */
            case 'userstats':
                $display = $this->hivTracking->showUserStats();
                $this->setVarByRef('display', $display);
                $this->setVar('suppressLeft', TRUE);
                return 'home_tpl.php';

            case 'tracking':
                $mode = $this->getParam('mode');
                $display = $this->hivTracking->show($mode);
                $left = $this->hivTracking->showLeftMenu();
                $this->hivTools->setLeftCol($left);
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            case 'settracking':
                $mode = $this->getParam('mode');
                $nextMode = $this->getParam('nextmode');
                $this->hivTracking->show($mode);
                return $this->nextAction('tracking', array('mode' => $nextMode));

            case 'printtracking':
                $mode = $this->getParam('printmode');
                $display = $this->hivTracking->show($mode);
                $this->setVarByRef('display', $display);
                return 'print_tpl.php';

            case 'exportlog':
                $display = $this->hivTracking->show('export');
                $filename = 'hivaids_log.csv';
                $this->setVarByRef('display', $display);
                $this->setVarByRef('name', $filename);
                $this->setPageTemplate('export_page_tpl.php');
                return 'blank_tpl.php';

            /* ** Suggestion box ** */
            case 'viewsuggestions':
                $display = $this->hivTools->viewSuggestions();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            case 'showbox':
                $display = $this->hivTools->showSuggestionBox();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            case 'savesuggestion':
                $save = $this->getParam('save');
                if(isset($save) && !empty($save)){
                    $this->dbSuggestions->addSuggestion();
                }
                return $this->nextAction('home', '', 'cms');

            /* ** Links page ** */
            case 'managelinks':
                $display = $this->hivTools->manageLinks();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            case 'addlinks':
                $display = $this->hivTools->addLinks();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            case 'savelinks':
                $save = $this->getParam('save');
                if(isset($save) && !empty($save)){
                    $id = $this->getParam('id');
                    $this->dbLinks->addPage($id);
                }
                return $this->nextAction('managelinks');

            case 'viewlinks':
                $display = $this->hivTools->showLinks();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            /* ** General actions ** */
            case 'playyourmoves':
                $check = $this->inIpRange();
                if($check){
                    $skinroot = $this->objConfig->getskinRoot().'uwchivaids/';
                    $this->setVarByRef('skin', $skinroot);
                    return 'game_tpl.php';
                }
                $display = $this->notAllowed();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';

            case 'survey':
                return $this->nextAction('', '', 'survey');

            case 'podcast':
                return $this->nextAction('', '', 'podcast');

            case 'photogallery':
                return $this->nextAction('', '', 'photogallery');

            case 'manageclear':
                $left = $this->hivTracking->showLeftMenu();
                $this->hivTools->setLeftCol($left);
                return 'home_tpl.php';

            default:
                $display = $this->hivTools->showManagement();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';
        }
    }

    /**
    * Method to register the user on the site
    *
    * @access private
    */
    private function saveRegister()
    {
        $objLanguage = $this->getObject('language', 'language');
        $userId = $this->objUserAdmin->generateUserId();

        $username = $this->getParam('username');
        $password = $this->getParam('password');
        $password2 = $this->getParam('confirmpassword');
        $firstname = $this->getParam('username');
        $surname = '';//$this->getParam('username');
        $gender = $this->getParam('gender');
        $title = ($gender == 'M') ? $objLanguage->languageText('title_mr') : $objLanguage->languageText('title_miss');
        $country = $this->getParam('country');

        // Check that username is available
        if ($this->objUserAdmin->userNameAvailable($username) == FALSE) {
            return $this->nextAction('showregister');
        }

        $pkid = $this->objUserAdmin->addUser($userId, $username, $password, $title, $firstname, $surname, '', $gender, $country, '', '', 'useradmin', '1');
        $this->dbUsers->addUser($userId);

        return $pkid;
    }

    /**
    * Method to check if the users IP is within the allowed IP range
    *
    * @access private
    * @return bool
    */
    private function inIpRange()
    {
        // Proxy: 192.102.x
        // Intranet: 172.16.x
        $ipRange = '172.16.';

        $ip = $_SERVER['REMOTE_ADDR'];
        $pos = strpos($ip, $ipRange, 0);

        if($pos === FALSE){
            return FALSE;
        }
        return TRUE;
    }

    /**
    * Method to display a message if the user is outside the IP range
    *
    * @access private
    * @return string html
    */
    private function notAllowed()
    {
        $objLanguage = $this->getObject('language', 'language');
        $institution = $this->objConfig->getinstitutionName();
        $arr = array('institution' => $institution);
        $msg = $objLanguage->code2Txt('mod_hivaids_notallowedplaygame', 'hivaids', $arr);

        $str = '<p class="noRecordsMessage error">'.$msg.'</p>';
        return $str;
    }

    /**
    * Method to allow user to view the forum without being logged in
    *
    * @access public
    */
    public function requiresLogin($action)
    {
        switch($action){
            case 'showregister':
            case 'register':
            case 'playyourmoves':
            case 'videolist':
            case 'viewlinks':
            case 'podcast':
            case 'showbox':
            case 'savesuggestion':
                return FALSE;
        }
        return TRUE;
    }
} // end of controller class
?>