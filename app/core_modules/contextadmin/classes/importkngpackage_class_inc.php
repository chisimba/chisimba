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
	 * @var object $objDebug - debugging flag to display information
	*/
	public $objDebug;
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
		$this->objConfig = &$this->getObject('altconfig','config');
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
		$this->objDebug = FALSE;
		#Un-comment to view debug information
		//$this->objDebug = TRUE;
	}
	public $fileNames;
	public $imageIds;
	public $pageIds;
	public $fileIds;
	/**
	 * Controls the process for import KNG content
	 * Calls all necessary functions an does error checking
	 *  
	 * @param $contextcode selected course
	 * @return TRUE - Successful execution
	*/
	function importKNGcontent($contextcode)
	{
		$this->objIEUtils->fileModOff();
		if(!isset($contextcode))
		{
			return  "choiceError";
		}
		//Retrieve data within context
		$courseData = $this->objIEUtils->getCourse($contextcode);
		$oldContextCode = $courseData['0']['contextcode'];
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
		#Initialize all locations
		$init = $this->initLocations($writeCourse['contextcode'], $writeCourse['title']);
		//Write Resources
		//Write Images to Chisimba usrfiles directory
		$writeKNGImages = $this->objIEUtils->writeImages($oldContextCode, $this->imagesLocation);
		if(!isset($writeKNGImages))
		{
			return  "imageWriteError";
		}
		//Load Images into Chisimba
		$this->imageIds = $this->uploadImagesToChisimba($imagesLocation);
		//Write Htmls to Chisimba usrfiles directory
		//Course id
		$courseId = $courseData['0']['id'];
		//Retrieve html page data within context
		$courseContent = $this->objIEUtils->getCourseContent($courseId);
		//Write Html's to specified directory  (resources folder)
		$writeKNGHtmls = $this->objIEUtils->writeKNGHtmls($courseData, $courseContent, $this->docsLocation, "kng");
		//Load Html's into Chisimba
		$menutitles = $this->loadToChisimba($courseContent, $courseData, $this->contextCode);
		$this->fileNames = $this->objIEUtils->list_dir_files($this->courseContentPath,0);
		#Rebuild html images and url links
		$rebuildHtml = $this->rebuildHtml($menutitles, $this->fileNames);
		if(!isset($rebuildHtml))
		{
			if($this->objError)
				return  "rebuildHtmlError";
		}		

		return TRUE;
	}

	public $contentBasePath;
	public $courseContentBasePath;
	public $contextCode;
	public $courseTitle;
	public $courseContentPath;
	public $imagesLocation;
	public $docsLocation;
	public $filesLocation;
	/**
	 * 
	 * 
	 * 
	*/
	function initLocations($contextcode, $courseTitle)
	{
		#Static Chisimba file locations
		#opt/lampp/htdocs/chisimba_framework/app/usrfiles/
		$this->contentBasePath = $this->objConfig->getcontentBasePath();
		$this->courseContentBasePath = $this->contentBasePath."content/";
		$this->contextCode = strtolower(str_replace(' ','_',$contextcode));
		$this->courseTitle = strtolower(str_replace(' ','_',$courseTitle));
		$this->courseContentPath = $this->courseContentBasePath.$this->contextCode;
		$this->imagesLocation = $this->courseContentPath."/images";
		$this->docsLocation = $this->courseContentPath."/staticcontent";
		$this->filesLocation = $this->courseContentPath."/documents";
		$locations = array('contentBasePath' => $this->contentBasePath,
					'courseContentBasePath' => $this->courseContentBasePath,
					'contextCode' => $this->contextCode,
					'courseContentPath' => $this->courseContentPath,
					'imagesLocation' => $this->imagesLocation,
					'docsLocation' => $this->docsLocation
					);
		#Enter context
		$enterContext = $this->objDBContext->joinContext($this->contextCode);
		if($this->objDebug)
		{
			echo $this->contentBasePath."<br />";
			echo $this->courseContentBasePath."<br />";
			echo $this->contextCode."<br />";
			echo $this->courseContentPath."<br />";
			echo $this->imagesLocation."<br />";
			echo $this->docsLocation."<br />";
		}

		return $locations;
	}

	public $courseId;
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
		$contextcode = $courseData['0']['contextcode'];
		$newCourse['contextcode'] = $contextcode.'_'.$this->objIEUtils->generateUniqueId();
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
		$this->courseId = $courseCreated;
		if(!isset($courseCreated) && $courseCreated)
		{
			return  "courseCreateError";
		}

		return $newCourse;
	}

	/**
	 * Writes all images used by course to new database (Chisimba)
	 * Makes query to tbl_files
	 * 
	 * @param string $folder - selected course
	 * @return array $indexFolder - list of id fields belonging to images
	*/
	function uploadImagesToChisimba($folder = '')
	{
		#Initialize Inner variables
		parent::init('tbl_files');
		#Add Images to database
		$indexFolder = $this->objIndex->indexFolder($this->imagesLocation, $this->objUser->userId());
		#Match image Id's to image names
		foreach($indexFolder as $pageId)
		{
			$filter = "WHERE id = '$pageId'";
			$result = $this->getAll($filter);
			$aFile = $result['0']['filename'];
			$imageIds[$aFile] = $pageId;
		}

		return $imageIds;
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
		#Pre-initialize variables
		static $i = 0;
		static $j = 0;
		static $k = 0;
		$menutitles = array();
		$values = array('',
				'menutitle' => $courseData['0']['menutext'],
				'content' => $courseData['0']['about'],
				'language' => "en",
				'headerscript' => "");
		$this->addChapters("", $courseData['0']['about']);
		$writePage = $this->writePage($values, $contextCode);
		static $i = 0;
		foreach($courseContent as $content)
		{
			$values = array('',
					'menutitle' => $content['0']['menu_text'],
					'content' => $content['0']['body'],
					'language' => "en",
					'headerscript' => "",
					'bookmark' => $i,
					'isbookmark' => 'Y');
			//Insert into database
			$menutitle = $this->writePage($values, $contextCode);
			$menutitles[$i] = $menutitle;
			$i++;
		}

		return $menutitles;
	}

	public $chapterId;
	function addChapters($title = '', $intro = '')
	{
		//Add Chapters
		//Course
		if(!(strlen($title) > 1))
			$title = $this->contextCode;
		if(!(strlen($intro) > 1))
			$intro = "Not Available";
		$visibility = 'Y';
		$this->chapterId = $this->objChapters->addChapter('', $title, $intro);
		$result = $this->objContextChapters->addChapterToContext($this->chapterId, $title, $visibility);

	}

	/**
	 * Write content to Chisimba database
	 *
	 * @param array $values - page details
	 * @param string $contextCode - course contextcode
	 *
	 * @return boolean 
	 *
	*/
	function writePage($values, $contextCode='')
	{
		$tree = $this->objContentOrder->getTree($this->contextCode, 'dropdown', $parent);
		//Add page
       		$titleId = $this->objContentTitles->addTitle('', 
								$values['menutitle'],
								$values['content'],
								$values['language'],
								$values['headerscript']);
       		$this->pageIds[$values['filename']] = $this->objContentOrder->addPageToContext($titleId, $parent, $contextCode, $this->chapterId, $values['bookmark'], $values['isbookmark']);
		//}

		return $values['menutitle'];
	}

	/**
	 * Control function to manipulate html pages and resources
	 * and re-insert into database
	 * 
	 * @param array $menutitles - each menu title of html page
	 * @param array $fileNames - the file name of resource
	 *
	 * @return TRUE - on successful execution
	 *
	*/
	function rebuildHtml($menutitles, $fileNames)
	{
		#switch tables
		parent::init('tbl_contextcontent_pages');
		#Retrieve resources
		#Manipulate images
		static $i = 0;
		static $j = 0;
		foreach($menutitles as $menutitle)
		{
			$filter = "WHERE menutitle = '$menutitle'";
			$result = $this->getAll($filter);
			if(count($result) > 0)
			{
				#Retrieve page contents
				$fileContents = $result['0']['pagecontent'];
				$id = $result['0']['id'];
				#Rewrite images source in html
				$page = $this->objIEUtils->changeImageSRC($fileContents, $this->contextCode, $fileNames, $this->imageIds);
				#Reinsert into database with updated images
				if(strlen($page) > 1 )
				{
					$update = $this->update('id', $id, array('pagecontent' => $page));
					if($i==0)
					{
						#Modify about in tbl_context
						parent::init('tbl_context');
						$this->update('id', $this->courseId, array('about' => $page));
						#switch tables
						parent::init('tbl_contextcontent_pages');
						$i++;
					}
				}
			}
		}
		#Manipulate links 
		foreach($menutitles as $menutitle)
		{
			$filter = "WHERE menutitle = '$menutitle'";
			$result = $this->getAll($filter);
			if(count($result) > 0)
			{
				#Retrieve page contents
				$fileContents = $result['0']['pagecontent'];
				$id = $result['0']['id'];
				#Rewrite links source in html
				$page = $this->objIEUtils->changeLinkUrl($fileContents, $this->contextCode, $fileNames, $this->imageIds);
				#Reinsert into database with updated links
				if(strlen($page) > 1 )
				{
					$update = $this->update('id', $id, array('pagecontent' => $page));
					if($j==0)
					{
						#Modify about in tbl_context
						parent::init('tbl_context');
						$this->update('id', $this->courseId, array('about' => $page));
						#switch tables
						parent::init('tbl_contextcontent_pages');
						$j++;
					}
				}
			}
		}

		return TRUE;
	}

}