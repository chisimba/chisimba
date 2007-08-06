<?php
/**
 * The class importKNGPackage that manages the import of KNG content into Chisimba
 * 
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   contextadmin
 * @author    Jarrett Jordaan
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   1.0
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
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
	 * @var object $objDebug - debugging flag to display information
	*/
	public $objDebug;
	/**
	 * The constructor
	*/
	public $fileNames;
	public $imageIds;
	public $pageIds;
	public $fileIds;
	public $contentBasePath;
	public $courseContentBasePath;
	public $contextCode;
	public $courseTitle;
	public $courseContentPath;
	public $imagesLocation;
	public $docsLocation;
	public $filesLocation;
	public $courseId;
	public $chapterId;

	function init()
	{
		//Load Import Export Utilities class.
		$this->objIEUtils =  $this->newObject('importexportutils','contextadmin');
		//Load Filemanager class.
		$this->objIndex = $this->getObject('indexfileprocessor', 'filemanager');
		//Load System classes.
		$this->objConfig = $this->getObject('altconfig','config');
		$this->objUser = $this->getObject('user', 'security');
		//Load Chapter classes.
		$this->objChapters = $this->getObject('db_contextcontent_chapters','contextcontent');
		$this->objContextChapters = $this->getObject('db_contextcontent_contextchapter','contextcontent');
		//Load context classes.
	        $this->objContentOrder = $this->getObject('db_contextcontent_order','contextcontent');
        	$this->objContentTitles = $this->getObject('db_contextcontent_titles','contextcontent');
        	$this->objDBContext =  $this->newObject('dbcontext', 'context');
		$this->objDebug = FALSE;
	}

	/**
	 * Controls the process for import KNG content
	 * Calls all necessary functions an does error checking
	 *
	 * @param $contextcode selected course
	 *
	 * @return TRUE - Successful execution
	 *
	*/
	function importKNGcontent($contextcode)
	{
		$this->objIEUtils->fileModOff();
		if(!isset($contextcode))
			if($this->objError)
				return  "choiceError";
		// Retrieve data within context.
		$courseData = $this->objIEUtils->getCourse($contextcode);
		$oldContextCode = $courseData['contextcode'];
		if(!isset($courseData))
			if($this->objError)
				return  "courseReadError";
		// Write course data to Chisimba.
		$courseData['userid'] = $this->objUser->userId();
		$courseData['isclosed'] = 'Public';
		$courseData['isactive'] = 'UnPublished';
		$courseData['contextcode'] = $contextcode.$this->objIEUtils->generateUniqueId('8');
		$writeCourse = $this->objIEUtils->createCourseInChisimba($courseData);
		if(!isset($writeCourse))
			if($this->objError)
				return  "courseWriteError";
		// Initialize all locations.
		$init = $this->initLocations($courseData['contextcode'], $courseData['title']);
		// Write Htmls to Chisimba usrfiles directory.
		// Course id
		$courseId = $courseData['id'];
		// Retrieve html page data within context.
		$courseContent = $this->objIEUtils->getCourseContent($courseId);
		// Write Html's to specified directory  (resources folder).
		if(count($courseContent) > 0)
			$writeKNGHtmls = $this->objIEUtils->writeFiles($courseContent, $this->docsLocation, '', 'html', 'kng');
		// Load Html's into Chisimba.
		if(count($courseContent) > 0)
			$menutitles = $this->loadToChisimba($courseContent, $courseData, $this->contextCode);
		$enterContext = $this->objDBContext->joinContext($this->contextCode);

		return TRUE;
	}

	/**
	 * Sets the global variables and directory paths
	 * 
	 * @param string $contextcode 
	 * @param string $courseTitle
	 * 
	 * @return array $locations - Array of all locations used
	 * 
	*/
	function initLocations($contextcode, $courseTitle)
	{
		// Static Chisimba file locations.
		// opt/lampp/htdocs/chisimba_framework/app/usrfiles/.
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

	/**
	 * Writes all images used by course to new database (Chisimba)
	 * Makes query to tbl_files
	 * 
	 * @param string $folder - selected course
	 * 
	 * @return array $indexFolder - list of id fields belonging to images
	 * 
	*/
	function uploadImagesToChisimba($folder = '')
	{
		// Initialize Inner variables.
		parent::init('tbl_files');
		// Add Images to database.
		$indexFolder = $this->objIndex->indexFolder($this->imagesLocation, $this->objUser->userId());
		// Match image Id's to image names.
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
	 * @param array $courseContent 
	 * @param array $courseData  
	 * @param array $contextCode
	 *
	 * @return array $menutitles - all menutitles of pages
	 *
	*/
	function loadToChisimba($courseContent, $courseData, $contextCode ='')
	{
		static $i = 0;
		static $j = 0;
		static $k = 0;
		$menutitles = array();
		$values = array('',
				'menutitle' => $courseData['menutext'],
				'content' => $courseData['about'],
				'language' => "en",
				'headerscript' => "");
		$this->chapterId = $this->objIEUtils->addChapters($this->contextCode, $this->courseTitle, $this->courseData['about']);
		$writePage = $this->writePage($values, $contextCode);
		static $i = 0;
		foreach($courseContent as $content)
		{
			$menutitle = $content['0']['menu_text'];
			$content = $content['0']['body'];
			if($menutitle == NULL)
				$menutitle = 'None';
			if($content == NULL)
				$content = 'None';
			$values = array('',
					'menutitle' => $menutitle,
					'content' => $content,
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
		// Add page.
       		$titleId = $this->objContentTitles->addTitle('', 
								$values['menutitle'],
								$values['content'],
								$values['language'],
								$values['headerscript']);
       		$this->pageIds[$values['filename']] = $this->objContentOrder->addPageToContext($titleId, $parent, $contextCode, $this->chapterId, $values['bookmark'], $values['isbookmark']);

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
		// switch tables.
		parent::init('tbl_contextcontent_pages');
		// Retrieve resources.
		// Manipulate images.
		static $i = 0;
		static $j = 0;
		foreach($menutitles as $menutitle)
		{
			$filter = "WHERE menutitle = '$menutitle'";
			$result = $this->getAll($filter);
			if(count($result) > 0)
			{
				// Retrieve page contents.
				$fileContents = $result['0']['pagecontent'];
				$id = $result['0']['id'];
				// Rewrite images source in html.
				$page = $this->objIEUtils->changeImageSRC($fileContents, $this->contextCode, $fileNames, $this->imageIds);
				// Reinsert into database with updated images.
				if(strlen($page) > 1 )
				{
					$update = $this->update('id', $id, array('pagecontent' => $page));
					if($i==0)
					{
						// Modify about in tbl_context.
						parent::init('tbl_context');
						$this->update('id', $this->courseId, array('about' => $page));
						// switch tables.
						parent::init('tbl_contextcontent_pages');
						$i++;
					}
				}
			}
		}
		// Manipulate links.
		foreach($menutitles as $menutitle)
		{
			$filter = "WHERE menutitle = '$menutitle'";
			$result = $this->getAll($filter);
			if(count($result) > 0)
			{
				// Retrieve page contents.
				$fileContents = $result['0']['pagecontent'];
				$id = $result['0']['id'];
				// Rewrite links source in html.
				$page = $this->objIEUtils->changeLinkUrl($fileContents, $this->contextCode, $fileNames, $this->imageIds);
				// Reinsert into database with updated links.
				if(strlen($page) > 1 )
				{
					$update = $this->update('id', $id, array('pagecontent' => $page));
					if($j==0)
					{
						// Modify about in tbl_context.
						parent::init('tbl_context');
						$this->update('id', $this->courseId, array('about' => $page));
						// switch tables.
						parent::init('tbl_contextcontent_pages');
						$j++;
					}
				}
			}
		}

		return TRUE;
	}
}
?>