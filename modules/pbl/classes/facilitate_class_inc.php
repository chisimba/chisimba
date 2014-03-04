<?php
/**
* Class facilitate extends object.
* @author Fernando Martinez
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package pbl
* @version 1
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Class to emulate a virtual facilitator.
 * This class should provide functionality to interpret text requests from students
 * during a pbl session.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 0.9
 */

class facilitate extends object
{
    private $dbclassroom;

    /**
     * Constructor method to initialise objects
     */
    public function init()
    {
        $this->dbclassroom = &$this->getObject('dbclassroom');
        $this->dbusers = &$this->getObject('usersdb', 'groupadmin');
        $this->dbloggedin = &$this->getObject('dbloggedin');
        $this->objLanguange = &$this->getObject('language', 'language');
    }

    /**
     * Method to execute the users choice (selected from a set of possible choices).
     *
     * @param string $request Input from user
     * @return string $ans The answer to the input
     */
    public function doChoice($request)
    {
        $sesClass = $this->getSession('choicestr');
        $ans = "";
        if ($request[0] == '.')
            $request = substr($request, 1);
        $str = $sesClass;
        $choices = explode(";", $str);
        foreach($choices as $choice) {
            $choicepair = explode("->", $choice);
            if (strpos($choicepair[0], "[") === FALSE) {
                if ($request == $choicepair[0]) {
                    return $choicepair[1];
                } else
                    $ans = $choicepair[1];
            }
        }
        return $ans;
    }

    /**
     * Method to parse a students request.
     *
     * @param string $request Input request from the user.
     * @return string $facilitator The facilitators response.
     */
    public function parse($request)
    {
        $sesClass = $this->getSession('classroom');
        $sesChair = $this->getSession('chair');
        $sesScribe = $this->getSession('scribe');
        $sesFacilitator = $this->getSession('facilitator');
        $sesPblUserId = $this->getSession('pbl_user_id');
        $sesChoice = $this->getSession('choicestr');

        $facilitator = $this->objLanguange->languageText('word_facilitator') . ': ';
        $lbHelp = $this->objLanguange->languageText('mod_pbl_help', 'pbl');
        $lbMore = $this->objLanguange->languageText('mod_pbl_moreinfo', 'pbl');
        $lbChairOnly = $this->objLanguange->languageText('mod_pbl_chaironly', 'pbl');
        $lbNoScribe = $this->objLanguange->languageText('mod_pbl_noScribe', 'pbl');
        $lbNoUser = $this->objLanguange->languageText('mod_pbl_noUser', 'pbl');
        $lbNotInClass = $this->objLanguange->languageText('mod_pbl_noUserInClass', 'pbl');
        $lbNotLog = $this->objLanguange->languageText('mod_pbl_notLoggedIn', 'pbl');
        $lbScribeIs = $this->objLanguange->languageText('mod_pbl_scribe', 'pbl');
        $lbNoChair = $this->objLanguange->languageText('mod_pbl_noChair', 'pbl');
        $lbChairIs = $this->objLanguange->languageText('mod_pbl_chair', 'pbl');
        $lbCant = $this->objLanguange->languageText("mod_pbl_can't", 'pbl');
        $lbAnswerIs = $this->objLanguange->languageText('mod_pbl_ans', 'pbl');
        $lbSorry = $this->objLanguange->languageText('mod_pbl_sorry', 'pbl');
        
        // Show help
        if (!strpos($request, "help") === FALSE) {
            return $facilitator . $lbHelp;
        }
        // Move case to next scene
        if (!strpos($request, "more") === FALSE && !strpos($request, "info") === FALSE) {
            // Options only available if user is the chair
            if ($sesChair || $sesFacilitator) {
                $aid = $this->dbclassroom->GetNextSceneId($this->dbclassroom->GetActiveSceneId());
                $this->dbclassroom->SetActiveSceneId($aid);
                return $facilitator . $lbMore;
            } else {
                return $facilitator . $lbChairOnly;
            }
        }
        // Assign Scribe
        if (!strpos($request, "scribe") === FALSE) {
            // Options only available if user is the chair
            if ($sesChair || $sesFacilitator) {
                $len = strlen($request);
                if ($len <= 8){
                    return $facilitator . $lbNoScribe;
                }
                if ($request[7] == ' ') {
                    // Check if user is loggedin to the class, if not logged in - return message, if not exist - ask for username
                    $name = substr($request, 8, $len-7);
                    // Get user id
                    $userid = $this->dbusers->getUsers(NULL, " where username='$name' ");
                    if (!$userid){
                        return $facilitator . $lbNoUser;
                    }
                    // Check if logged in
                    $isAvail = $this->dbloggedin->isLoggedIn($userid[0]['id'], $sesClass);
                    if ($isAvail === FALSE){
                        return $facilitator . $lbNotInClass;
                    }
                    if (!$isAvail){
                        return $facilitator . $lbNotLog;
                    }
                    if($sesScribe && !$sesFacilitator){
                        $this->dbloggedin->setPosition($sesClass, $sesPblUserId, 'n');
                    }
                    $this->setSession('scribe', FALSE);
                    if(!($this->dbloggedin->getPosition($sesClass, $userid[0]['id']) == 'f')){
                        $this->dbloggedin->setPosition($sesClass, $userid[0]['id'], 's');
                    }
                    if($sesPblUserId == $userid[0]['id']){
                        $this->setSession('scribe', TRUE);
                    }

                    return $facilitator . $lbScribeIs . ': ' . $name;
                }
            } else {
                return $facilitator . $lbChairOnly;
            }
        }
        
        // Assign chair
        if (!strpos($request, "chair") === FALSE) {
            $len = strlen($request);
            if ($len <= 7){
                return $facilitator . $lbNoChair;
            }
            if ($request[6] == ' ') {
                // Check if user is loggedin to the class, if not logged in - return message, if not exist - ask for username
                $name = substr($request, 7, $len-6);
                // Get user id
                $userid = $this->dbusers->getUsers(NULL, " where username='$name' ");
                if (!$userid){
                    return $facilitator . $lbNoUser;
                }
                // Check if logged in
                $isAvail = $this->dbloggedin->isLoggedIn($userid[0]['id'], $sesClass);
                if ($isAvail === FALSE){
                    return $facilitator . $lbNotInClass;
                }
                if (!$isAvail){
                    return $facilitator . $lbNotLog;
                }
                if($sesChair && !$sesFacilitator){
                    $this->dbloggedin->setPosition($sesClass, $sesPblUserId, 'n');
                }
                $this->setSession('chair', FALSE);
                if(!($this->dbloggedin->getPosition($sesClass, $userid[0]['id']) == 'f')){
                    $this->dbloggedin->setPosition($sesClass, $userid[0]['id'], 'c');
                }
                if($sesPblUserId == $userid[0]['id']){
                    $this->setSession('chair', TRUE);
                }

                return $facilitator . $lbChairIs . ': ' . $name;
            }
        }
        // Return correction of answer
        if (isset($sesChoice)) {
            if (!strpos($request, "more") === FALSE && !strpos($request, "info") === FALSE) {
                return $facilitator . $lbCant;
            }
            $ans = $this->DoChoice($request);
            return $facilitator . $lbAnswerIs . " $ans";
        }
        return $facilitator . $lbSorry;
    }
}

?>