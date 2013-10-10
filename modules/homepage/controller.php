<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Homepage module
 * @author Jeremy O'Connor (ported to PHP5 by Alastair Pursch)
 */
class homepage extends controller {
    var $objUser;

    public function init() {
        $this->objUser =& $this->getObject('user', 'security');
        $this->objLanguage =& $this->getObject('language','language');

        /*
		  * the objUserAdmin will be
		  * used to initiate the search for user by surname
        * by getting the users details
        */
        // $this->objUserAdmin=&$this->getObject('useradmin_model','security');


        $this->objdBHomePages =& $this->getObject('dbhomepages');
        $this->objdBHomePagesLog =& $this->getObject('dbhomepageslog');

        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Set it to log once per session
        //$this->objLog->logOncePerSession = TRUE;
        //Log this module call
        $this->objLog->log();
    }

    public function dispatch($action=Null) {
        // Set the layout template.
        $this->setLayoutTemplate("layout_tpl.php");
        $this->setVarByRef('objUser', $this->objUser);

        switch($action) {

            case 'listusers':
                $how=$this->getParam('how');
                $match=stripslashes($this->getParam('searchField'));
                $this->setVar('match',$match);
                if($match == 'listall') {
                    $listHomePages = $this->objdBHomePages->getHomePagesWithHits();
                    $this->setVarByRef("listHomePages", $listHomePages);
                    return 'viewlist_tpl.php';
                }
                else {
                    $listHomePages = $this->objdBHomePages->getHomePagesWithHitsSingle($match);
                    $this->setVarByRef("listHomePages", $listHomePages);
                    return 'viewlist_tpl.php';
                }


            case "viewlist":
                $listHomePages = $this->objdBHomePages->getHomePagesWithHits();
                $this->setVarByRef("listHomePages", $listHomePages);
                return 'viewlist_tpl.php';
            case "viewhomepage":
                $userId = $this->getParam('userId', $this->objUser->userId());
                $this->setVarByRef("userId", $userId);
                $this->setVar('exists',$this->objdBHomePages->homepageExists($userId));
                $list = $this->objdBHomePages->listSingle($userId);

                // Get contents of homepage
                if (empty($list)) {
                    $contents = "";
                } else {
                    $contents = $list['contents'];
                    $contents = stripslashes($contents);
                    // Update log
                    $this->objdBHomePagesLog->insertSingle($list['id'], date("w"), $_SERVER['REMOTE_ADDR'], mktime());
                }
                //parse content
                $objWashOut=$this->getObject("washout","utilities");
                $contents=$objWashOut->parseText($contents);
                $this->setVarByRef('contents', $contents);
                return "viewhomepage_tpl.php";
            // Edit the homepage
            case "edithomepage":
                $userId = $this->getParam('userId', $this->objUser->userId());
                $this->setVarByRef('userId', $userId);
                $list = $this->objdBHomePages->listSingle($userId);
                // Get contents of homepage
                if (empty($list)) {
                    $contents = "";
                } else {
                    $contents = $list['contents'];
                    $contents = stripslashes($contents);
                }
                $this->setVarByRef('contents', $contents);
                return "edithomepage_tpl.php";
            case "edithomepageconfirm":
                $userId = $this->getParam('userId', null);

                // Insert or update contents of homepage
                $contents = $_POST["contents"];
                $contents = stripslashes($contents);
                $objWashOut=$this->getObject("washout","utilities");
                $contents=$objWashOut->parseText($contents);
                $contents = addslashes($contents);

                if ($this->objdBHomePages->homepageExists($userId)) {
                    $this->objdBHomePages->updateSingle($userId, $contents);
                } else {
                    $this->objdBHomePages->insertSingle($userId, $contents);
                }
                return $this->nextAction('viewhomepage', array('userId'=> $userId));
            case 'deletehomepage':
                $this->objdBHomePages->deleteSingle($this->objUser->userId());
                return $this->nextAction(null, null);
            default:
            // Get Homepage
                $this->setVar('exists', $this->objdBHomePages->homepageExists($this->objUser->userId()));

                // List of Other Home Pages
                $listHomePages = $this->objdBHomePages->getHomePagesWithHits('visitors DESC', 10);
                $this->setVarByRef("listHomePages", $listHomePages);

                return "main_tpl.php";
        } // switch
    }
}
?>