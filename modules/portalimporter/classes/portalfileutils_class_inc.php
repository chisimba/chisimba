<?php
set_time_limit(0);
/**
 *
 * Class to recurse the UWC website and generate XML output
 *
 * This class will recurse the static website pages and generate XML that can be used
 * for the purpose of recreating the UWC portal content as XML or storing it in a 
 * database.  This is a quick and dirty project for a single use so I may not add 
 * comments or multilingual text. I have tried to write the code in a self-documenting
 * kind of way.
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
 * @package   portaltools
 * @author    dkeats@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: portalfileutils_class_inc.php 11929 2008-12-29 21:15:36Z charlvn $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check


/**
*
* Database accesss class for Chisimba for the module _MODULECODE
*
* @author Derek Keats
* @package portalimport
*
*/
class portalfileutils extends object
{
	public $dirs=array();
	public $files=array();
	public $sCOnfig;
	public $startDir;
	public $xmlPath;
	public $xmlFile;
	public $xmlOut;
	public $fullTitle;
	public $pageTitle;
	public $objRegex;
	public $objConfig;
	public $objStdlib;
	public $objLanguage;

	/**
    *
    * Intialiser for the portalimporter module
    * @access public
    *
    */
	public function init()
	{
		$this->dbLog = $this->getObject('dblog', 'portalimporter');
		$this->sConfig = $this->getObject('dbsysconfig', 'sysconfig');
		$this->startDir = $this->sConfig->getValue('mod_portalimporter_sitepath', 'portalimporter');
		$this->contentStart = $this->sConfig->getValue('mod_portalimporter_contentstart', 'portalimporter');
		$this->contentEnd = $this->sConfig->getValue('mod_portalimporter_contentend', 'portalimporter');
		$this->xmlPath = $this->sConfig->getValue('mod_portalimporter_xmlpath', 'portalimporter');
		$this->xmlFile = $this->sConfig->getValue('mod_portalimporter_xmlfile', 'portalimporter');
		$this->xmlOut = $this->xmlPath . "/" . $this->xmlFile;
		$this->objUser = $this->getObject("user", "security");
		$this->objStdlib = $this->getObject('splstdlib', 'files');
		$this->objCmsDb = $this->getObject('dbcmsadmin', 'cmsadmin');
		$this->objRegex = $this->getObject('regexes','utilities');
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objLanguage = $this->getObject("language", "language");
	}



	/**
    * 
    * Method to read a path, and create an array of all the directories and all
    * the files in it.
    * 
    * @param String $path The path. Use "start" to start from the configured path
    * @param Integer $level The level to work from (normally 1)
    * @param Integer $last The last level to work from (normally 4 to go 4 directories deep)
    * @param String array $dirs The array of directories
    * @param String array $files
    * 
    * @return boolean TRUE|FALSE depending on whether there are any files or not
    * @access public
    *  
    */
	public function readPath($path, $level,$last,&$dirs,&$files) {
		if ($path == "start") {
			$path=$this->startDir;
		}
		$dp=opendir($path);
		while (false!=($file=readdir($dp)) && $level <= $last){
			if ($file!="." && $file!="..")
			{
				if (is_dir($path."/".$file))
				{
					$this->readPath($path."/".$file,$level+1,$last,$dirs,$files); // uses recursion
					$dirs[] = "$path/$file";  // reads the dir into an array
				} else {
					$files[] = "$path/$file"; // reads the file into an array
				}
			}
		}
		$this->dirs=$dirs;
		$this->files=$files;
		if (!empty($this->dirs) || !empty($this->files)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	/**
    *
    * A method to simply list all the directories found using the readPath method
    *
    *   @return string HTML formatted line for each directory or NULL if no directories found
    *   @access Public
    * 
    */
	public function showDirs()
	{
		if (!empty($this->dirs)) {
			$ret="";
			$okCount=0;
			$dudCount=0;
			foreach ($this->dirs as $directory) {
				$count++;
				if (!$this->isExcludeFile($directory)) {
					$ret .= $this->getPortalPath($directory) . "<font color=\"green\"> OK</font><br />";
					$okCount++;
				} else {
					$ret .= $this->getPortalPath($directory) . "<font color=\"red\"> DUD</font><br />";
					$dudCount++;
				}
			}
			$ret = "<h1>Listing directories</h1>"
			. "<br /><h2><font color='green'>Content directories: $okCount</font>"
			. "<br /><font color='red'>Dud directories: $dudCount</font>"
			. "<br /><font color='purple'>Total directories parsed: $count</font></h2>"
			. $ret;
			return $ret;
		} else {
			return NULL;
		}
	}

	/**
    *
    * A method to simply list all the files found using the readPath method
    *
    *   @return string HTML formatted line for each file or NULL if no files found
    *   @access Public
    * 
    */
	public function showFiles()
	{
		if (!empty($this->files)) {
			$ret="";
			$count=0;
			foreach ($this->files as $file) {
				$count++;
				$ret .= $file . "<font color=\"green\"> OK</font><br />";
			}
			return $ret;
		} else {
			return NULL;
		}
	}

	/**
    * A method to list files along with information as to whether they
    * contain a content delimiter or are legacy plain HTML pages. It also checks
    * for dud pages by comma delimited list of page name contents stored
    * in the sysconfig for this module.
    * 
    * @access public
    * @return String A list of files
    *  
    */
	public function listFilesWithDelimiters()
	{
		$ret= "";
		if (!empty($this->files)) {
			$count=0;
			$strCount;
			$legacyCount=0;
			$dudFiles=0;
			$sPattern = "/$this->contentStart(.*)$this->contentEnd/iseU";
			$portalUrl = $this->sConfig->getValue('mod_portalimporter_staticUrl', 'portalimporter');
			foreach ($this->files as $filename) {
				$count++;
				$extension = $this->fileExtension($filename);
				$lcExt = strtolower($extension);
				if ($lcExt == "htm" || $lcExt == "html") {
					$contents = file_get_contents($filename);
					if (!$this->isExcludeFile($filename)) {
						if (preg_match($sPattern, $contents, $elems)) {
							if (!$this->hideStructured==TRUE) {
								$ret .= $count. ". " . $filename . " <font color='green'>STRUCTURED PAGE</font>"
								. "&nbsp;&nbsp;&nbsp;[<a href=\"" . $portalUrl . $this->getPortalPath($filename) . "\">View</a>]<br />";
							}
							$strCount++;
						} else {
							if (!$this->hideLegacy==TRUE) {
								$ret .= $count. ". " . $filename . " <font color='red'>LEGACY PAGE</font>"
								. "&nbsp;&nbsp;&nbsp;[<a href=\"" . $portalUrl . $this->getPortalPath($filename) . "\">View</a>]<br />";
							}
							$legacyCount++;
						}
					} else {
						if (!$this->hideDuds==TRUE) {
							$ret .= $count. ". " . $filename . " <font color='orange'>DUD PAGE</font><br />"
							. "&nbsp;&nbsp;&nbsp;[<a href=\"" . $portalUrl . $this->getPortalPath($filename) . "\">View</a>]<br />";
						}
						$dudFiles++;
					}
				}
			}
			$totalHtml = $legacyCount + $strCount + $dudFiles;
			$ret = "<h1>Listing files per structured or legacy content</h1>"
			. "<br /><h2><font color='green'>Structured pages: $strCount</font>"
			. "<br /><font color='red'>Legacy pages: $legacyCount</font>"
			. "<br /><font color='orange'>Dud pages: $dudFiles</font>"
			. "<br /><font color='blue'>Total HTML pages: $totalHtml</font>"
			. "<br /><font color='purple'>Total files parsed: $count</font></h2>"
			. $ret;
		}
		return $ret;
	}

	/**
    * A method to write out the data to an XML file
    * @access  Public
    * @return String "Finished" on completion
    * 
    */
	public function xmlToFile()
	{
		$fh = fopen($this->xmlOut, 'w')  or die("Cannot open file for writing");
		fwrite($fh, $this->xmlHeader());
		fclose($fh);
		$this->showFilesAsXML("file");
		return "Finished";
	}

	/**
    * 
    * Method to return an XML header
    * @access public
    * @return String A standard XML header together with a <document> root tag
    * 
    */
	public function xmlHeader()
	{
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n<document>\n\n";
	}

	/**
    * 
    * Method to return a closing &lt;document&gt; tag
    * @return String CLosing document tag
    * 
    */
	public function closeXml()
	{
		return "\n\n</document>\n\n";
	}

	/**
    * 
    * MEthod to store the page data in the database. This is the core piece. 
    * @access public
    * @return "All done" when finished
    * 
    */
	public function storeData()
	{
		$db = $this->getObject("dbcontent","portalimporter");
		foreach ($this->files as $filename) {
			// Check for exclusion strings in filename
			if (!$this->isExcludeFile($filename)) {
				$count++;
				$extension = $this->fileExtension($filename);
				$lcExt = strtolower($extension);
				if ($lcExt == "htm" || $lcExt == "html") {
					$filepath = $filename;
					$filetype=$extension;
					$portalPath = $this->getPortalPath($filename);
					$portal = $this->getLevel($portalPath, "portal");
					$section =$this->getLevel($portalPath, "section");
					$subportal = $this->getLevel($portalPath, "subportal");
					$page = $this->getLevel($portalPath, "page");

					$this->getContent($filename, "database");

					$ar = array(
					'filepath' => $filepath,
					'filetype' => $filetype,
					'portalpath' => $portalPath,
					'portal' => $portal,
					'section' => $section,
					'subportal' => $subportal,
					'page' => $page,
					'pagetitle' => $this->pageTitle,
					'structuredcontent' => $this->contentStructured,
					'rawcontent' => $this->contentRaw
					);
					$db->insert($ar);
				}
			}
		}
		return "<h1>All done: $count files processed</h1>";
	}

	/**
    * 
    * Method to get the Portal|Section|Subportal|Page information from
    * a file path.
    * 
    * @access public
    * @param String $portalPath The path to the file on the original website
    * @param $level THe level in the above hierarchy that we are working
    * @return The names of each level to store in data
    * 
    */
	public function getLevel($portalPath, $level)
	{
		$pStr = explode("/", $portalPath);
		$items = count($pStr);
		if ($items == 1 || empty($pStr)) {
			$portal = "/";
			$section = "";
			$subportal ="";
			$page = $pStr[0];
		}
		if ($items == 2 || empty($pStr)) {
			$portal = $pStr[0];
			$section = "";
			$subportal ="";
			$page = $pStr[1];
		}
		if ($items == 3 || empty($pStr)) {
			$portal = $pStr[0];
			$section = $pStr[1];
			$subportal ="";
			$page = $pStr[2];
		}
		if ($items == 4 || empty($pStr)) {
			$portal = $pStr[0];
			$section = $pStr[1];
			$subportal =$pStr[2];
			$page = $pStr[3];
		}
		switch($level) {
			case "portal":
				return $portal;
				break;
			case "section":
				return $section;
				break;
			case "subportal":
				return $subportal;
				break;
			case "page":
				return $page;
			default:
				return "";
				break;
		}
	}


	/**
    * 
    * Method to show the files as pseudo XML in the web browser or formatted
    * for writing to a file.
    * 
    * @access public
    * @param String $outPutMethod WHere the data are being written screen | file
    * @return String The formatted output or NULL
    *  
    */
	public function showFilesAsXML($outPutMethod="screen")
	{
		if (!empty($this->files)) {
			$ret="";
			$count=0;
			if ($outPutMethod == "file") {
				$fh = fopen($this->xmlOut, 'a')  or die("Cannot open file for writing");
			}
			foreach ($this->files as $filename) {
				// Check for exclusion strings in filename
				if (!$this->isExcludeFile($filename)) {
					$count++;
					$extension = $this->fileExtension($filename);
					$portalPath = $this->getPortalPath($filename);
					$lcExt = strtolower($extension);
					$ret .= "<file counter=\"" . $count . "\">\n";
					$ret .= "<filepath>" . $filename . "</filepath>\n";
					$ret .= "<filetype>" . $extension . "</filetype>\n";
					$ret .= "<depth>" . $this->getLevels($filename) . "</depth>\n";
					$ret .= "<portalpath>" . $portalPath . "</portalpath>\n";
					$ret .= $this->getPortalStructure($portalPath);
					if ($lcExt == "htm" || $lcExt == "html") {
						$ret .= "<content>\n\n" . $this->getContent($filename) . "\n</content>\n\n";
					} else {
						$ret .= "<content />\n";
					}
					$ret .= "</file>\n\n\n";
					if ($outPutMethod == "file") {
						fwrite($fh, $ret);
						$ret="";
					}
				}
			}
			if ($outPutMethod == "file") {
				fwrite($fh, $this->closeXml());
				fclose($fh);
			}
			return $ret;
		} else {
			return NULL;
		}
	}

	/**
    * 
    * A simple method to detect MS Word crap in a page
    * @access public
    * @return String A count and list of the files with word crud based on the MSO tag.
    *  
    */
	public function detectWordCrap()
	{
		$pattern = "/MSO-(.*)>/iseU";
		$count=0;
		$ret = "";
		$crapCount=0;
		$noCrapCount=0;
		foreach ($this->files as $filename) {
			// Check for exclusion strings in filename
			if (!$this->isExcludeFile($filename)) {
				$extension = $this->fileExtension($filename);
				$lcExt = strtolower($extension);
				$count++;
				if ($lcExt == "htm" || $lcExt == "html") {
					$contents = file_get_contents($filename);
					if (preg_match($pattern, $contents, $elems)) {
						$crapCount++;
						$ret .= $this->getPortalPath($filename) . "<br />";
					} else {
						$noCrapCount++;
					}
				}
			}
		}
		$totalHtml = $crapCount + $noCrapCount;
		$ret = "<h1>Listing files per structured or legacy content</h1>"
		. "<br /><h2><font color='green'>Without word crud: $noCrapCount</font>"
		. "<br /><font color='red'>With word crud: $crapCount</font>"
		. "<br /><font color='blue'>Total HTML pages: $totalHtml</font>"
		. "<br /><font color='purple'>Total files parsed: $count</font></h2>"
		. $ret;
		return $ret;
	}

	/**
    * 
    * A method to fetch the content of a page
    * @access public
    * @param String $filename The full path to the file
    * @param String $outStyle A string to determine whether to output XML tags or not
    * @return String The content extracted from the page by body or structure tag
    * 
    */
	public function getContent($filename, $outStyle="xml")
	{
		$contents = file_get_contents($filename);
		//@todo
		// Check for exclusion strings in contents
		if (!$this->isExcludeContents($contents)) {
			$sPattern = "/$this->contentStart(.*)$this->contentEnd/iseU";
			// Set it up to generate XML if required
			if ($outStyle=="xml") {
				$strOpen = "<useraw>FALSE</useraw>\n<structuredcontent>";
				$strClose = "</structuredcontent>\n<rawcontent />\n";
				$rawOpen = "<useraw>TRUE</useraw>\n<structuredcontent />\n<rawcontent>";
				$rawClose = "</rawcontent>\n";
			} else {
				$strOpen = "";
				$strClose = "";
				$rawOpen = "";
				$rawClose = "";
			}
			if (preg_match($sPattern, $contents, $elems)) {
				$ret = $strOpen
				. $this->resetImages($this->getContentStructured($contents))
				. $strClose;
				if ($outStyle=="xml") {
					return $ret;
				} else {
					$this->contentStructured = $ret;
					$this->contentRaw="";
					//die("<h1>FOUND</h1>" . htmlentities($ret));
					return TRUE;
				}
			} else {
				$ret = $rawOpen
				. $this->resetImages($this->getBody($contents))
				. $rawClose;
				if ($outStyle=="xml") {
					return $ret;
				} else {
					$this->contentStructured = "";
					$this->contentRaw=$ret;
					return TRUE;
				}
			}
			//Get the title for the menu
			$this->pageTitle = $this->extractTitle($this->fullTitle, $filename);
		} else {
			return FALSE;
		}

	}

	/**
     * 
     * Method to extract the body of a page
     * @param String $contents All the HTML from which to extract the body
     * @return String The contents of what is between the &lt;body&gt; and &lt;/body&gt; tags
     * 
     */
	public function getBody(& $contents)
	{
		$contents = $this->unCrapify($contents);
		if (preg_match("/<body.*>(.*)<\/body>/iseU", $contents, $elems)) {
			$page = $elems[1];
			//$page = $this->unCrapify($page);
		} else {
			$page = "No data in page";
		}
		return $page;
	}

	/**
    * Method to extract the content from the page by the content delimiter
    * of structured content. The delimiter is a config parameter.
    * 
    * @access public
    * @param String $contents The full contents of the page
    * @return String The extracted content
    *  
    */
	public function getContentStructured(& $contents)
	{
		//Get the title
		$ptPattern = "/<title>(.*)<\/title>iseU/";
		if (preg_match($ptPattern, $contents, $tits)) {
			$pageTitle = $tits[1];
		}
		$this->fullTitle = $pageTitle;
		$contents = $this->unCrapify($contents);
		$sPattern = "/$this->contentStart(.*)$this->contentEnd/iseU";
		if (preg_match($sPattern, $contents, $elems)) {
			$page = $elems[1];
		} else {
			$page = "No data in page";
		}
		return $page;
	}

	/**
    *
    * Method to extract the title from the text already pulled out of the TITLE 
    * tag or build it from the file name
    *
    * @param String $titleText The text of the page title
    * @param String $
    */
	public function extractTitle($titleTxt)
	{
		$titleAr = explode(":", $titleTxt);
		if (!empty($titleAr)) {
			// Get the title from the bit after the :
			$this->pageTitle = $titleAr[1];
		} else {
			// Get the title from the filename
			$titleAr = explode("/", $titleTxt);
			$x = count($titleAr)-1;
			$filePart = $titleAr[$x];
			$titAr = explode(".", filePart);
			$x = count($filePart) - 1;
			$ret = $x;
		}
	}

	public function getLevels($filename)
	{
		$fTmp = str_replace(START_DIR . "/", "", $filename);
		$pStr = explode("/", $fTmp);
		return count($pStr) - 1;
	}

	public function getPortalPath($filename)
	{
		return str_replace($this->startDir . "/", "", $filename);
	}

	public function getPortalStructure($portalPath)
	{
		$pStr = explode("/", $portalPath);
		$items = count($pStr);
		if ($items == 1 || empty($pStr)) {
			$portal = "<portal>/</portal>\n";
			$section = "<section />\n";
			$subportal ="<subportal />\n";
		}
		if ($items == 2 || empty($pStr)) {
			$portal = "<portal>" . $pStr[0] ."</portal>\n";
			$section = "<section />\n";
			$subportal ="<subportal />\n";
		}
		if ($items == 3 || empty($pStr)) {
			$portal = "<portal>" . $pStr[0] ."</portal>\n";
			$section = "<section>" . $pStr[1] . "</section>\n";
			$subportal ="<subportal />\n";
		}
		if ($items == 4 || empty($pStr)) {
			$portal = "<portal>" . $pStr[0] ."</portal>\n";
			$section = "<section>" . $pStr[1] . "</section>\n";
			$subportal ="<subportal>" . $pStr[2] . "</subportal>\n";
		}
		return $portal . $section . $subportal;
	}

	//------------MOVE file methods ---- put in a different class

	/**
    * 
    * Method to move any image assets to a repository. It doesn't move
    * the image, just makes a copy of it.
    * 
    * @access Public
    * 
    */
	public function moveImagesToRepository()
	{
		$successes = 0;
		$failures = 0;
		$count=0;
		$str = "";
		$failedFiles="";
		foreach ($this->files as $filename) {
			if (!$this->isExcludeFile($filename)) {
				$extension = $this->fileExtension($filename);
				$portalPath = $this->getPortalPath($filename);
				$lcExt = strtolower($extension);
				if ($this->isImage($lcExt)) {
					$count++;
					$source = $filename;
					$destination = $this->getDestination("image", $portalPath);
					//echo $source . "-------->" . $destination . "<br />";
					if (copy($source, $destination)) {
						$successes++;
					} else {
						$failures ++;
						$failedFiles .= "Failed to copy <em>$source</em> to the destination of <em>$destination";
					}
				}
			}
		}
		$str .= "<br /><br />Image files located: $count<br />"
		. "Succeeded: $successes<br />"
		. "Failed: $failures<br />";
		if ($failures > 0) {
			$str .= "List of failed copies: "
			. $failedFiles;
		}
		return $str;
	}

	/**
    * 
    * Method to get the destination for a file by file type
    * @access public
    * @param String $ext The extension to test
    * @return String The file destination full path
    *   
    */
	private function getDestination($assetType, & $portalPath)
	{
		$assetBase = $this->sConfig->getValue('mod_portalimporter_repository', 'portalimporter');
		$assetBase .= $assetType;
		//Create the asset base for this asset type if it does not exist
		if (!file_exists($assetBase)) {
			mkdir($assetBase, 0777, TRUE);
		}
		$pStr = explode("/", $portalPath);
		//Pop off the file name from the array
		array_pop($pStr);
		$curPath = $assetBase;
		foreach ($pStr as $directory) {
			$curPath .= "/" . $directory;
			//echo $curPath . "<br />";
			if (!file_exists($curPath)) {
				mkdir($curPath, 0777, TRUE);
			}
		}
		$ret = $assetBase . "/" . $portalPath;
		//Cater for it being entered as directory/ and directory
		$ret = str_replace("//", "/", $ret);
		return $ret;
	}

	/**
    * 
    * Method to determine if a file extension is an image or not
    * @access public
    * @param String $ext The extension to test
    *   
    */
	public function isImage($ext)
	{
		$images=array("jpg", "jpeg", "png", "gif", "svg");
		if (in_array($ext, $images)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
    * 
    * Method to reset the image tags in the database so that they
    * point to the new image location wich is used to update 
    * image tags in imported content so that they point to the 
    * repository.
    * 
    * @access public
    * @return String The URL path to images
    * 
    */
	public function resetImages($contents)
	{
		if ($this->imageBaseUrl=="") {
			$this->imageBaseUrl = $this->sConfig->getValue('mod_portalimporter_imageurl', 'portalimporter');
		}
		$pattern="/<img.*src=['\"](.*)['\"].*>/iseU";
		if (preg_match_all($pattern, $contents, $matches)) {
			$str = "";
			foreach ($matches[1] as $matched) {
				$newLink = $this->imageBaseUrl . "/" . $matched;
				$contents = str_ireplace($matched, $newLink, $contents);
			}
			return $contents;
		} else {
			return $contents;
		}
	}

	//----------------- END MOVE file ------------------------------

	public function fileExtension($filename)
	{
		$pathInfo = pathinfo($filename);
		return $pathInfo['extension'];
	}


	function getSize($path=NULL)
	{
		if ($path==NULL) {
			$path = $this->startDir;
		}
		if(!is_dir($path))return filesize($path);
		$dir = opendir($path);
		while($file = readdir($dir)) {
			if(is_file($path."/".$file))$size+=filesize($path."/".$file);
			if(is_dir($path."/".$file) && $file!="." && $file !="..")$size +=$this->getSize($path."/".$file);
		}
		return $size;
	}

	/**
    *
    * Method uses Tidy to clearn up word and other crap.
    * It must be called before picking out the content of the page because
    * otherwise, Tidy will make it back into a full page with headers and
    * body tags.
    *
    * @access public
    * @param string $content The string with the HTML to clean (before extracting content)
    * @return string The cleaned content
    *
    */
	public function unCrapify(&$content, $options = NULL)
	{
		if ($options == NULL){
			$options = array(
				"clean" => true,
				"indent" => true,
				"indent-spaces" => 4,
				"drop-proprietary-attributes" => true,
				"drop-empty-paras" => true,
				"word-2000" => true,
				"xhtml-output" => true,
			);
		}
		if (function_exists(tidy_parse_string)) {
			$tidy = tidy_parse_string($content, $options);
			tidy_clean_repair($tidy);
			return $tidy;
		} else {
			die ("TIDY is not available");
		}
	}

	/**
    *
    * A method to determin from a comma delimited list of patterns stored in the
    * config parameter mod_portalimporter_excludenames whether a file should be included
    * or not.
    *
    * @access private
    * @param string $filename The filename to check, normally including directory paths
    * @return boolean TRUE|FALSE
    *
    */
	private function isExcludeFile(& $filename)
	{
		$ret = FALSE;
		$ar = array();
		$exFiles = $this->sConfig->getValue('mod_portalimporter_excludenames', 'portalimporter');
		$ar = explode(",", $exFiles);
		if (!empty($ar)) {
			foreach ($ar as $pattern) {
				//If the pattern is in the file name return FALSE
				if(stristr($filename, $pattern) !== FALSE) {
					$ret=TRUE;
					//echo $filename . " contains $pattern at " . stristr($filename, $pattern) . "<br >";
				}
			}
			return $ret;
		}
	}

	///---------needs finishing------------------------------------------------------not used but should be-----------------------
	private function isExcludeContents(& $contents)
	{
		$ret = FALSE;
		$ar = array();
		$exCt = $this->sConfig->getValue('mod_portalimporter_excludestrings', 'portalimporter');
		$ar = explode(",", $exCt);
		if (!empty($ar)) {
			foreach ($ar as $pattern) {
				echo $pattern . "<br />";
				//If the pattern is in the file name return FALSE
				//if(stristr($contents, $pattern) !== FALSE) {
				//   $ret=TRUE;
				// }
			}
		}
		return $ret;
	}


	/**
	*   This function will iterate through the static_content and
	*   format the pages for use with the portal importer
	*
	*/
	public function fixDataRecurse($subsections)
	{
		//echo $secname; die();
		$subsection[] = $this->objStdlib->dirFilterDots($subsections);
		$subsection = array_filter($subsection);
		//var_dump($subsection);
		// add each subsection to this section now
		if(isset($subsection[0]))
		{
			$subsection = $subsection[0];
			foreach($subsection as $subsect)
			{
				$ssecname = end(explode('/', $subsect));
				$ssecname = ucwords(str_replace("_", " ", $ssecname));
				log_debug("Adding $ssecname as a subsection to $secname with ID $secid");
				$csecarr = array(
				'parentselected' => $secid,
				'title' => $ssecname,
				'menutext' => $ssecname,
				'access' => 0,
				'layout' => 'page',
				'description' => '',
				'published' => 1,
				'hidetitle' => 0,
				'showdate' => 1,
				'showintroduction' => 1,
				'ordertype' => 'pageorder',
				'userid' => $this->objUser->userId(),
				'pagenum' => 0,
				'imagesrc' => NULL,
				'contextcode' => NULL,
				);
				$csecid = $this->objCmsDb->addSection($csecarr);
				// ok subsection is created, now lets add the pages...
				$subsect = $subsect."/";
				// we should look through the dirs available for assets and move them to the repo as well now.
				// now do the pages
				$this->doPages($subsect, $csecid, $ssecname);
			}
		}
		else {
		    return;
		}
	}



	public function doSection($subsections, $secname = NULL)
	{
		// create the section
		$secname = end(explode('/', $subsections));
		$sectionname = $secname;
		$secname = ucwords(str_replace("_", " ", $secname));
		//echo "<h1>Section name : $secname</h1><br />";
		// this is a parent section, so no section id (0)
		$psecarr = array(
		'parentselected' => 0,
		'title' => $secname,
		'menutext' => $secname,
		'access' => '',
		'layout' => 'page',
		'description' => '',
		'published' => 1,
		'hidetitle' => 0,
		'showdate' => 1,
		'showintroduction' => 1,
		'ordertype' => 'pageorder',
		'userid' => $this->objUser->userId(),
		'pagenum' => 0,
		'imagesrc' => NULL,
		'contextcode' => NULL,
		);

		$secid = $this->objCmsDb->addSection($psecarr);
		// add the top level page (welcome page)
		$this->addWelcomePage($subsections, $secid);
		// return some info
		return array('secid' => $secid, 'secname' => $secname);
	}

	public function doSubSections($subsections, $secname, $secid)
	{
		//echo $secname; die();
		$subsection[] = $this->objStdlib->dirFilterDots($subsections);
		$subsection = array_filter($subsection);
		//var_dump($subsection);
		// add each subsection to this section now
		if(isset($subsection[0]))
		{
			$subsection = $subsection[0];
			foreach($subsection as $subsect)
			{
				$ssecname = end(explode('/', $subsect));
				$ssecname = ucwords(str_replace("_", " ", $ssecname));
				log_debug("Adding $ssecname as a subsection to $secname with ID $secid");
				$csecarr = array(
				'parentselected' => $secid,
				'title' => $ssecname,
				'menutext' => $ssecname,
				'access' => 0,
				'layout' => 'page',
				'description' => '',
				'published' => 1,
				'hidetitle' => 0,
				'showdate' => 1,
				'showintroduction' => 1,
				'ordertype' => 'pageorder',
				'userid' => $this->objUser->userId(),
				'pagenum' => 0,
				'imagesrc' => NULL,
				'contextcode' => NULL,
				);
				$csecid = $this->objCmsDb->addSection($csecarr);
				// ok subsection is created, now lets add the pages...
				$subsect = $subsect."/";
				// we should look through the dirs available for assets and move them to the repo as well now.
				// now do the pages
				$this->doPages($subsect, $csecid, $ssecname);
			}
		}
		else {
		    return;
		}
	}

	public function doPages($subsect, $csecid, $secname)
	{

		if(!file_exists($this->objConfig->getSiteRootPath()."usrfiles/importcms"))
		{
			mkdir($this->objConfig->getSiteRootPath()."usrfiles/importcms/", 0777);
		}
		if(!file_exists($this->objConfig->getSiteRootPath()."usrfiles/importcms/".$csecid))
		{
			mkdir($this->objConfig->getSiteRootPath()."usrfiles/importcms/".$csecid, 0777);
		}
		$dirs = $this->objStdlib->dirFilterDots($subsect);
		if(!empty($dirs))
		{
			foreach($dirs as $dir)
			{
				$ndir = explode("/",$dir);
				$ndir = end($ndir);
				mkdir($this->objConfig->getSiteRootPath()."usrfiles/importcms/".$csecid."/".$ndir, 0777);
				$filesindir = new DirectoryIterator($dir);
				foreach($filesindir as $movables)
				{
					if(!is_dir($dir."/".$movables))
					{
						copy($dir."/".$movables, $this->objConfig->getSiteRootPath()."usrfiles/importcms/".$csecid."/".$movables);
					}
				}
			}
		}
		$pages[] = $this->objStdlib->fileLister($subsect);
		$pages = array_filter($pages);
		if(!empty($pages))
		{
			foreach ($pages as $pageobj)
			{
				foreach($pageobj as $page)
				{
					//echo "<h1>$subsect</h1>";
					// ok now we need to do some magic on the pages.
					$contents = file_get_contents($subsect.$page);
					
					$content_path = $subsect.$page;
					// grok the title
					//preg_match_all('/\<title>(.*)\<\/title\>/U', $contents, $tresults, PREG_PATTERN_ORDER);
					$title = $this->objRegex->get_doc_title($contents);

					//$title = $tresults[1][0];
					$title = explode("--", $title);

					$must_add_title = false;

					if(isset($title[1]))
					{
						//Title will consist of all other information delimited by --
						/*
						$total = count($title);
						for ($i = 1;$i < $total; $i++){
						    $title_str .= $this->str_first_upper($this->strip_ext(trim($title[$i])), '--').' ';
						}	
						$title = $title_str;
						*/

						//Compensating for known problem titles
						if (preg_match('/.*student.*src.*/i', $content_path)
							|| preg_match('/.*student.*sds.*/i', $content_path)
							|| preg_match('/.*student.*postgrad.*/i', $content_path)
							|| preg_match('/.*student.*international.*/i', $content_path)
							|| preg_match('/.*student.*parttime.*/i', $content_path)
							|| preg_match('/.*student.*undergrad.*/i', $content_path)
							|| preg_match('/.*faculty.*project.*focus.*/i', $content_path)
							|| preg_match('/.*faculty.*project.*essru.*/i', $content_path)
							|| preg_match('/.*faculty.*project.*pet.*/i', $content_path)
							|| preg_match('/.*faculty.*project.*envirofacts.*/i', $content_path)
							|| preg_match('/.*faculty.*project.*hypnea.*/i', $content_path)
							|| preg_match('/.*faculty.*law.*/i', $content_path)
							|| preg_match('/.*faculty.*economic.*/i', $content_path)
							|| preg_match('/.*faculty.*center.*cshe.*/i', $content_path)
							|| preg_match('/.*faculty.*school.*/i', $content_path)
							|| preg_match('/.*faculty.*departments.*/i', $content_path)
							|| preg_match('/.*faculty.*unit.*avoir.*/i', $content_path)
							|| preg_match('/.*faculty.*unit.*gender.*/i', $content_path)
							){
								$title = end($title);
							} else {
								$title = $title[1];
							}
		

						$must_add_title = true;
					}
					else {
						$title = $this->objLanguage->languageText("mod_portalimporter_missingpgtitle", "portalimporter");
						$must_add_title = false;
					}
					
					//if($title = '')
					//{
					//	$title = 'Title Missing';
					//}
					// grab the content body
					preg_match_all('/<!--CONTENT_BEGIN-->(.*)<!--CONTENT_END-->/iseU', $contents, $bresults, PREG_PATTERN_ORDER);

					$must_add_content = false;

					if(isset($bresults[1][0]))
					{
						$body = $bresults[1][0];	
						$must_add_content = true;
					}
					else {
						//$body = $this->objLanguage->languageText("mod_portalimporter_missingtagsinbody", "portalimporter");
						//continue;
						$must_add_content = false;
					}
					// change the links in the pages to the assets to point to the correct ones.
					
					// change the anchors as well?
					
					// clean up the bad code
					$options = array(
					"clean" => true,
					"indent" => true,
					"indent-spaces" => 4,
					"drop-proprietary-attributes" => true,
					"drop-empty-paras" => true,
					"word-2000" => true,
					"quote-ampersand" => true,
					"lower-literals" => true,
					"show-body-only" => true,
					);

					if (function_exists('tidy_parse_string')) {
						$tidy = new tidy;
						$tidy->parseString($body, $options, 'utf8');
						$tidy->cleanRepair();
					} else {
						die ("TIDY is not available");
					}
					$body = $tidy;



					$imgs = $this->objRegex->get_images($body);
					$images = $imgs[3];
					//var_dump($imgs);
					$counter = 0;
					foreach($images as $img)
					{
						$ifile = explode("/", $img);
						$ifile = end($ifile);
						$ref = $this->objConfig->getSiteRoot()."usrfiles/importcms/".$csecid."/".$ifile;
						//$replacement = "<img src=\"$ref\" />";
						$replacement = $ref;
						// move the files to the repo
						//var_dump($imgs);
						//var_dump($replacement);
						//$body = str_replace($imgs[3][$counter], $replacement, $body);
						//$body = preg_replace('/'.addslashes(str_replace('.', '\.', $imgs[3][$counter])).'/i', $replacement, $body);
						$body = str_replace($imgs[3][$counter], $replacement, $body);
						//var_dump($replacement);
						$counter++;
					}

					// case for the public folder - i.e frontpage
					if($secname == 'Public')
					{
						//log_debug("Adding Public page $title to frontapge");
						$pagearr = array(
						'title' => $title,
						'sectionid' => $csecid,
						'introtext' => 'Welcome',
						'body' => $body,
						'access' => 0,
						'published' => 1,
						'hide_title' => 0,
						'post_lic' => 'by-sa',
						'created_by' => $this->objUser->userId(),
						'creatorid' => $this->objUser->userId(),
						'metakey' => 'tag',
						'metadesc' => 'UWC',
						'start_publish' => NULL,
						'end_publish' => NULL,
						'isfrontpage' => '1',
						);
					}
					else {
						$pagearr = array(
						'title' => $title,
						'sectionid' => $csecid,
						'introtext' => '',
						'body' => $body,
						'access' => 0,
						'published' => 1,
						'hide_title' => 0,
						'post_lic' => 'by-sa',
						'created_by' => $this->objUser->userId(),
						'creatorid' => $this->objUser->userId(),
						'metakey' => 'tag',
						'metadesc' => 'UWC',
						'start_publish' => NULL,
						'end_publish' => NULL,
						'isfrontpage' => 0,
						);
					}
					if ($must_add_content && $must_add_title){
						$pgid = $this->objCmsDb->addContent($pagearr);
						//log_debug("Adding page (id $pgid) with title $title to section ($csecid) | fullpath : $content_path");

						//Logging content item to db:
						$this->dbLog->log($content_path, $pgid, $csecid);
	
					} else {
							if (!$must_add_title){
						 	log_debug("Failed : AddContent Missing Title $csecid $content_path");
						} else if (!$must_add_content) {
						 	log_debug("Failed : AddContent Content to $csecid $content_path");
						} else {
							log_debug("Failed : AddContent Unknown Error");
						}
					}
					//var_dump($contents);
					unset($page);
					unset($pagearr);
				}
				unset($pageobj);
				unset($pages);
			}
			
		}
	}
	
	public function addWelcomePage($section, $secid)
	{
		$pages[] = $this->objStdlib->fileLister($section);
		$pages = array_filter($pages);
		if(!empty($pages))
		{
			foreach ($pages as $pageobj)
			{
				foreach($pageobj as $page)
				{
					// ok now we need to do some magic on the pages.
					$contents = file_get_contents($section."/".$page);
					$content_path = $section.'/'.$page;

					// grok the title
					//preg_match_all('/\<title>(.*)\<\/title\>/U', $contents, $tresults, PREG_PATTERN_ORDER);
					$title = $this->objRegex->get_doc_title($contents);
					//$title = $tresults[1][0];
					$title = explode("--", $title);

					$must_add_title = false;

					if(isset($title[1]))
					{
						//Title will consist of all other information delimited by --
						/*
						$total = count($title);
						for ($i = 1; $i < $total; $i++){
						    $title_str .= $this->str_first_upper($this->strip_ext(trim($title[$i])), '--').' ';
                        }
                        $title = $title_str;
						*/

						//Compensating for known problem titles
						if (preg_match('/.*student.*src.*/i', $content_path)
							|| preg_match('/.*student.*sds.*/i', $content_path)
							|| preg_match('/.*student.*postgrad.*/i', $content_path)
							|| preg_match('/.*student.*international.*/i', $content_path)
							|| preg_match('/.*student.*parttime.*/i', $content_path)
							|| preg_match('/.*student.*undergrad.*/i', $content_path)
							|| preg_match('/.*faculty.*project.*focus.*/i', $content_path)
							|| preg_match('/.*faculty.*project.*essru.*/i', $content_path)
							|| preg_match('/.*faculty.*project.*pet.*/i', $content_path)
							|| preg_match('/.*faculty.*project.*envirofacts.*/i', $content_path)
							|| preg_match('/.*faculty.*project.*hypnea.*/i', $content_path)
							|| preg_match('/.*faculty.*law.*/i', $content_path)
							|| preg_match('/.*faculty.*economic.*/i', $content_path)
							|| preg_match('/.*faculty.*center.*cshe.*/i', $content_path)
							|| preg_match('/.*faculty.*school.*/i', $content_path)
							|| preg_match('/.*faculty.*departments.*/i', $content_path)
							|| preg_match('/.*faculty.*unit.*avoir.*/i', $content_path)
							|| preg_match('/.*faculty.*unit.*gender.*/i', $content_path)
							){
								$title = end($title);
							} else {
								$title = $title[1];
							}
						
						$must_add_title = true;
					}
					else {
						$title = $this->objLanguage->languageText("mod_portalimporter_missingpgtitle", "portalimporter");
						$must_add_title = false;
					}
					$title = trim($title);

					//log_debug("Adding page with title $title to top level section $secid");

					// grab the content body
					preg_match_all('/<!--CONTENT_BEGIN-->(.*)<!--CONTENT_END-->/iseU', $contents, $bresults, PREG_PATTERN_ORDER);

					$must_add_content = false;

					if(isset($bresults[1][0]))
					{
						$body = $bresults[1][0];
						$must_add_content = true;
					}
					else {
						$must_add_content = false;
						//$body = $this->objLanguage->languageText("mod_portalimporter_missingtagsinbody", "portalimporter");
					}
					// change the links in the pages to the assets to point to the correct ones.
					
					// change the anchors as well?
					// changing anchors must be done after import
					// clean up the bad code
					$options = array(
					"clean" => true,
					"indent" => true,
					"indent-spaces" => 4,
					"drop-proprietary-attributes" => true,
					"drop-empty-paras" => true,
					"word-2000" => true,
					"quote-ampersand" => true,
					"lower-literals" => true,
					"show-body-only" => true,
					);

					if (function_exists('tidy_parse_string')) {
						$tidy = new tidy;
						$tidy->parseString($body, $options, 'utf8');
						$tidy->cleanRepair();
					} else {
						die ("TIDY is not available");
					}
					$body = $tidy;
					$pagearr = array(
						'title' => $title,
						'sectionid' => $secid,
						'introtext' => '',
						'body' => $body,
						'access' => 0,
						'published' => 1,
						'hide_title' => 0,
						'post_lic' => 'by-sa',
						'created_by' => $this->objUser->userId(),
						'creatorid' => $this->objUser->userId(),
						'metakey' => 'tag',
						'metadesc' => 'UWC',
						'start_publish' => NULL,
						'end_publish' => NULL,
						'isfrontpage' => '1',
						'imagesrc' => NULL,
						);
					if ($must_add_content && $must_add_title){
						$pgid = $this->objCmsDb->addContent($pagearr);
						//log_debug("Adding page (id $pgid) with title $title to section ($csecid) | fullpath : $content_path");

						//Logging content item to db:
						$this->dbLog->log($content_path, $pgid, $csecid);
					} else {
						if (!$must_add_title){
						 	log_debug("Failed : Missing Title $csecid $content_path");
						} else if (!$must_add_content) {
						 	log_debug("Failed : Missing Content to $csecid $content_path");
						} else {
							log_debug("Unknown Error");
						}

					}
	
					//var_dump($contents);
					unset($page);
					unset($pagearr);
				}
				unset($pageobj);
				unset($pages);
			}
			
		}

	}






				/**
				 *  Strips a files extention
				 */

				function strip_ext($str, $extensions = 'htm|html')
				{
						$ext_arr = explode('|', $extensions);
						foreach ($ext_arr as $ext)
						{
								$str_ext = end(explode('.', $str));
								if ($str_ext == $ext)
								{
										$ret_str = str_replace('.'.$ext, '', $str);
										echo $str."\n";
										return $ret_str;
								} else {
										return $str;
								}
						}
				}

				/*
				 * Makes a strings first character an uppercase character
				 *
				 */

				function str_first_upper($str, $delim = ' '){
						$str = explode($delim, strtolower($str));
						for($i = 0; $i < count($str); $i++){
								if (count($str) > 1){
										$str[$i] = strtoupper(substr($str[$i], 0, 1)) . substr($str[$i], 1) . ' ';
								} else {
										$str[$i] = strtoupper(substr($str[$i], 0, 1)) . substr($str[$i], 1);
								}
						}
						return implode('', $str);
				}






}
?>
