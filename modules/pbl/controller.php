<?php
/**
* Pbl class extends controller
* @author Fernando Martinez
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package pbl
* @version 0.9
* @filesource
*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * Problem Based Learning Module.
 * The module opens a virtual classroom in which a group a students can interact through the
 * chat facility. The classrooms are created and students assigned to them using the pbladmin
 * module.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 0.9
 */
class pbl extends controller
{
    // var to hold javascript defaults to empty
    public $script;

    /**
    * Method to construct the class
    */
    public function init()
    {
        // get pbl classes
        $this->pbl = &$this->getObject('pblmain');
        $this->classroom = &$this->getObject('pblclassroom');
        $this->jscript = &$this->getObject('jscript');
        // get pbl database access classes
        $this->dbloggedin = &$this->getObject('dbloggedin');
        $this->dbclassroom = &$this->getObject('dbclassroom');
        $this->dbcontent = &$this->getObject('dbcontent');
        $this->dbchat = &$this->getObject('dbchat');
        $this->dbcases = &$this->getObject('dbcases');
        // get html elements
        $this->objBox = &$this->getObject('multitabbedbox', 'htmlelements');
        // get language object
        $this->objLanguage = &$this->getObject('language', 'language');
        // get user object
        $this->objUser = &$this->getObject('user', 'security');
        // get the context object
        $this->objContext = &$this->getObject('dbcontext', 'context');
        // create an instance of the usersdb object in groupadmin
        $this->objGroupUser = &$this->getObject('usersdb', 'groupadmin');
        $this->objDate = &$this->newObject('dateandtime','utilities');
        $this->objModules =& $this->newObject('modules','modulecatalogue');
    } // end of init

    /**
     * The standard dispatch method for the module.
     * The dispatch() method must
     * return the name of a page body template which will render the module
     * output (for more details see Modules and templating).
     */
    public function dispatch($action)
    {
        switch ($action) {
            case "showboard":
                // Display content of case on 'board' (case information)
                $template = "board_tpl.php";
                break;

            case "showtasks":
                // Display tasks associated with current scene of case
                $template = "tasks_tpl.php";
                break;

            case "showcontent":
                // Display learning issues and hypothesis.
                // Reset last update of li's and hypothesis time
                $time = date("ymdHi", time());
                $this->setSession('time', $time);
                $template = "content_tpl.php";
                break;

            case "editcontent":
                // Save or Erase LI's and Hypothesis
                $msgli=''; $msghyp='';
                $erase = $this->getParam('erase');
                $save = $this->getParam('save');
                $option = $this->getParam('option');
                
                if (isset($erase) && !empty($erase)) {
                    $this->dbcontent->eraseNotes(strtolower($option));
                    $msg = $this->objLanguage->languageText('word_erased');
                } else if(isset($save) && !empty($save)){
                    $content = $this->getParam('content');
                    
                    if (!empty($content)) {
                        $this->dbcontent->saveNotes($content, strtolower($option));
                        $msg = $this->objLanguage->languageText('word_saved');
                    }
                }
                if(strtolower($option) == 'li'){
                    $msgli = $msg;
                }else{
                    $msghyp = $msg;
                }

                $this->setVarByRef('msgli',$msgli);
                $this->setVarByRef('msghyp',$msghyp);
                $template = "content_tpl.php";
                break;

            case "editnotes":
                // Save or erase notes in notebook
                $msg='';
                $erase = $this->getParam('erase');
                $save = $this->getParam('save');
                $option = $this->getParam('option');
                
                if (isset($erase) && !empty($erase)) {
                    $this->dbloggedin->eraseNotes();
                    $msg = $this->objLanguage->languageText('mod_pbl_noteserased', 'pbl');
                } else if(isset($save) && !empty($save)){
                    $content = $this->getParam('content');
                    if (!empty($content)) {
                        $this->dbloggedin->saveNotes($content);
                        $msg = $this->objLanguage->languageText('mod_pbl_notessaved', 'pbl');
                    }
                }
                $this->nextAction('classroom',array('msg'=>$msg));
                break;

            case "evaluatemcq":
                // Evaluate multiple choice questions, return answer
                $template = "evalmcq_tpl.php";
                break;

            case 'showchat':
                $sesActive = $this->getSession('active');
                $sesClass = $this->getSession('classroom');
                $sesTime = $this->getSession('time');

                // check if the activescene has changed ? update 'board' iframe : ignore
                $scene = $this->dbclassroom->checkActive($sesActive, $sesClass);
                $script = "";
                if ($scene) {
                    $path = $this->uri(array('action' => 'showboard'));
                    // Replace the &amp; to & in the url
                    $path = preg_replace('/&amp;/', '&', $path);
                    $script .= "parent.board.location.href='".$path."'; ";
                }
                // check if li's / hypothesis have changed ? update 'content' iframe : ignore
                $row = $this->dbcontent->retrieveNotes('modified');
                if ($row) {
                    // if last modified more recent than last updated: update
                    if ($sesTime < $row[0]['modified']) {
                        $path = $this->uri(array('action' => 'showcontent'));
                        // Replace the &amp; to & in the url
                        $path = preg_replace('/&amp;/', '&', $path);
                        $script .= "parent.content.location.href='" .$path. "';";
                    }
                }
                $this->setVarByRef('script', $script);
                $template = 'chat_tpl.php';
                break;

            case 'chatline':
                $sesClass = $this->getSession('classroom');
                $sesUserId = $this->getSession('pbl_user_id');

                // check if chair or scribe has been changed
                $pos = $this->dbloggedin->getPosition($sesClass, $sesUserId);
                if($pos == 'c'){
                    $this->setSession('chair', TRUE);
                }else{
                    $this->setSession('chair', FALSE);
                }
                if($pos == 's'){
                    $this->setSession('scribe', TRUE);
                }else{
                    $this->setSession('scribe', FALSE);
                }

                $line = $this->getParam('chatline');
                // Send the input chat line to the chat display
                $this->dbchat->say($line);
                $this->nextAction('classroom');
                break;

            case 'restore':
                $sesClass = $this->getSession('classroom');
                $this->dbchat->moveChat($sesClass, '1', '2');
                $this->nextAction('classroom');
                break;

            // view pbl chat log in separate window
            case 'viewlog';
                $template='viewlog_tpl.php';
                break;

            case 'startpbl':
                // Initiate pbl classroom
                $this->pbl->initiate();
                // if classroom is open to all users ? get user and add to class list : set user as available
                $sesClass = $this->getSession('classroom');
                $sesUserId = $this->getSession('pbl_user_id');

                $class = $this->dbclassroom->getClass($sesClass);
                if ($class['status'] == 'o') {
                    $this->dbloggedin->addToClass($sesClass, $sesUserId, '1');
                } else{
                    $this->dbloggedin->setLoggedIn('1');
                }
                $time = date('ymdHi', time());
                $this->setSession('time', $time);
                $this->pbl->startCase();
                $this->nextAction('classroom');
                break;


            case 'classroom':
                $msg='';
                if($this->getParam('msg')){
                    $msg = $this->getParam('msg');
                }
                $this->setVarByRef('msg',$msg);
                $template = 'pbl_tpl.php';
                break;

            case 'exit':
                // if classroom is open remove user
                // else remove user from class list
                $sesClass = $this->getSession('classroom');
                $class = $this->dbclassroom->getClass($sesClass);
                if ($class['status'] == 'o') {
                    $this->dbloggedin->removeFromClass();
                } else{
                    $this->dbloggedin->setLoggedIn('0');
                }
                // wait till class is empty then delete old chat and set current chat to old
                $filter = "classroomid='" .$sesClass. "' and isavailable='1'";
                $userids = $this->dbloggedin->findUserIds($filter);
                if (empty($userids)) {
                    $this->dbchat->deleteChat($sesClass);
                    $this->dbchat->moveChat($sesClass);
                }
                // clear session variables associated with the classroom
                $this->unsetSession('choicestr');
                $this->unsetSession('ok');
                $this->unsetSession('nchoices');
                $this->unsetSession('active');
                $this->unsetSession('classroom');
                $this->unsetSession('caseid');
                $this->unsetSession('time');
                $this->unsetSession('classname');
                $this->unsetSession('casename');
                $this->unsetSession('chair');
                $this->unsetSession('scribe');
                $this->unsetSession('facilitator');
                $this->nextAction('');
                break;

            case 'showinfo':
                $template = 'pblinfo_tpl.php';
                break;

            default:
                // Log this call if registered
                if(!$this->objModules->checkIfRegistered('logger', 'logger')){
                    //Get the activity logger class
                    $this->objLog=$this->newObject('logactivity', 'logger');
                    //Log this module call
                    $this->objLog->log();
                }
                $template = 'index_tpl.php';
                break;
        }
        return $template;
    } // end of dispatch

    /**
    * Method to take a datetime string and reformat it as text.
    * @param string $date The date in datetime format.
    * @return string $ret The formatted date.
    *
    public function formatDate($date)
    {
        $ret = substr($date,8,2);
        $ret .= ' '.$this->objDate->monthFull(substr($date,5,2));
        $ret .= ' '.substr($date,0,4);

        $time = substr($date,11,5);
        if(!empty($time) && $time!=0)
            $ret .= ' '.$time;

        return $ret;
    }*/
} // end of pbl class

?>