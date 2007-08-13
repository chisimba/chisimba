<?php

/**
 * File deletion class
 * 
 * Abstraction class for deletion of files
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
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */


/**
 * Deletion class
 * 
 * Abstraction-layer class for eletion of files and folders.
 * 
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       utilities
 */
class del extends object 
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
    * Delete the file specified in $fullFilePath
    * inline {@internal checks the OS php is running on, and execute appropriate command}}
    * @access Public
    * @param  string $type file | folder if its a file or a folder
    * @return bool   TRUE | FALSE, sets error if error occurs
    *                
	*/
    function delete()
    {
        if ($this->fullFilePath != NULL) {
            //Check if its a file
            if (is_file($this->fullFilePath)) {
                //DO the unlink and check for success
                if (unlink( $this->fullFilePath )) {
                    return TRUE;
                } else {
                    $this->err = TRUE;
                    $this->errMsg = $this->objLanguage->languageText('mod_files_err_unable2del')
                      . ": " . $this->fullFilePath;
                }
            } else {
                $this->err = TRUE;
                $this->errMsg = $this->objLanguage->languageText('mod_files_err_isdirnotfile')
                  . ": " . $this->fullFilePath;
            }
        //If the file path is null
        } else {
            $this->err = TRUE;
            $this->errMsg = $this->objLanguage->languageText('mod_files_err_nofilepath') 
              . ": " . $this->fullFilePath;
            return FALSE;
        }
    } //function
    
} // end of class
?>
