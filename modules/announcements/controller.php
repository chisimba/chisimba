<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller class for blog module
 *
 * @category  Chisimba
 * @package   announcements
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 25097 2013-05-31 06:45:14Z joconnor $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class announcements extends controller
{

    /**
     * @var array $userContext List of Contexts a user belongs to except the current one
     * @access private
     */
    private $userContexts;

    /**
     * @var array $lecturerContext List of Contexts a user is a lecturer in
     * @access private
     */
    private $lecturerContext;

    /**
    * Constructor for the Module
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objContext = $this->getObject('dbcontext','context');
        $this->objDate = $this->getObject('dateandtime', 'utilities');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        //feed creator subsystem
        $this->objFeedCreator = $this->getObject('feeder', 'feed');
        $this->objAnnouncements = $this->getObject('dbannouncements');
        $this->userId = $this->objUser->userId();
        $objUserContext = $this->getObject('usercontext', 'context');
        if(!empty($this->userId)){
            $userContext = $objUserContext->getUserContext($this->userId);
            $cc = $this->objContext->getContextCode();
            foreach ($userContext as $context) {
                if ($context !== $cc) {
                    $this->userContexts[] = $context;
                }
            }
            $this->lecturerContext = $objUserContext->getContextWhereLecturer($this->userId);
        }
        $this->isAdmin = $this->objUser->isAdmin();
        $this->itemsPerPage = 10;
        $this->setVar('lecturerContext', $this->lecturerContext);
        $this->setVar('isAdmin', $this->isAdmin);
		//Load Module Catalogue Class
		$this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');
		$this->objContextGroups = $this->getObject('managegroups', 'contextgroups');
		if($this->objModuleCatalogue->checkIfRegistered('activitystreamer'))
		{
			$this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
			$this->eventDispatcher->addObserver ( array ($this->objActivityStreamer, 'postmade' ) );
			$this->eventsEnabled = TRUE;
		} else {
			$this->eventsEnabled = FALSE;
		}
        // [[ JOC
        //$this->objMailer = $this->getObject('email', 'mail');
        // ]] JOC
    }

    /**
    * Standard Dispatch Function for Controller
    *
    * @access public
    * @param string $action Action being run
    * @return string Filename of template to be displayed
    */
    public function dispatch($action)
    {
        /*
        * Convert the action into a method (alternative to
        * using case selections)
        */
        $method = $this->getMethod($action);
        $this->setLayoutTemplate('announcements_layout_tpl.php');
        // Return the template determined by the method resulting from action.
        return $this->$method();
    }

    /**
    *
    * Method to convert the action parameter into the name of
    * a method of this class.
    *
    * @access private
    * @param string $action The action parameter passed byref
    * @return string the name of the method
    *
    */
    function getMethod(& $action)
    {
        if ($this->validAction($action)) {
            return '__'.$action;
        } else {
            return '__home';
        }
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
    function validAction(& $action)
    {
        if (method_exists($this, '__'.$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function requiresLogin()
    {
        switch ($this->getParam('action', NULL)) {
            case 'edit':
            case 'add':
            case 'save':
            case 'delete':
                return TRUE;
                break;
            default:
                return False;
                break;
        }
    }
    /**
     *Method to generate a rss feed
     *@param null
     *@return an xml string
     *@access public
     */
    public function __feed()
    {
        //get ther username
        $username = $this->getParam("username");
        //$username = "admin";
        //get all the announcements for this user which
        //will include all announcements for all the courses that he
        //registered in
        $objManageGroups = $this->getObject("managegroups", "contextgroups");
        $userId = $this->objUser->getUserId($username);
        //print $userId;
        $posts = $this->objAnnouncements->getAllAnnouncements($objManageGroups->usercontextcodes($userId));
       // var_dump($posts);
        //create the feed with the post
        //title of the feed - Site Name Announcements
        $feedtitle = htmlentities("Announcements");
        //description
        $feedDescription = "Some description";

        //link back to the blog
        $feedLink = $this->objConfig->getSiteRoot() . "index.php?module=announcements&action=home&userid=" . $userid;
        //sanitize the link
        $feedLink = htmlentities($feedLink);
        //set up the url
        $feedURL = $this->objConfig->getSiteRoot() . "index.php?module=announcements&userid=" . $userid . "action=feed&format=" . $format;
        //print_r($feedURL);
        $feedURL = htmlentities($feedURL);
        //setup image
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->setModuleIcon('announcements');
        $iconURL = $this->objConfig->getSiteRoot().$objIcon->iconfolder.$objIcon->name. "." . $objIcon->type;
        $this->objFeedCreator->setrssImage($feedtitle, $iconURL, $feedLink, $feedDescription);
        //set up the feed
        $this->objFeedCreator->setupFeed(TRUE, $feedtitle, $feedDescription, $feedLink, $feedURL);
        //
        foreach($posts as $feeditems) {
            //use the post title as the feed item title
            $itemTitle = $feeditems['title'];
            $itemLink = $this->uri(array(
                'action' => 'view',
                'id' => $feeditems['id']
            )); //todo - add this to the posts table!
            //description
            $itemDescription = $feeditems['message'];
            //where are we getting this from
            $itemSource = $this->objConfig->getSiteRoot() . "index.php?module=announcements&userid=" . $userid;
            //feed author
            $itemAuthor = $this->objUser->userName($feeditems['createdby'])."<".$this->objUser->email($feeditems['createdby']).">";
            //item date
            $DT = split(" ",$feeditems['createdon']);
            $date = split("-", $DT[0]);
            $time = split(":", $DT[1]);
            //var_dump($date); die;
            $itemDate = mkTime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
            //add this item to the feed
            $itemLink = $this->objConfig->getSiteRoot() . "index.php?module=announcements&action=view&id=" . $feeditems['id'];;
            $this->objFeedCreator->addItem($itemTitle, $itemLink, $itemDescription, $itemSource, $itemAuthor, $itemDate);
        }
        $feed = $this->objFeedCreator->output();
       // echo htmlentities($feed);
    }

    public function cData($data)
    {
        return  "<![CDATA[".$data."]]>";
    }
    /**
     * Method to check whether a user has update permissions for an announcement
     * @param string $item Record Id of the announcement
     * @return boolean
     */
    protected function checkPermission($item)
    {
        if (!is_array($item)) {
            $item = $this->objAnnouncements->getMessage($item);
        }
        if ($item === FALSE) {
            return FALSE;
        }
        if ($item['contextid'] == 'site' && $this->isAdmin) {
            return TRUE;
        }
        if ($item['contextid'] == 'context' && count($this->lecturerContext) > 0) {
            // See if some items match
            $diff = array_intersect($this->lecturerContext, $this->objAnnouncements->getMessageContexts($item['id']));
            // If yes, user can edit or delete
            if (count($diff) > 0) {
                return TRUE;
            }
        }
        return FALSE;
    }


    // Beginning of Functions Relating to Actions in the Controller //



    /**
    * Method to display the template to show the list of announcements
    */
    private function __home()
    {
        //use own template here
        $this->setLayoutTemplate(NULL);
        //$numAnnouncements = $this->objAnnouncements->getNumAnnouncements($this->userContext);
        //$this->setVarByRef('numAnnouncements', $numAnnouncements);
        return 'home_tpl.php';
    }

    /**
     * Method to add an announcement
     */
    private function __add()
    {
        $this->setVar('mode', 'add');
        return 'addedit_tpl.php';
    }

    /**
     * Method to save an announcement
     */
    private function __save()
    {
        $mode = $this->getParam('mode');
        $title = $this->getParam('title');
        $recipienttarget = $this->getParam('recipienttarget');
        $contexts = $this->getParam('contexts', array());
        $email = $this->getParam('email');
        $message = $this->getParam('message');
        $email = ($email == 'Y');
        if (
            ($mode == 'add'
            || $mode == 'fixup')
            && ($title == ''
            || strip_tags($message) == '')) {
            $this->setVar('mode', 'fixup');
            $this->setVar("errorMessage", $this->objLanguage->languageText('mod_announcements_errortitlemessagerequired', 'announcements'));
            $this->setVar("title", $title);
            $this->setVar('recipienttarget', $recipienttarget);
            $this->setVar('contexts', $contexts);
            $this->setVar("message", $message);
            return 'addedit_tpl.php';
        } else if ($mode == 'add' || $mode == 'fixup') //  || $mode == 'save'
        {
            $result = $this->objAnnouncements->addAnnouncement($title, $message, $recipienttarget, $contexts, $email);
            //add to activity streamer/log
            if($this->eventsEnabled)
            {
                $message = $this->objUser->getsurname()." ".$this->objLanguage->languageText('mod_announcements_hasaddeda', 'announcements')." ".$this->objContext->getContextCode()." ".$this->objLanguage->languageText('mod_announcements_announcement', 'announcements').": ".$title;
                $this->eventDispatcher->post($this->objActivityStreamer, "context", array(
                    'title'=> $message,
    				'link'=> $this->uri(array()),
    				'contextcode' => $this->objContext->getContextCode(),
    				'author' => $this->objUser->fullname(),
    				'description'=>$message
    			));
            }
            return $this->nextAction('view', array('id'=>$result));
        }
    }

    /**
     * Method to view an announcement
     */
    private function __view()
    {
        $id = $this->getParam('id');
        $announcement = $this->objAnnouncements->getMessage($id);
        if ($announcement == FALSE) {
            return $this->nextAction(NULL, array('error'=>'unknownannouncement'));
        } else {
            $this->setVarByRef('announcement', $announcement);
            return 'view_tpl.php';
        }
    }

    /**
     * Method to respond via ajax for a listing of all announcements
     */
    private function __getajax()
    {
        $page = $this->getParam('page', 0);
        $announcements = $this->objAnnouncements->getAllAnnouncements($this->userContexts, $this->itemsPerPage, $page);
        if (count($announcements) == 0) {
            echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_announcements_noannouncements', 'announcements', 'There are no announcements').'</div>';
        } else {
            return $this->generateAjaxResponse($announcements);
        }
    }

    /**
     * Method to respond via ajax for a listing of the current context announcements
     */
    private function __getcontextajax()
    {
        $page = $this->getParam('page', 0);
        $announcements = $this->objAnnouncements->getContextAnnouncements(
          $this->objContext->getContextCode(), $this->itemsPerPage, $page
        );
        if (count($announcements) == 0) {
            echo '<div class="noRecordsMessage">'
              .$this->objLanguage->languageText(
                'mod_announcements_noannouncements', 'announcements',
                'There are no announcements'
              ) .'</div>';
        } else {
            return $this->generateAjaxResponse($announcements);
        }
    }

    /**
     * Method to convert db results into a table before responding via ajax
     */
    private function generateAjaxResponse($announcements)
    {
        $this->loadClass('link', 'htmlelements');
            $this->loadClass('htmlheading', 'htmlelements');
            $objDateTime = $this->getObject('dateandtime', 'utilities');
            $objTrimString = $this->getObject('trimstr', 'strings');
            $table = $this->newObject('htmltable', 'htmlelements');
			//$table->width= '80%';
            $table->startHeaderRow();
            $table->addHeaderCell($this->objLanguage->languageText('word_date', 'system', 'Date'), "180");
            $table->addHeaderCell($this->objLanguage->languageText('word_title', 'system', 'Title'));
            $table->addHeaderCell($this->objLanguage->languageText('word_by', 'system', 'By'), "200");
            $table->addHeaderCell($this->objLanguage->languageText('word_type', 'system', 'Type'), "70");
			$table->addHeaderCell('&nbsp;', "70");
			$table->endHeaderRow();
            // Initialise class for odd even.
            $rowClass = 'even';
            foreach ($announcements as $announcement)
            {
                $rowClass = ($rowClass == 'even')? 'odd' : 'even';
                $link = new link ($this->uri(array('action'=>'view', 'id'=>$announcement['id'])));
                $link->link = $announcement['title'];


                $vLink = '<a class="an_vw_msg" href="javascript:void(null)" id="'
                  . $announcement['id'] . '">'
                  . $announcement['title'] . '</a>';
                $message = "<div id='msg_" . $announcement['id']
                  . "' class='announcement_hider' style='display: none;'>"
                  . $announcement['message'] . "</div>";


                //Get and set the edit icon
                $objEdIcon = $this->getObject('geticon', 'htmlelements');
                $objEdIcon->setIcon('edit');
                //Link to edit using the edit icon
                $editLink = new link ($this->uri(array('action'=>'edit', 'id'=>$announcement['id'])));
                $editLink->link = $objEdIcon->show();
                //Get and set the delete icon
                $objIcon = $this->getObject('geticon', 'htmlelements');
                $objIcon->setIcon('delete');
                //Link to delete using the delete icon
                $deleteArray = array('action'=>'delete', 'id'=>$announcement['id']);
                $deleteLink = $objIcon->getDeleteIconWithConfirm($announcement['id'], $deleteArray, 'announcements');
                $table->startRow($rowClass);
                $table->addCell($objDateTime->formatDate($announcement['createdon']), "180");


                $table->addCell($vLink . "<br />" . $message);


                $table->addCell($this->objUser->fullName($announcement['createdby']), "200");
                if ($announcement['contextid'] == 'site') {
                    $type = $this->objLanguage->languageText('mod_announcements_siteword', 'announcements', 'Site');
                } else {
                    $type = ucwords($this->objLanguage->code2Txt('mod_context_context', 'context', NULL, '[-context-]'));
                }
                $table->addCell($type, "70");
				if ($this->checkPermission($announcement['id'])) {
					$table->addCell($editLink->show()."&nbsp;&nbsp;".$deleteLink, "70");
				} else {
                    $table->addCell("&nbsp;", "70");
                }
                $table->endRow();
            }
            echo $table->show();
    }

    /**
     * Method to show the form to edit an announcement
     */
    private function __edit()
    {
        $id = $this->getParam('id');
        $announcement = $this->objAnnouncements->getMessage($id);
        if ($announcement == FALSE) {
            return $this->nextAction(NULL, array('error'=>'unknownannouncement'));
        } else if (!$this->checkPermission($announcement['id'])) {
            return $this->nextAction(NULL, array('error'=>'nopermission'));
        } else {
            $this->setVarByRef('announcement', $announcement);
            $contextAnnouncementList = $this->objAnnouncements->getMessageContexts($id);
            $this->setVarByRef('contextAnnouncementList', $contextAnnouncementList);
            $this->setVar('mode', 'edit');
            return 'addedit_tpl.php';
        }
    }

    /**
     * Method to update an announcement
     *
     */
    private function __update()
    {
        $id = $this->getParam('id');
        $title = $this->getParam('title');
        $message = $this->getParam('message');
        $email = $this->getParam('email');
        $mode = $this->getParam('mode');
        $recipienttarget = $this->getParam('recipienttarget');
        $contexts = $this->getParam('contexts');
        $email = ($email == 'Y') ? TRUE : FALSE;
        if (!$this->checkPermission($id)) {
            return $this->nextAction(NULL, array('error'=>'nopermission'));
        } else {
            $this->objAnnouncements->updateAnnouncement($id, $title, $message, $recipienttarget, $contexts, $email);
            return $this->nextAction('view', array('id'=>$id));
        }
    }

    /**
     * Method to delete an announcement
     */
    private function __delete()
    {
        $id = $this->getParam('id');
        if (!$this->checkPermission($id)) {
            return $this->nextAction(NULL, array('error'=>'nopermission'));
        } else {
            $announcement = $this->objAnnouncements->deleteAnnouncement($id);
            return $this->nextAction(NULL);
        }
    }
}

?>
