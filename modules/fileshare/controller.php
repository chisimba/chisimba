<?php
/* -------------------- filestore class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Controller class for the fileshare module.
* @author James Scoble
* @author Jeremy O'Connor
* @copyright (C) 2006 UWC
*/

class fileshare extends controller
{
	public $objConfig;
	public $objLanguage;
    public $objDbFileShare;

    private $objDbContext;
    private $objDbWorkgroup;

    public function init()
    {
        $this->objLanguage =& $this->getObject('language','language');
        $this->objConfig = &$this->getObject('altconfig','config');
		/*
        $this->objUser=& $this->getObject('user', 'security');
        $this->userId=$this->objUser->userId();
		*/
		//$this->objDbContext = &$this->getObject('dbcontext','context');
        $this->objDbWorkgroup=$this->getobject('dbworkgroup', 'workgroup');
        $this->objDbFileShare=$this->getobject('dbfileshare');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }

    /**
    * The dispatch method
    * @param string $action
    */
    public function dispatch($action=NULL)
    {
		$this->setVarByRef('objLanguage',$this->objLanguage);

        //$systemType = $this->objConfig->getValue("SYSTEM_TYPE", "contextabstract");
        $isAlumni = false; //($systemType == "alumni");

		// Get context description
		//$this->objDbContext = &$this->getObject('dbcontext','context');
		$this->contextCode = '0'; //$this->objDbContext->getContextCode();
        $this->setVar('contextCode',$this->contextCode);
		if (is_null($this->contextCode)) {
            if ($isAlumni) {
    			$this->contextTitle = "Lobby";
            } else {
                die('Access denied');
            }
		} else {
			$this->contextTitle = 'Context0'; //$this->objDbContext->getTitle();
		}
        $this->setVar('contextTitle',$this->contextTitle);

        $this->workgroupId = $this->objDbWorkgroup->getWorkgroupId();
        if (is_null($this->workgroupId)) {
            die("Access denied");
        }
        $this->setVar('workgroupId',$this->workgroupId);
        $workgroupDescription = 'Workgroup0'; //$this->objDbWorkgroup->getDescription($this->workgroupId);
        $this->setVar('workgroupDescription', $workgroupDescription);

		// Add context and workgroup descriptions to breadcrumbs
		$objBreadcrumbs =& $this->getObject('tools','toolbar');
		$this->loadClass('link', 'htmlelements');

		// Get Context link
		$contextLink = new link ($this->uri(array(), 'context'));
		$contextLink->link = $contextTitle;

		// Get workgroup link
		$workgroupLink = new link ($this->uri(array(), 'workgroup'));
		$workgroupLink->link = $workgroupDescription;

		$objBreadcrumbs->insertBreadCrumb(array($contextLink->show(),$workgroupLink->show()));

        switch ($action)
        {
		/*
        case 'dofileupload':
            $this->setPageTemplate('upload_page_tpl.php');  // Here we don't break or return, because we want to drop down to the next option
		*/
		case 'upload':
            $this->setLayoutTemplate("user_layout_tpl.php");
			return "upload_tpl.php";
        case 'uploadconfirm':
        	/*
        	$filename=$_FILES['userFile']['name'];
            if (preg_match('/^.*\.(.*)$/i',$filename,$matches)) {
            	if (strtolower( $matches[1] )=='php')
                	return;
            }
            */
            $this->objDbFileShare->uploadFile(
				$this->contextCode,	
				$this->workgroupId,
				$_POST['title'],
				$_POST['description'],
				$_POST['version']
			);
            $this->setLayoutTemplate("user_layout_tpl.php");
            //return 'upload_tpl.php';
			return "main_tpl.php";
		case 'edit':
            $id=$this->getParam('id');
			$this->setVar('id',$id);
			$files = $this->objDbFileShare->listSingle($id);
			$file = $files[0];
			$this->setVar('filename',$file['filename']);
			$this->setVar('title',$file['title']);
			$this->setVar('description',$file['description']);
			$this->setVar('version',$file['version']);
            $this->setLayoutTemplate("user_layout_tpl.php");
			return "edit_tpl.php";
		case 'editconfirm':
			$this->objDbFileShare->updateFile(
				$this->getParam('id'),
				$_POST['title'],
				$_POST['description'],
				$_POST['version']
			);
			return $this->nextAction(null);
		case 'delete':
            $this->objDbFileShare->deleteFile($this->getParam('id'));
            $this->setLayoutTemplate("user_layout_tpl.php");
            //return 'upload_tpl.php';
			return "main_tpl.php";
		/*
        case 'contextlist':
            return 'contextfiles_tpl.php';
            break;
		*/
        /*
		case 'display':
		*/
		/*
        case 'download':
            $this->setPageTemplate('download_page_tpl.php');
            return 'download_tpl.php';
		*/
		/*
        case 'contextdownload':
            $this->setPageTemplate('contextdownload_page_tpl.php');
            return 'filedownload_tpl.php';
            break;
		*/
        default:
            $this->setLayoutTemplate("user_layout_tpl.php");
            //return 'upload_tpl.php';
			return "main_tpl.php";
        }
    }
}
?>