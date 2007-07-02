<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The class importKNGPackage that manages 
 * the import of KNG content into Chisimba
 * @package importkngpackage
 * @category context
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Jarrett Jordaan
 * The process for import KNG content is:
 * 
 * 
 */

class importKNGPackage extends dbTable
{
	/**
	 * @var object $objIEUtils
	*/
	public $objIEUtils;
	/**
	 * File Location handler
	 *
	 * @var object
	 */
	public $objConf;
	/**
	 * File Upload handler
	 *
	 * @var object
	 */
	public $objUpload;
	/**
	 * @var object $objContextContent
	*/
	public $objContextContent;
	/**
	 * @var object $objDebug - debugging flag to display information
	*/
	/**
	 * @var object $contextCode - the course context code
	*/
	public $contextCode;

	/**
	 * The constructor
	*/
	function init()
	{
		//Load Import Export Utilities class
		$this->objIEUtils = & $this->newObject('importexportutils','contextadmin');
		//Load Filemanager class
		$this->objIndex =& $this->getObject('indexfileprocessor', 'filemanager');
		//Load System classes
		$this->objConf = &$this->getObject('altconfig','config');
		$this->objUser =& $this->getObject('user', 'security');
		//Load Chapter classes
		$this->objChapters =& $this->getObject('db_contextcontent_chapters','contextcontent');
		$this->objContextChapters =& $this->getObject('db_contextcontent_contextchapter','contextcontent');
		//Load context classes
        	$this->objContentPages =& $this->getObject('db_contextcontent_pages','contextcontent');
	        $this->objContentOrder =& $this->getObject('db_contextcontent_order','contextcontent');
        	$this->objContentTitles =& $this->getObject('db_contextcontent_titles','contextcontent');
	        $this->objContentInvolvement =& $this->getObject('db_contextcontent_involvement','contextcontent');
        	$this->objDBContext = & $this->newObject('dbcontext', 'context');

	}

	/**
	 * Controls the process for import KNG content
	 * Calls all necessary functions an does error checking
	 *  
	 * @param $contextcode selected course
	 * @return TRUE - Successful execution
	*/
	function importKNGcontent($contextcode)
	{
		$contextcodeInChisimba = strtolower(str_replace(' ','_',$contextcode));
		$contextcodeInChisimba = strtolower(str_replace('$','_',$contextcode));
		$basePath = $this->objConf->getcontentBasePath();
		$basePathNew = $basePath."content/".$contextcodeInChisimba;
		//Static Chisimba file locations
		//opt/lampp/htdocs/chisimba_framework/app/usrfiles/
		$contentBasePath = $this->objConf->getcontentBasePath();
		$courseContentBasePath = $contentBasePath."content/";
		$courseContentPath = $courseContentBasePath.$contextcodeInChisimba;
		$imagesLocation = $courseContentPath."/images";
		$docsLocation = $courseContentPath."/documents";
		$this->contextCode = $contextcode;
		//Enter context
		$enterContext = $this->objDBContext->joinContext($this->contextCode);

		if(!isset($contextcode))
		{
			return  "choiceError";
		}
		//Retrieve data within context
		$courseData = $this->objIEUtils->getCourse($contextcode);
		if(!isset($courseData))
		{
			return  "courseReadError";
		}
		//Write course data to Chisimba
		$writeCourse = $this->writeKNGCourseToChisimba($courseData);
		if(!isset($writeCourse))
		{
			return  "courseWriteError";
		}
		//Write Resources
		//Write Images to Chisimba usrfiles directory
		$writeKNGImages = $this->objIEUtils->writeImages($contextcode);
		if(!isset($writeKNGImages))
		{
			return  "imageWriteError";
		}
		//Write Htmls to Chisimba usrfiles directory
		//Course id
		$courseId = $courseData['0']['id'];
		//Retrieve html page data within context
		$courseContent = $this->objIEUtils->getCourseContent($courseId);
		//Write Htmls to specified directory  (resources folder)
		$writeKNGHtmls = $this->objIEUtils->writeKNGHtmls($courseData, $courseContent, $docsLocation, "kng");
		//Load data into Chisimba
		$loadData = $this->loadToChisimba($courseContent, $courseData, $this->contextCode);
		if(!isset($loadData))
		{
			return  "loadDataError";
		}
		$this->uploadImagesToChisimba($imagesLocation);

		return TRUE;
	}

	/**
	 * Control loading resources into Chisimba
	 * and file manipulation functions
	 *
	 * @param 
	 * @param 
	 *
	 * @return 
	 *
	*/
	function loadToChisimba($courseContent, $courseData, $contextCode ='')
	{
		$values = array('',
				'menutitle' => $courseData['0']['menutext'],
				'content' => $courseData['0']['about'],
				'language' => "en",
				'headerscript' => "");
		$this->addChapters("", $courseData['0']['about']);
		$writePage = $this->writePage($values, $contextCode);
		foreach($courseContent as $content)
		{
			$values = array('',
					'menutitle' => $content['0']['menu_text'],
					'content' => $content['0']['body'],
					'language' => "en",
					'headerscript' => "");
			//Insert into database
			$writePage = $this->writePage($values, $contextCode);
		}

		return TRUE;
	}

	public $chapterIds;
	function addChapters($title = '', $intro = '')
	{
		//Add Chapters
		//Course
		if(!(strlen($title) > 1))
			$title = $this->contextCode;
		if(!(strlen($intro) > 1))
			$intro = "Not Available";
		$visibility = 'Y';
		$this->chapterIds = $this->objChapters->addChapter('', $title, $intro);
		$result = $this->objContextChapters->addChapterToContext($this->chapterIds, $title, $visibility);

	}

	/**
	 * Write content to Chisimba database
	 *
	 * @param 
	 * @param 
	 *
	 * @return 
	 *
	*/
	function writePage($values, $contextCode='')
	{
		//duplication error needs to be fixed by Tohir
		parent::init('tbl_contextcontent_pages');
		$menutitle = $values['menutitle'];
		$filter = "WHERE menutitle = '$menutitle'";
		$result = $this->getAll($filter);
		if(!count($result) > 0)
		{
			//No idea!!!
			$tree = $this->objContentOrder->getTree($this->contextCode, 'dropdown', $parent);
			//Add page
        		$titleId = $this->objContentTitles->addTitle('', 
									$values['menutitle'],
									$values['content'],
									$values['language'],
									$values['headerscript']);
        		$this->objContentOrder->addPageToContext($titleId, $parent, $contextCode, $this->chapterIds);
		}

		return TRUE;
	}

	/**
	 * Writes course data from KNG to the new database (Chisimba)
	 * Converts 2d array to 1d array before passing it
	 * Makes query to tbl_context in new database
	 * 
	 * @param array $courseData - 2d array containing course data
	 * @return array $newCourse - 1d array containing course data
	*/
	function writeKNGCourseToChisimba($courseData)
	{
		//Convert 2-d array into 1-d array
		$newCourse['id'] = $courseData['0']['id'];
		$newCourse['contextcode'] = $courseData['0']['contextcode'];
		$newCourse['title'] = $courseData['0']['title'];
		$newCourse['menutext'] = $courseData['0']['menutext'];
		$newCourse['userid'] = $courseData['0']['userid'];
		$newCourse['about'] = $courseData['0']['about'];
		if($courseData['isactive'] == 0)
			$newCourse['isactive'] = "Public";
		else if($courseData['isactive'] == 1)
			$newCourse['isactive'] = "Open";
		else
			$newCourse['isactive'] = "Private";
		if($courseData['isclosed'] == 1)
			$newCourse['isclosed'] = "Published";
		else
			$newCourse['isclosed'] = "UnPublished";
		//Create course
		$courseCreated= $this->objIEUtils->createCourseInChisimba($newCourse);
		if(!isset($courseCreated) && $courseCreated)
		{
			return  "courseCreateError";
		}

		return $newCourse;
	}

	/**
	 * Writes all images used by KNG course to new database (Chisimba)
	 * Makes query to tbl_context_file
	 * 
	 * @param string $contextcode - selected course
	 * @return array $indexFolder - list of id fields belonging to images
	*/	
	function uploadImagesToChisimba($folder)
	{
		//duplication error needs to be fixed by Tohir
		parent::init('tbl_files');

		//Course Images
		//$indexFolder = $this->objIndex->indexFolder($folder, $this->objUser->userId());
		//$rootId = $this->objUser->userId();
		//$addImages = $this->objIEUtils->addImagesToChisimba($folder,$rootId);
		//var_dump($indexFolder);
//var_dump($folder);
		//echo $this->objIndex->processIndexedFile($folder."/"."0001.jpg",'1');

		return TRUE;
	}

/*
	/**
	 * Writes all images used by KNG course to new database (Chisimba)
	 * Makes query to tbl_context_file
	 * 
	 * @param string $contextcode - selected course
	 * @return array $indexFolder - list of id fields belonging to images
	
	function uploadImagesToChisimba($folder)
	{
		//Course Images
		$indexFolder = $this->objIndex->indexFolder($folder, $this->objUser->userId());
		//$rootId = $this->objUser->userId();
		//$addImages = $this->objIEUtils->addImagesToChisimba($folder,$rootId);

		return TRUE;
	}

	/**
	 * Writes all htmls specific to context to usrfiles directory of new system (Chisimba)
	 * For modification before insertion into new database
	 *
	 * @param $contextcode selected course
	 * @return TRUE - Successful execution

	function writeKNGHtmls($contextcode)
	{
		//Course htmls

		
		return TRUE;
	}

	/**
	 *  Writes all pages created in old course to new database
	 *  Makes query to tbl_context_page_content
	 * 
	 * @param $contextcode selected course
	 * @return TRUE - Successful execution

	function uploadHtmls($contextcode)
	{
		
	
		
		return TRUE;
	}
*/
}