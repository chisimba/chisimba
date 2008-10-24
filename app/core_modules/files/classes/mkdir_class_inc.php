<?php

/**
 * Mkdir class
 * 
 * Class to make a directory & manage errors in doing so.
 * 
 * PHP version 3
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
 * @package   files
 * @author Tohir Solomons
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
/**
* 
* Class to make a directory & manage errors in doing so
* 
* @author Derek Keats
*/

class mkdir extends object 
{

    /**
    * 
    * @var string $fullFilePath The file including its path, 
    *             or the folder if deleting a folder.
    *             
    */
    var $fullFilePath;
    
    /**
    * 
    * @var boolean $err TRUE|FALSE; TRUE if error, else false
    *              
    */
    var $err;
    
    /**
    * 
    * @var string $errMst The error message
    *             
    */
    var $errMsg;
    
    /**
    * 
    * @var object $objLanguage String to hold the language object
    *             
    */
    var $objLanguage;
    
    /**
    * 
    * Standard init method to set the default values for
    * the properties.
    * 
    */
    function init()
    {
        $this->fullFilePath = NULL;
        $this->err = NULL;
        $this->errMsg = NULL;
        // Create an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language'); 
    }
    
	/**
    * 
    * Create a folder. Needs fullFilePath to be set
    * to a valid directory before hand
    * 
    * @access Public
    *                
	*/
    function makedir()
    {
        if ($this->fullFilePath != NULL) {
            //Check if its a file
            if (is_file($this->fullFilePath)) {
                $this->err = TRUE;
                $this->errMsg = $this->objLanguage->languageText('mod_files_err_isfilenotdir')
                  . ": " . $this->fullFilePath;
            } else {
                mkdir($this->fullFilePath);
            }
        //If the file path is null
        } else {
            $this->err = TRUE;
            $this->errMsg = $this->objLanguage->languageText('mod_files_err_nofilepath') 
              . ": " . $this->fullFilePath;
            return FALSE;
        }
    } //function
    
    /**
    * Method to Recursively Check for Folders
    * This method takes a path, and checks that each folder in the path exists.
    * @param  string $strPath Path to Check
    * @author Tohir Solomons
    */
    function mkdirs($strPath)
    {
        if (is_dir($strPath)) {
            return true;
        }
        
        $pStrPath = dirname($strPath);
        if (!$this->mkdirs($pStrPath)) {
            return false;
        }
        return mkdir($strPath);
    }
    
} // end of class
?>
