<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Controller class for readinglist module
* @author John Abakpa, Juliet Mulindwa
* @copyright 2005 University of the Western Cape
*/

class readinglist extends controller{

var $objUser;
var $objLanguage;
var $objDbReadingList;
var $contextId;
var $contextTitle;
    /**
    * @var string $userId The id on the current logged in user
    */
    protected $userId;

/**
* The Init function
*/
function init ()
{
	// Get the user object.
	$this->objUser =& $this->getObject('user', 'security');
	// Get the language object.
	$this->objLanguage =& $this->getObject('language','language');
	// Get the DB object.
	$this->objDbReadingList =& $this->getObject('dbreadinglist');
	$this->objDbReadingList_links =& $this->getObject('dbreadinglist_links');
	$this->objDbReadingList_comment =& $this->getObject('dbreadinglist_comment');
	//Get the activity logger class
	$this->objLog=$this->newObject('logactivity', 'logger');
	//Log this module call
	$this->objLog->log();
    $this->objUser=&$this->newObject('user','security');
    $this->userId=$this->objUser->userId();
				
}

/**
* The dispatch fucntion
* @param string $action The action
* @return string The content template file
*/
function dispatch($action=null)
{
// Set the layout template.
        $this->setLayoutTemplate("layout_tpl.php");


	//$this->setVarByRef('objUser', $this->objUser);
	$this->setVarByRef('objLanguage', $this->objLanguage);
        // Get the context
        $objDbContext = &$this->getObject('dbcontext','context');
        $contextCode = $objDbContext->getContextCode();
        // If we are not in a context...

	if ($contextCode == null) {
	    $this->contextId = "root";
	    $this->setVarByRef('contextId', $this->contextId);
	    $this->contextTitle = "Lobby";
	    $this->setVarByRef('contextTitle', $this->contextTitle);
	 }
	// ... we are in a context
        else {
            $this->contextId = $contextCode;
            $this->setVarByRef('contextId', $this->contextId);
            
            $contextRecord = $objDbContext->getContextDetails($contextCode);
            $this->contextTitle = $contextRecord['title'];
            $this->setVarByRef('contextTitle', $this->contextTitle);
        }
	switch ($action) {
	case "add":
		return "add_tpl.php";
	case "addconfirm":
        $link = $this->getParam('link', NULL);
        $id = $this->objDbReadingList->insertSingle(
            $this->contextId,
            $this->getParam('author', NULL),
            $this->getParam('title', NULL),
			$this->getParam('publisher', NULL),
			$this->getParam('publishingYear', NULL),
			//$this->getParam('link', NULL),
			$this->getParam('publication', NULL),
			$this->getParam('country', NULL),
			$this->getParam('language', NULL)
		);
		if($link != NULL){
            $this->objDbReadingList_links->insertSingle($id, $link);
        }
        return $this->nextAction('');
		break;
		
	case "additionals":
		
		
		$id = $this->getParam('id', null);
		//die($id);
		$this->setVarByRef('id',$id);
		$list = $this->objDbReadingList->listSingle($id);		
		if (isset($list[0])){
            $author = $list[0]['author'];
		    $title = $list[0]['title'];
		    $publisher = $list[0]['publisher'];
		    $publishingYear = $list[0]['publishingyear'];
//		    $link = $list[0]['link'];
		    $publication = $list[0]['publication'];
		    $country = $list[0]['country'];
		    $language = $list[0]['language'];
		}
        $this->setVarByRef('author',$author);
		$this->setVarByRef('title',$title);
		$this->setVarByRef('publisher',$publisher);
		$this->setVarByRef('publishingYear',$publishingYear);
//		$this->setVarByRef('link',$link);
		$this->setVarByref('publication',$publication);
		$this->setVarByref('country',$country);
		$this->setVarByref('language',$language);
		return "additional_tpl.php";
		
	case "additionalconfirm":
		$this->nextAction(
		$this->objDbReadingList->insertSingle(
			$this->contextId,
			$this->getParam('author', NULL),
			$this->getParam('title', NULL),
			$this->getParam('publisher', NULL),
			$this->getParam('publishingYear', NULL),
			$this->getParam('link', NULL),
			$this->getParam('publication', NULL),
			$this->getParam('country', NULL),
			$this->getParam('language',NULL)
		));
		break;
		
	case "edit":
		$id = $this->getParam('id', null);
		$this->setVarByRef('id',$id);
		$list = $this->objDbReadingList->listSingle($id);
		$author = $list[0]['author'];
		$title = $list[0]['title'];
		$publisher = $list[0]['publisher'];
		$publishingYear = $list[0]['publishingyear'];
		$link = $list[0]['link'];
		$publication = $list[0]['publication'];
		$country = $list[0]['country'];
		$language = $list[0]['language'];
		$this->setVarByRef('author',$author);
		$this->setVarByRef('title',$title);
		$this->setVarByRef('publisher',$publisher);
		$this->setVarByRef('publishingYear',$publishingYear);
		$this->setVarByRef('link',$link);
		$this->setVarByref('publication',$publication);
		$this->setVarByref('country',$country);
		$this->setVarByref('language',$language);
		return "edit_tpl.php";
	
	case "editconfirm":
		$this->nextAction(
		$id = $this->getParam('id', null),
		$this->objDbReadingList->updateSingle(
			$id,
			$this->getParam('author', NULL),
			$this->getParam('title', NULL),
			$this->getParam('publisher', NULL),
			$this->getParam('publishingYear', NULL),
			$this->getParam('link', NULL),
			$this->getParam('publication', NULL),
			$this->getParam('country', NULL),
			$this->getParam('language', NULL)
		));
		break;
		
	case "deleteconfirm":
		$this->nextAction(
		$id = $this->getParam('id', null),
		$this->objDbReadingList->deleteSingle(
			$id
		));
		break;
		
	case "urls":
		$this->setLayoutTemplate(NULL);
		$id = $this->getParam('id', null);
		$this->setVarByRef('id',$id);
		$list = $this->objDbReadingList->listSingle($id);
		$link = $list[0]['link'];
		$this->setVarByRef('link',$link);
		
		$urls = $this->objDbReadingList_links->getByItem($id);
		$this->setVarByRef('urls', $urls);
		return "urls_tpl.php";
		
	case "urlsconfirm":
		
		//$this->nextAction(
		$id = $this->getParam('id', null);
		$this->objDbReadingList_links->insertSingle(
			$id,
			$this->getParam('url', NULL)
		);
		return $this->nextAction('urls', array('id'=>$id));
		
	case "addurls":
		$id = $this->getParam('id', null);
		$this->setVarByRef('id',$id);
		$list = $this->objDbReadingList->insertSingle($id);
		$link = $list[0]['link'];
		$this->setVarByRef('link',$link);
		return "urls_tpl.php";
		
	case "addurlconfirm":
		$this->nextAction(
		$url = $this->getParam('url', null),
		$this->objDbReadingList_links->insertSingle(
			$url,
			$this->getParam('link', NULL)
		));
		break;
		
	case "deleteurl":
		$urlId = $this->getParam('urlId', null);
		$id = $this->getParam('id', null);
		$this->objDbReadingList_links->deleteSingle($urlId);
		$this->nextAction('urls', array('id'=>$id));
		break;
		
	case "comment":
		$this->setLayoutTemplate(NULL);
		$itemid = $this->getParam('id', null);
		$this->setVarByRef('itemid',$itemid);
		//$list = $this->objDbReadingList->listSingle($id);
		//$this->setVarByRef('comment',$list);//if(!empty($list))
		return "comment_tpl.php";
		
	case "commentconfirm":
		$this->setLayoutTemplate(NULL);
		$itemid = $this->getParam('itemid');
		$comment = $this->getParam('comment');
		$id = $this->objDbReadingList_comment->insertIntoDB($itemid, $comment);
		$this->setVarByRef('id', $id);
		$this->setVarByRef('itemid', $itemid);
		$this->setVar('showConfirm', TRUE);
		return "comment_tpl.php";
		break;
		
	//Work with the scholar google search
    case "schgoogle":
        $q=urlencode($this->getParam('q', NULL));
        //---delete these when all working
        $ie=$this->getParam('ie', NULL);
        $oe=$this->getParam('oe', NULL);
        $hl=$this->getParam('hl', NULL);
        //Grab the data
        $objGrab = & $this->getObject('dbreadinglist');
        $objGrab->saveRecord();
        header("Location: http://scholar.google.com/scholar?q=$q&ie=$ie&oe=$oe&hl=$hl&btnG=Search");
                break;
		
	default:
	}
	$list = $this->objDbReadingList->listAll($this->contextId);
	$this->setVarByRef('list', $list);
	return "view_tpl.php";
}

/*
* Function that checks if a user has been logged in or not 
*/
/*
public function requiresLogin($action)
{
	switch($action){
		case 'addtionals':
			return FALSE;
			
		case 'urls':
			return FALSE;
				
		case 'addurls':
			return FALSE;
				
		case 'deleteurl':
			return FALSE;
				
		case 'comment':
			return FALSE;

		default:
			return TRUE;
			}
				
		return TRUE;

}*/
}
?>