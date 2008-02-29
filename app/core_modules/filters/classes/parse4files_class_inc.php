<?php
/**
* 
* Parse string for filter for directory contents
*  
* Class to parse a string (e.g. page content) that contains a filter
* code for including the all files in a user directory as links with descriptions
* where descriptions exist.
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
* @package   filters
* @author    Derek Keats <dkeats@uwc.ac.za>
* @copyright 2007 Derek Keats
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   CVS: $Id$
* @link      http://avoir.uwc.ac.za
*/



/**
*
* Parse string for filter for directory contents
*  
* Class to parse a string (e.g. page content) that contains a filter
* code for including the all files in a user directory as links with descriptions
* where descriptions exist.
*
* @author Derek Keats
*
*/
class parse4files extends object
{

    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;

    /**
    *
    * String object $objExpar is a string to hold the parameter extractor object
    * @access public
    *
    */
    public $objExpar;

    /**
     *
     * String $dirpath is the path to search for files
     * @access public
     *
     */
    public $type;

    /**
     *
     * Constructor for the TWITTER filter parser
     *
     * @return void
     * @access public
     *
     */
    public function init()
    {
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
        $this->objUser = $this->getObject('user', 'security');

    }

    /**
    *
    * Method to parse the string
    * @param  string $str The string to parse
    * @return string The parsed string
    *
    */
    public function parse($txt)
    {
       	//Match filters based on a wordpress style
       	preg_match_all('/\\[FILES:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
       	$counter = 0;
       	foreach ($results[0] as $item) {
            //$this->item=$item;
        	$str = $results[1][$counter];
        	$ar= $this->objExpar->getArrayParams($str, ",");
            $this->setupPage();
            $replacement = $this->getFiles();
            //$replacement = htmlentities($replacement);
        	$txt = str_replace($item, $replacement, $txt);
        	$counter++;
        }
        return $txt;
    }

    /**
    *
    * Method to set up the parameter / value pairs for th efilter
    * @access public
    * @return VOID
    *
    */
    private function setUpPage()
    {
        //Get directory
        if (isset($this->objExpar->folder)) {
            $this->folder = $this->objExpar->folder;
        } else {
            $this->folder=NULL;
        }
        //Get userid
        if (isset($this->objExpar->username)) {
            $un = $this->objExpar->username;
            $this->userId = $this->objUser->getUserId($un);
        } else {
            $this->userId=NULL;
        }
        //Get directory
        if (isset($this->objExpar->newline)) {
            $this->newLine = $this->objExpar->newline;
        } else {
            $this->newLine=NULL;
        }

    }

	/**
	 * 
	 * Method to return a formatted list of files in the directory specified in the filter
	 * tag
	 * 
	 * @access private
	 * @param string $dirname The folder name
	 * @param string $item Item within which the replacement should take place
	 * @return the parased item with the linked files replacing the filter tags
	 * 
	 */
    private function getFiles()
    {
        $oF = $this->getObject('dbfile', 'filemanager');
        if ($this->folder == "/") {
           	$this->folder = NULL;
        } else {
        	$this->folder = "/" . $this->folder;
        }
        $sql = "SELECT filename, mimetype, path, filefolder, description FROM tbl_files WHERE userid = '" . $this->userId 
          . "' AND filefolder = 'users/" . $this->userId . $this->folder . "'";
        $ar = $oF->getArray($sql);
        return $this->renderFiles($ar);
    }
    
    /**
    * 
    * Method to return a formatted list of files from the array passed to it
    * by the getFiles method.
    * 
    * @access private
    * @param string array $ar An array of files and descriptions
    * @return String the parased item with the linked files and their descriptions
    * 
    */
    private function renderFiles(& $ar)
    {
    	$ret = "<table cellpadding=\"5\" cellspacing=\"2\" style=\"margin-left:10px\">";
        $objConfig = $this->getObject('altconfig', 'config');
        $siteRoot = $objConfig->getSiteRoot();
        $this->oIcon = $this->getObject('fileicons', 'files');
        foreach ($ar as $file) {
            $description = $file['description'];
            if ($description==""){
                $description = $this->objLanguage->languageText("mod_filters_file_nodesc", "filters");
            }
            $path = $siteRoot . "/usrfiles/" . "/" . $file['path'];
            $icon = $this->oIcon->getFileIcon($file['filename']);
            $ret .= "<tr><td>" . $icon . "&nbsp;<a href=\"" . $path . "\">" 
              . $file['filename'] . "</a></td>";
            if ($this->newLine == "true" || $this->newLine==TRUE) {
                $ret .= "</tr><tr>";
            }
            $ret .= "<td>&nbsp;&nbsp;&nbsp;" . $description . "</td></tr>";
        }
        $ret .= "</table>";
        return $ret;
    }
}