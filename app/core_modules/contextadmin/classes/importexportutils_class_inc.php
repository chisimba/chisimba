<?php
/**
 * The class importexportutils that manages the functions regularly used in the IMS import export module
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
// security check - must be included in all scripts.
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check.
class importexportutils extends dbTable
{
	/**
	 * @var object $objConfig
	*/
	public $objConfig;

    	/**
     	 * The DSN to the database to import FROM
     	 * @var mixed Data Source Name of the data that you wish to import
     	 */
	public $dsn;

	/**
	 * Table name of the tables that we need to connect to
	 * @var string
	 */
	public $_tableName;

	/**
	 * The database (remote) connection object
     	 * @var object
     	 */
    	public $objDb;

    	/**
     	 * Language Object
	 * @var object
	 */
	public $objLanguage;
	public $fileMod;

	/**
	 * The constructor
	 */
	public function init()
	{
		// Load Configuration class.
		$this->objConf = $this->getObject('altconfig','config');
        	// language object.
        	$this->objLanguage = $this->getObject('language', 'language');
		$this->objDBContext =  $this->newObject('dbcontext', 'context');
		$this->objFiles = $this->getObject('dbfile','filemanager');
		// Load Chapter Classes.
		$this->objChapters = $this->getObject('db_contextcontent_chapters','contextcontent');
		$this->objContextChapters = $this->getObject('db_contextcontent_contextchapter','contextcontent');
	}

	/**
	 * Scans a specified folder and returns all file Locations
	 * @param string $dir - Location of folder to scan
	 * @param int $bool - 0 to return filenames and 1 to return file Locations
	 *
	 * @return array $file_list
	 *
	*/
	public function list_dir_files($dir, $bool) 
	{
		$file_list = '';
		$stack[] = $dir;
		while ($stack) 
		{
			$current_dir = array_pop($stack);
			if ($dh = opendir($current_dir)) 
			{
				while (($file = readdir($dh)) !== false) 
				{
                   			if ($file !== '.' AND $file !== '..') 
					{
						$current_file = "{$current_dir}/{$file}";
						if (is_file($current_file)) 
						{
							if($bool == 0)
        	                   				$file_list[] = $file;
                	          			else
                        	   				$file_list[] = "{$current_dir}/{$file}";
                       				}
						elseif (is_dir($current_file)) 
						{
                           				$stack[] = $current_file;
                       				}
                   			}
               			}
           		}
       		}
	return $file_list;
   	}

	/**
	 * Scans a specified folder and returns all folder Locations
	 * @param string $dir - Location of folder to scan
	 * @param int $bool - 0 to return folder names and 1 to return folder Locations
	 *
	 * @return array $file_list
	 *
	*/
	public function list_dir($dir, $bool) 
	{
		$dir_list = '';
		$stack[] = $dir;
		while ($stack) 
		{
           		$current_dir = array_pop($stack);
           		if ($dh = opendir($current_dir)) 
			{
               			while (($file = readdir($dh))) 
				{
                 			if ($file !== '.' AND $file !== '..' AND is_dir("{$current_dir}/$file")) 
					{
						$current_file = "{$current_dir}/{$file}";
						if($bool == "0")
							$dir_list[$file]=$file;
						else
							$dir_list[$current_file]="$current_file";
						$stack[] = $current_file;
        		           	}
               			}
			closedir($dh);
			}
		}
		return $dir_list;
	}

    	/**
     	 * Pseudo constructor method. We have not yet used the standard init() 
	 * function here, or extended dbTable, as we are not really
	 * interested in connecting to the local db with this object.
 	 * @param The name of the server to connect to (predefined) $server
	 *
	 * @return string, set DSN
	 * @access public
	 *
	 */
    	public function setup($server) 
    	{
        	switch ($server) 
		{
			case 'fsiu':
                		$this->dsn = 'mysql:// reader:reader@172.16.203.173/fsiu';
		                return $this->dsn;
                	break;
            		case 'elearn':
                		$this->dsn = 'mysql:// reader:reader@172.16.203.210/nextgen';
		                return $this->dsn;
                	break;
            		case 'santec':
                		$this->dsn = 'mysql:// reader:reader@172.16.203.173/santec';
				return $this->dsn;
                	break;
            		case 'freecourseware':
            			$this->dsn = 'mysql:// next:n3xt@172.16.203.178/ocw';
		            	return $this->dsn;
            		break;
			case '5ive':
            			$this->dsn = 'mysql:// root:0h5h1t.pear@196.21.45.41/chisimba';
		            	return $this->dsn;
            		break;
			case 'pear':
		            	$this->dsn = 'mysql:// root:0h5h1t.pear@196.21.45.41/chisimbademo';
	            		return $this->dsn;
            		break;
			case 'dfx':
            			$this->dsn = 'mysql:// root:0h5h1t.pear@196.21.45.41/dfx';
		            	return $this->dsn;
			break;
			default:
				$this->dsn = $server;
                		return $this->dsn;
			break;
        	}
    	}

	/**
	 * Return a list of currntly available servers
	 */
	public function getServers()
	{
		$serverlist[0] = "fsiu";
		$serverlist[1] = "elearn";
		$serverlist[2] = "santec";
		$serverlist[3] = "freecourseware";
		$serverlist[4] = "5ive";
		$serverlist[5] = "pear";
		$serverlist[6] = "dfx";

		return $serverlist;
	}

	/**
     	 * Build and instantiate the database object for the remote
	 *
	 * @param void
	 *
	 * @return object
	 * @access private
	 *
	 */
    	public function _dbObject() 
    	{
        	require_once 'MDB2.php';
        	// MDB2 has a factory method, so lets use it now.
        	$this->objDb = &MDB2::factory($this->dsn);
        	// Check for errors on the factory method.
        	if (PEAR::isError($this->objDb)) 
		{
            		throw new customException($this->objLanguage->languageText("mod_contextadmin_import_noconn", "contextadmin"));
        	}
        	// set the options.
        	$this->objDb->setOption('portability', MDB2_PORTABILITY_FIX_CASE);
        	// load the date and iterator MDB2 Modules..
        	MDB2::loadFile('Date');
        	MDB2::loadFile('Iterator');
        	// Check for errors.
        	if (PEAR::isError($this->objDb)) 
		{
            		throw new customException($this->objLanguage->languageText("mod_contextadmin_import_noconn", "contextadmin"));
        	}

        return $this->objDb;
    	}

	/**
     	 * Method to query an arbitrarary remote table
     	 * @param string $table
     	 * @param string $filter can be full SQL Query
	 *
     	 * @return resultset
     	 * @access public
	 *
     	 */
	public function queryTable($table, $filter) 
	{
        	$this->_tableName = $table;
        	$res = $this->objDb->query($filter);
        	// set the return mode to return an associative array.
	
        	return $res->fetchAll(MDB2_FETCHMODE_ASSOC);
	}

	/**
	 *
	 */
	public function importDBData($dsn, $table, $query)
	{
		$this->_tableName = $table;
		$dsn1 = $this->setup($dsn);
		$this->objDb = $this->_dbObject();
		$result = $this->objDb->query($query);
        	if(PEAR::isError($result)) 
		{
            		throw new customException($result->getMessage());
            		exit;
        	}
		$resultarray = $result->fetchAll(MDB2_FETCHMODE_ASSOC);

		return $resultarray;
	}

	/**
	 * Use information to create the course
	 *
	 * @param array $newCourse - 1 dimensional array storing course data
	 *
	 * @return TRUE
	 *
	*/
	public function createCourseInChisimba($newCourse)
	{
		$createContext = $this->objDBContext->createContext($newCourse);
		if(!isset($createContext) || $createContext === FALSE)
		{
			return "courseWriteError";
		}
		$saveAboutEdit = $this->objDBContext->saveAboutEdit($newCourse);
		if(!isset($saveAboutEdit) || $saveAboutEdit === FALSE)
		{
			return "courseWriteError";
		}

		return $createContext;
	}

	/**
	 * Retrieves all course data specific to chosen context from old database.
	 * Retrieves all course data specific to chosen context from new database (Chisimba)
	 * Makes query to tbl_context in old database (Nextgen)
	 * Makes query to tbl_context in new database (Chisimba)
	 * 
	 * @param $contextcode selected course
	 * @param string $type
	 * 
	 * @return TRUE - Successful execution
	 *
	*/
	function getCourse($contextcode, $type = NULL)
	{
		if($type == "new")
		{
			// Access new database.
			parent::init('tbl_context');
			$filter = "WHERE contextcode = '$contextcode'";
			$courseData = $this->getAll($filter);

			return $courseData['0'];
		}
		else
		{
			// Set database.
			$dsn = $this->getSession('server');
			// Set table.
			$table = "tbl_context";
			// Set query.
			$query = "SELECT * from tbl_context where contextcode = '$contextcode'";
			// Execute query on specified database and table.
			$courseData = $this->importDBData($dsn, $table, $query);
			if(!isset($courseData))
			{
				return  "courseReadError";
			}
			$this->switchDatabase();
		
			return $courseData['0'];
		}

		return FALSE;
	}

	/**
	 * Retrieves all sub-page data specific to chosen context from old database (nextgen)
	 * Retrieves all sub-page data specific to chosen context from new database (chisimba)
	 * Makes query to tbl_context_nodes in old database
	 * Makes query to tbl_context_page_content in old database
	 * 
	 * @param string $courseId - selected course id
	 * @param string $type 
	 *
	 * @return array $subPages - list of all subpages in context
	 *
	*/
	function getCourseContent($courseId, $type = NULL)
	{
		if($type == "new")
		{
			// Access new database.
			parent::init('tbl_context_nodes');
			$filter = "WHERE tbl_context_parentnodes_id = '$courseId'";
			$courseContent = $this->getAll($filter);
			$subPages = array();
			$i = 0;
			parent::init('tbl_context_page_content');
			foreach($courseContent as $aNode)
			{
				$pageId = $aNode['id'];
				$filter = "WHERE id = '$pageId'";
				$subPages[$i] = $this->getAll($filter);
				$i++;
			}
			if(!isset($courseContent))
				return  "courseReadError";

			return $subPages;
		}
		else
		{
			// Set database.
			$dsn = $this->getSession('server');
			// Set table.
			$table = "tbl_context_nodes";
			// Set query.
			$query = "SELECT * from tbl_context_nodes WHERE tbl_context_parentnodes_id = '$courseId'";
			// Execute query on specified database and table.
			$courseContent = $this->importDBData($dsn, $table, $query);
			$subPages = array();
			$i = 0;
			foreach($courseContent as $aNode)
			{
				$pageId = $aNode['id'];
				// Retrieve each sub-page.
				// Set database.
				$dsn = $this->getSession('server');
				// Set table.
				$table = "tbl_context_page_content";
				// Set query.
				$query = "SELECT * from tbl_context_page_content where id = '$pageId'";
				// Execute query on specified database and table.
				$subPages[$i] = $this->importDBData($dsn, $table, $query);
				$i++;
			}
			if(!isset($courseContent))
				return  "courseReadError";
			$this->switchDatabase();

			return $subPages;
		}

		return FALSE;
	}

	/**
	 * Writes all images specific to context to usrfiles directory of new system (Chisimba)
	 * or to a specified folder
	 *
	 * @param string $resourceFolder - 
	 * @param string $folder - 
	 *
	 * @return $imageIds - File names of images
	 *
	*/
	function writeImages($imageNames, $resourceFolder)
	{
		// files data table
		parent::init('tbl_files');
		foreach($imageNames as $image)
		{
			$filter = "WHERE filename = '$image'";
			$results = $this->getAll($filter);
			foreach($results as $result)
			{
				$path = $result['path'];
				$filepath = $this->objConf->getcontentBasePath().$path;
				$fileContents = file_get_contents($filepath);
				$filepath = $resourceFolder.'/'.$image;
				$fp = fopen($filepath,'w');
				if((fwrite($fp, $fileContents) === FALSE))
					return  "writeResourcesError";
				fclose($fp);
				$imageIds[$i] = $result['id'];
				$i++;
			}
		}
		return $imageIds;
	}

	function writeResources($resourceIds, $resourceFolder)
	{
		// files data table.
		parent::init('tbl_files');
		foreach($resourceIds as $resourceId)
		{
			$filter = "WHERE id = '$resourceId'";
			$results = $this->getAll($filter);
			foreach($results as $result)
			{
				$path = $result['path'];
				$resourceName = $result['filename'];
				$filepath = $this->objConf->getcontentBasePath().$path;
				$fileContents = file_get_contents($filepath);
				$filepath = $resourceFolder.'/'.$resourceName;
				$fp = fopen($filepath,'w');
				if((fwrite($fp, $fileContents) === FALSE))
					return  "writeResourcesError";
				fclose($fp);
				$resourceNames[$i] = $resourceName;
				$i++;
			}
		}

		return $resourceNames;
	}

	/**
	 * 
	 * 
	 * 
	*/
	function switchDatabase()
	{
		// Access old database.
		// Set database.
		$dsn = $this->objConf->getDsn();
		// Set table.
		$table = "tbl_context";
		// Set query.
		$query = "SELECT * from tbl_context";
		// Execute query on specified database and table.
		$dummyData = $this->importDBData($dsn, $table, $query);
	}

	/**
	 * Gets all subpages of a course according to the level the pages are on
	 *
	 * @param string $courseId - selected course id
	 * @param string $level - depth of pages
	 * 
	*/
	function getAllPages($courseId, $level)
	{
		$server = $this->getSession('server');
		$course = $this->getParam('course');
        	// set the table.
        	$tableName = "tbl_context_nodes";
        	// set up the query.
        	$sql = "SELECT * from tbl_context_nodes WHERE tbl_context_parentnodes_id = '$courseId' AND pagelevel = '$level'";
		$dbData = $this->importDBData($server, $tableName, $sql);
		$this->switchDatabase();

		return $dbData;
	}

	/**
	 * Write htmls to IMS resource folder
	 *
	 * @param array $courseData
	 * @param array $courseContent or context code
	 * @param string $tempDirectory
	 *
	 * @return array $htmlfilenames - 
	 *
	*/
	function writeFiles($htmlPages, $resourceFolder, $courseTitle = '', $fileType = '', $packageType = '')
	{
		if(!(preg_match('/\./',$fileType)))
			$fileType = '.'.$fileType;
		$courseTitle = str_replace(' ','',$courseTitle);
		$courseTitle = str_replace('_','',$courseTitle);
		$i = 0;
		if(count($htmlPages) == 1)
		{
			$filename = $courseTitle;
			$filepath = $resourceFolder."/".$filename.$fileType;
			$fp = fopen($filepath,'w');
			$htmlFilenames[$i] = $filename;
			if((fwrite($fp, $htmlPages) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		else
		{
			if($packageType == 'kng')
			{
				foreach($htmlPages as $page)
				{
					$pageContent = $page['0']['body'];
					$filename = "resource".$i.$fileType;
					$filepath = $resourceFolder."/".$filename;
					$fp = fopen($filepath,'w');
					$htmlFilenames[$i] = $filename;
					if((fwrite($fp, $pageContent) === FALSE))
						return  "writeResourcesError";
					fclose($fp);
					$i++;
				}
			}
			else
			{
				foreach($htmlPages as $page)
				{
					$filename = "resource".$i.$fileType;
					$filepath = $resourceFolder."/".$filename;
					$fp = fopen($filepath,'w');
					$htmlFilenames[$i] = $filename;
					if((fwrite($fp, $page) === FALSE))
						return  "writeResourcesError";
					fclose($fp);
					$i++;
				}
			}
		}

		return $htmlFilenames;
	}

	function getCourseHtml($contextcode)
	{
		parent::init('tbl_context');
		$filter = "WHERE contextcode = '$contextcode'";
		$courses = $this->getAll($filter);
		foreach($courses as $course)
		{
			$pageContents = $course['about'];
		}
		
		return $pageContents;
	}

	function getHtmlPages($contextcode = '', $allFilesLocation = '', $folder = '', $resourceFolder = '', $field = '')
	{
		$i = 0;
		if($contextcode)
		{
			parent::init('tbl_contextcontent_order');
			$filter = "WHERE contextcode = '$contextcode' ORDER BY bookmark";
			$orders = $this->getAll($filter);
			foreach($orders as $titleId)
			{
				$titleId = $titleId['titleid'];
				parent::init('tbl_contextcontent_pages');
				$filter = "WHERE titleid = '$titleId'";
				$pages = $this->getAll($filter);
				foreach($pages as $aPage)
				{
					$pageContents[$i] = $aPage[$field];
					$i++;
				}
			}
		}
		else if($allFilesLocation)
		{
			foreach($allFilesLocation as $fileLocation)
			{
				$file = $folder.'/'.$fileLocation;
				$pageContents[$i] = file_get_contents($file);
				$i++;
			}
		}
		else
		{
			return 'error';
		}

		return $pageContents;
	}

	function getImageNames($htmlPages, $packageType = '')
	{
		if($packageType == 'exe')
		{
			foreach($htmlPages as $page)
			{
				static $i=0;
				$regex = '';
				preg_match_all($regex, $page, $matches,	PREG_SET_ORDER);
				if($matches)
				{
					static $i= 0;
					foreach($matches as $match)
					{

						$resourcePaths[$i] = $match[0];
						$i++;
					}
				}
			}
		}
		else
		{
			if(count($htmlPages) == 1)
			{
				$regex = '/filename=.*type/';
				preg_match_all($regex, $htmlPages, $matches, PREG_SET_ORDER);
				if($matches)
				{
					static $i= 0;
					foreach($matches as $match)
					{
						$match[0] = preg_replace('/filename=/','',$match[0]);
						$match[0] = preg_replace('/&amp;type/','',$match[0]);
						$resourcePaths[$i] = $match[0];
						$i++;
					}
				}
			}
			else
			{
				foreach($htmlPages as $page)
				{
					$regex = '/filename=.*type/';
					static $i=0;
					preg_match_all($regex, $page, $matches,	PREG_SET_ORDER);
					if($matches)
					{
						static $i= 0;
						foreach($matches as $match)
						{
							$match[0] = preg_replace('/filename=/','',$match[0]);
							$match[0] = preg_replace('/&amp;type/','',$match[0]);
							$resourcePaths[$i] = $match[0];
							$i++;
						}
					}
				}
			}
		}

		return $resourcePaths;
	}

	function getResourceIds($htmlPages)
	{
		if(count($htmlPages) == 1)
		{
			preg_match_all('/fileinfo.*"/', $htmlPages, $matches, PREG_SET_ORDER);
			if($matches)
			{
				foreach($matches as $match)
				{
					$match = preg_replace('/fileinfo&amp;id=/','',$match);
					$resourceIds[$i] = $match;
					$i++;
				}
			}
		}
		else
		{
			foreach($htmlPages as $page)
			{
				preg_match_all('/fileinfo.*"/', $page, $matches, PREG_SET_ORDER);
				if($matches)
				{
					foreach($matches as $match)
					{
						$match = preg_replace('/fileinfo&amp;id=/','',$match);
						$resourceIds[$i] = $match;
						$i++;
					}
				}
			}
		}

		return $resourceIds;
	}

	function generateUniqueId($len = '')
	{
		$genkey = md5(uniqid(time(),true));
		if($len)
			$genkey = substr($genkey, 0, $len);

		return $genkey;
	}

  	/**
    	 * Method to replace image source links with links to the filemanager
	 *
    	 * @author Kevin Cyster
	 * @Modified by Jarrett L Jordaan
    	 * @param string $fileContents - the text of the page to operate on.
    	 * @param string $contextCode - course context code
    	 * @param array $fileNames - names of all files in package
    	 * @param array $imageIds - 
    	 * @param boolean/string $staticPackage - 
    	 * 
    	 * @return string $page - the finished modified text page
    	 * @return TRUE - if page is un-modified
	 *
	*/
    	function changeImageSRC($fileContents, $contextCode, $fileNames, $imageIds='', $staticPackage='')
	{
		// Image location on disc..
		$imageLocation =  'src="'.$this->objConf->getcontentBasePath().'content/';
		$imageLocation = $imageLocation.$contextCode.'/images/';
		// Image location on localhost..
		$action = 'src="'.$this->objConf->getsiteRoot().'index.php?module=filemanager&amp;action=file&amp;id=';
		// Image location in static package.
		$newLink = '"../'.$contextCode;
		if($staticPackage)
		{
			// Check if its Course Home Page.
			if($staticPackage == '2')
				$newLink = '"../'.$contextCode.'/'.$contextCode;
			foreach($fileNames as $fileName)
			{
				$regex = '/'.$fileName.'/';
				preg_match_all($regex, $fileContents, $matches, PREG_SET_ORDER);
				if($matches)
				{
					$newLink .= '/'.$fileName.'"';
					$regReplace = '/(".*'.$fileName.'.*?")/i';
					$page = preg_replace($regReplace, $newLink, $fileContents);
				}
				else
					$page = $fileContents;
			}
		}
		else
		{
			$page = $fileContents;
			// Only run through html's contained in package.
			foreach($fileNames as $aFile)
			{
				// Check if its an Image.
				if(preg_match("/.jpg|.gif|.png/",$aFile))
				{
					// Create new file source location.
					// Check if its a static package.
					if(!($static == ''))
						$newLink = $imageLocation.$aFile.'"';
					else
					{
						$newLink = $action.$imageIds[$aFile].'&amp;filename='.$aFile.'&amp;type=.jpg"';
					}
					// Convert filename into regular expression.
					$regex = '/'.$aFile.'/';
					// Find filename in html page if it exists.
					preg_match_all($regex, $page, $matches, PREG_SET_ORDER);
					if($matches)
					{
						$regReplace = '/(src=".*'.$aFile.'.*?")/i';
						$page = preg_replace($regReplace, $newLink, $page);
					}
					// If the image was renamed.
					else
					{
						$aFile = preg_replace("/.jpg|.gif|.png/","",$aFile);
						$regex = '/'.$aFile.'/';
						preg_match_all($regex, $fileContents, $matches, PREG_SET_ORDER);
						if($matches)
						{
							$regReplace = '/(src=".*'.$aFile.'.*?")/i';
							$page = preg_replace($regReplace, $newLink, $page);
						}
					}
				}
			}
		}

		return $page;
    	}

	/**
    	 * Method to replace source links with links to the blob system
	 *
    	 * @author Kevin Cyster
	 * @Modified by Jarrett L Jordaan
    	 * @param string $str - the text of the page to operate on.
    	 * @param string $contextCode - course context code
    	 * @param string $fileNames - names of all files in package
    	 * 
    	 * @return string $page - the finished modified text page
	 *
	*/
	function changeLinkUrl($fileContents, $contextCode, $fileNames, $pageIds, $packageType='', $allFilesLocation = '')
    	{
		$action =$this->objConf->getsiteRoot().'index.php?module=contextcontent&amp;action=viewpage&amp;id=';
		// Run through each resource.
		$page = $fileContents;
		if($packageType == 'mit')
		{
			foreach($allFilesLocation as $aFilepath)
			{
				$aFilepath = '../../../../'.$aFilepath;
				if(preg_match('/CourseHome/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['CourseHome.html'], $page);
				else if(preg_match('/Syllabus/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['Syllabus.html'], $page);
				else if(preg_match('/Calendar/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['Calendar.html'], $page);
				else if(preg_match('/Readings/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['Readings.html'], $page);
				else if(preg_match('/Labs/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['Labs.html'], $page);
				else if(preg_match('/LectureNotes/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['LectureNotes.html'], $page);
				else if(preg_match('/Assignments/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['Assignments.html'], $page);
				else if(preg_match('/Exams/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['Exams.html'], $page);
				else if(preg_match('/Projects/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['Projects.html'], $page);
				else if(preg_match('/Tools/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['Tools.html'], $page);
				else if(preg_match('/RelatedResources/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['RelatedResources.html'], $page);
				else if(preg_match('/DiscussionGroup/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['DiscussionGroup.html'], $page);
				else if(preg_match('/DownloadthisCourse/', $aFilepath))
					$page = preg_replace('%'.$aFilepath.'%', $action.$pageIds['DownloadthisCourse.html'], $page);
			}
		}
		else
		{
			foreach($fileNames as $aFile)
			{
				if($this->fileMod)
				{
					$aFile = preg_replace("/.html|.htm|.jpg|.gif|.png/","",$aFile);;
				}
				$regReplace = '/(href=".*'.$aFile.'.*?")/i';
				$modAction = 'href="'.$action.$pageIds[$aFile].'"';
				$page = preg_replace($regReplace, $modAction, $page);
			}
		}

		return $page;
    	}

	/**
    	 * Method to replace image source links with links to the blob system
	 *
    	 * @author Kevin Cyster
	 * @Modified by Jarrett L Jordaan
    	 * @param string $str - the text of the page to operate on.
    	 * @param string $contextCode - course context code
    	 * @param string $fileNames - names of all files in package
    	 * 
    	 * @return string $page - the finished modified text page
	 *
	*/
	function changeMITLinkUrl($fileContents, $contextCode, $fileNames, $pageIds, $static='')
    	{
		$action = 'href="'.$this->objConf->getsiteRoot().'/index.php?module=filemanager&action=fileinfo&amp;id=';
		// Run through each resource.
		$page = $fileContents;
		foreach($fileNames as $aFile)
		{
			if(!(preg_match("/.txt|.htm|.html|.xml|.css|.js|.jpg|.gif|.png/",$aFile)))
			{
				$regReplace = '/(href=".*'.$aFile.'.*?")/i';
				$modAction = $action.$pageIds[$aFile].'"';
				$page = preg_replace($regReplace, $modAction, $page);
			}
		}

		return $page;
    	}

	/**
    	 * Method to replace image source links with links to the blob system
	 *
    	 * @author Kevin Cyster
	 * @Modified by Jarrett L Jordaan
    	 * @param string $str - the text of the page to operate on.
    	 * @param string $contextCode - course context code
    	 * @param string $fileNames - names of all files in package
    	 * 
    	 * @return string $page - the finished modified text page
	 *
	*/
	function changeDataLink($fileContents, $contextCode, $fileNames, $pageIds, $static='')
    	{
		// Run through each resource.
		$page = $fileContents;
		foreach($fileNames as $aFile)
		{
			if(!(preg_match("/.txt|.htm|.html|.xml|.css|.js|.jpg|.gif|.png/",$aFile)))
			{
				$file = $this->objFiles->getFileInfo($pageIds[$aFile]);
				$filepath = $file['path'];
				$action = 'value"'.$this->objConf->getsiteRoot().$filepath;
				$regReplace = '/(value=".*'.$aFile.'.*?")/i';
				$modAction = $action.$pageIds[$aFile].'"';
				$page = preg_replace($regReplace, $modAction, $page);
				
				
			}
		}

		return $page;
    	}

	/**
	 * Write Schema files
	 *
	 * @param string $tempDirectory - location of temporary folder
	 * 
	 * @return TRUE - Successful execution
	 * 
	*/
	function writeSchemaFiles($tempDirectory)
	{
		// Additional site root path locations
		// Schema filenames
		$fileEdu = "eduCommonsv1.1.xsd";
		$fileImscp = "imscp_v1p2.xsd";
		$fileImsmd = "imsmd_v1p2p4.xsd";
		// Schama files locations
		$schamas = $siterootpath."core_modules/contextadmin/ims/";
		$schamaspath1 = $schamas."eduCommonsv1.1.xsd";
		$schamaspath2 = $schamas."imscp_v1p2.xsd";
		$schamaspath3 = $schamas."imsmd_v1p2p4.xsd";
		// Write files to new directory
		// Write Schema files
		$fp = fopen($tempDirectory."/".$fileEdu,'w');
		if(fwrite($fp, file_get_contents($schamaspath1)) === FALSE)
			return "writeError";
		$fp = fopen($tempDirectory."/".$fileImscp,'w');
		if(fwrite($fp, file_get_contents($schamaspath2)) === FALSE)
			return "writeError";
		$fp = fopen($tempDirectory."/".$fileImsmd,'w');
		if(fwrite($fp, file_get_contents($schamaspath3)) === FALSE)
			return "writeError";
		fclose($fp);
		if(!chmod($tempDirectory,0777))
			return "permissionError";

		return TRUE;
	}

	/**
	 * Adds a chapter to the course content
	 *
	 *
	*/
	function addChapters($contextCode, $chapterName, $intro)
	{
		$title = str_replace('_',' ',$chapterName);
		$visibility = 'Y';
		$chapterId = $this->objChapters->addChapter('', $title, $intro);
		$result = $this->objContextChapters->addChapterToContext($chapterId, $contextCode, $visibility);

		return $chapterId;
	}

	function chapterOrder($contextcode)
	{
		parent::init('tbl_contextcontent_chaptercontext');
		$filter = "WHERE contextcode = '$contextcode' ORDER by chapterorder";
		$chapterOrders = $this->getAll($filter);
		$i = 0;
		foreach($chapterOrders as $chapterOrder)
		{
			$indexdOrders[$i] = $chapterOrder;
			$i++;
		}

		return $indexdOrders;
	}
	function chapterContent($chapterid)
	{
		parent::init('tbl_contextcontent_chaptercontent');
		$filter = "WHERE chapterid = '$chapterid'";
		$chapterContents = $this->getAll($filter);
		$i = 0;
		foreach($chapterContents as $chapterContent)
		{
			$indexdOrders[$i] = $chapterContent;
			$i++;
		}

		return $indexdOrders;
	}
	function pageOrder($contextcode, $chapterId)
	{
		parent::init('tbl_contextcontent_order');
		$filter = "WHERE contextcode = '$contextcode' AND chapterid = '$chapterId' ORDER by bookmark";
		$pageOrders = $this->getAll($filter);
		$i = 0;
		foreach($pageOrders as $pageOrder)
		{
			$indexdOrders[$i] = $pageOrder;
			$i++;
		}

		return $indexdOrders;
	}

	function pageContent($titleId)
	{
		parent::init('tbl_contextcontent_pages');
		$filter = "WHERE titleid = '$titleId'";
		$pageContents = $this->getAll($filter);
		$i = 0;
		foreach($pageContents as $pageContent)
		{
			$indexdOrders[$i] = $pageContent;
			$i++;
		}

		return $indexdOrders['0'];
	}


	/**
	 * Sets debugging on
	 *
    	 * @param NULL
	 *
	*/
	function fileModOn()
	{
		$this->fileMod = TRUE;
	}

	/**
	 * Sets debugging off
	 *
    	 * @param NULL
	 *
	*/
	function fileModOff()
	{
		$this->fileMod = FALSE;
	}

	/**
	 * Main template file for the import module
	 * Displays data and forms
	 * Written by James Scoble using code written by Wesley Nitsckie
	 * Modified by Jarrett L. Jordaan
	 */
	public function importTemplate($dbData, $packageType, $newCourse)
	{
		$this->switchDatabase();
		// Load needed display classes.
		$this->loadClass('textinput', 'htmlelements');
		$this->loadClass('htmlheading','htmlelements');
		$this->loadClass('label', 'htmlelements');
		$this->loadClass('button', 'htmlelements');
		$this->loadClass('form', 'htmlelements');
		$this->loadClass('dropdown','htmlelements');
		$objH = new htmlheading();
		if($newCourse)
			$objH->str = $this->objLanguage->languageText("mod_contextadmin_selectCourse","contextadmin");
		else
			$objH->str = $this->objLanguage->languageText("mod_contextadmin_selectCourseHeading","contextadmin");
		$objH->type=2;
		if($packageType == 'mit' || $packageType == 'default')
			$objForm = new form('impfrm', $this->uri(array('action' => 'uploadIMSIntoExisting')));
		else
			$objForm = new form('impfrm', $this->uri(array('action' => 'uploadKNG')));
		// Label.
		$label = new label($this->objLanguage->languageText("mod_contextadmin_selectCourse","contextadmin"),"select");
		// Button.
		$inpButton =  new button('Import', $this->objLanguage->languageText("word_import"));
		$inpButton->setToSubmit();
		$objElement = new dropdown('dropdownchoice');
		// Dropdown .
		foreach($dbData as $dataOld)
			$objElement->addOption($dataOld['contextcode']);
		$objForm->addToForm($objH);
		$objForm->addToForm($label->show());
		$objForm->addToForm($objElement->show());
		$objForm->addToForm('<br/>');
		$objForm->addToForm($inpButton->show());

		return $objForm->show();
	}

	/**
	 * Main template file for the import module
	 * Displays data and forms
	 * Written by James Scoble using code written by Wesley Nitsckie
	 * Modified by Jarrett L. Jordaan
	 */
	public function uploadTemplate($section = '')
	{
		// Load needed display classes.
		$this->loadClass('textinput', 'htmlelements');
		$this->loadClass('radio','htmlelements');
		$this->loadClass('label', 'htmlelements');
		$this->loadClass('button', 'htmlelements');
		$this->loadClass('form', 'htmlelements');
		$this->loadClass('hiddeninput', 'htmlelements');
		$this->loadClass('checkbox', 'htmlelements');
		$this->loadClass('htmlheading', 'htmlelements');
		// Creating the form for IMS package upload.
		$paramArray1 = array('action' => 'uploadIMS');
		$form1 = new form('uploadziplocal', $this->uri($paramArray1,'contextadmin'));
    		$form1->extra=' enctype="multipart/form-data" ';
    		// File input.
    		$fileInput = new textinput('upload');
    		$fileInput->fldType='file';
    		$fileInput->label=$this->objLanguage->languageText("mod_contextadmin_uploadNotice","contextadmin");
    		$fileInput->size=50;
    		// Submit button.
    		$selectCourse = new button('submit');
    		$selectCourse->setToSubmit();
    		$selectCourse->setValue($this->objLanguage->languageText("word_upload"));
		// Package type.
		$pakRadio = new radio('packageType');
		$pakRadio->addOption('default','eduCommons');
		$pakRadio->addOption('mit','MIT');
		// $pakRadio->addOption('exe','eXe');
		$pakRadio->setSelected('default');
		$checkLabel = new label($this->objLanguage->languageText("mod_contextadmin_createCourse","contextadmin"),"");
		// Checkbox.
		$createCheckbox = new checkbox('createCourse','',true);
	    	// add the objects to the form
    		$form1->addToForm($fileInput);
    		$form1->addToForm('<br />');
    		$form1->addToForm($pakRadio);
    		$form1->addToForm('<br />');
    		$form1->addToForm($checkLabel);
    		$form1->addToForm(' ');
    		$form1->addToForm($createCheckbox);
    		$form1->addToForm('<br />');
    		$form1->addToForm($selectCourse);
		// Creating the form for package upload.
		$paramArray2 = array('action' => 'uploadFromServer');
		$form2 = new form('uploadziplocal', $this->uri($paramArray2,'contextadmin'));
    		$form2->extra=' enctype="multipart/form-data" ';
    		// Heading.
		$heading1 = new htmlheading();
    		$heading1->str=$this->objLanguage->languageText("mod_contextadmin_uploadserver","contextadmin");
    		$heading1->type=2;
		// Button.
		$loginButton = new button('login');
		$loginButton->setValue('Login');
		$loginButton->setToSubmit();
		// Label - server.
		$serverLabel = new label($this->objLanguage->languageText("mod_contextadmin_selectServer","contextadmin"),"");
		// Dropdown - server selection.
		$serverDropDown = new dropdown('server');
		// Populate Dropdown - server.
		$servers = $this->getServers();
		foreach($servers as $server)
		{
			$serverDropDown->addOption($server);
		}
		// Label - local server.
		$localLabel = new label($this->objLanguage->languageText("mod_contextadmin_localServer","contextadmin"),"");
    		// Server input.
    		$serverInput = new textinput($this->objLanguage->languageText("mod_contextadmin_localhost","contextadmin"), $this->objLanguage->languageText("mod_contextadmin_uploadInfo","contextadmin"),'','45');
		// Label - Create Course.
		$checkLabel2 = new label($this->objLanguage->languageText("mod_contextadmin_createCourse","contextadmin"),"");
		$form2->addToForm('<hr>');
		$form2->addToForm($heading1);
		$form2->addToForm('<br />');
		$form2->addToForm($serverLabel);
		$form2->addToForm($serverDropDown);
		$form2->addToForm('<br />');
		$form2->addToForm($loginButton);
		if($section == '1')
			return $form1->show();
		else if($section == '2')
			return $form2->show();
		else
			return $str = $form1->show().$form2->show();
	}

	/**
	 * Main template file for the export module
	 * Displays data and forms
	 *
	 */
	public function downloadTemplate()
	{
		// course data table.
		parent::init('tbl_context');
		// Load needed display classes.
		$this->loadClass('textinput', 'htmlelements');
		$this->loadClass('dropdown','htmlelements');
		$this->loadClass('label', 'htmlelements');
		$this->loadClass('button', 'htmlelements');
		$this->loadClass('hiddeninput', 'htmlelements');
		$this->loadClass('form', 'htmlelements');
		// Creating the form.
		$paramArray1 = array('action' => 'downloadChisimba');
		$form1 = new form('exportziplocal', $this->uri($paramArray1,'contextadmin'));
    		$form1->extra=' enctype="multipart/form-data" ';
		// Label - server.
		$courseLabel = new label($this->objLanguage->languageText("mod_contextadmin_selectCourse","contextadmin"),"");
		// Dropdown - course selection.
		$courseDropDown = new dropdown('dropdownchoice');
		// Retrieve all courses in Chisimba.
		$courses = $this->getAll();
		// Populate Dropdown course.
		foreach($courses as $course)
		{
			$courseDropDown->addOption($course['contextcode'], $course['title']);
		}
		// Button.
		$exportButton = new button('export');
		$exportButton->setValue($this->objLanguage->languageText("word_export"));
		$exportButton->setToSubmit();
		$form1->addToForm($courseLabel);
		$form1->addToForm($courseDropDown);
		$form1->addToForm("<br />");
		$form1->addToForm($exportButton);

		return $form1->show();
	}

/*
 ------------ lixlpixel recursive PHP functions -------------
 recursive_remove_directory( directory to delete, empty )
 expects path to directory and optional TRUE / FALSE to empty
 of course PHP has to have the rights to delete the directory
 you specify and all files and folders inside the directory
 ------------------------------------------------------------

 to use this function to totally remove a directory, write:
 recursive_remove_directory('path/to/directory/to/delete');

 to use this function to empty a directory, write:
 recursive_remove_directory('path/to/full_directory',TRUE);
*/
function recursive_remove_directory($directory, $empty=FALSE)
{
	// if the path has a slash at the end we remove it here.
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	// if the path is not valid or is not a directory.
	if(!file_exists($directory) || !is_dir($directory))
	{
		// we return false and exit the function.
		return FALSE;

	// if the path is not readable.
	}elseif(!is_readable($directory))
	{
		// we return false and exit the function.
		return FALSE;

	// else if the path is readable.
	}else{

		// we open the directory.
		$handle = opendir($directory);

		// and scan through the items inside.
		while (FALSE !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory.
			// or the parent directory.
			if($item != '.' && $item != '..')
			{
				// we build the new path to delete.
				$path = $directory.'/'.$item;

				// if the new path is a directory.
				if(is_dir($path)) 
				{
					// we call this function with the new path.
					$this->recursive_remove_directory($path);

				// if the new path is a file.
				}else{
					// we remove the file.
					unlink($path);
				}
			}
		}
		// close the directory.
		closedir($handle);

		// if the option to empty is not set to true.
		if($empty == FALSE)
		{
			// try to delete the now empty directory.
			if(!rmdir($directory))
			{
				// return false if not possible.
				return FALSE;
			}
		}
		// return success.
		return TRUE;
	}
}
}
?>
