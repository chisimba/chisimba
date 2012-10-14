<?php

/**
 * Mkdir class
 * 
 * Class to rename directory/file & manage errors in doing so.
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
 * @author David Wafula
 * @copyright 2010, University of the Witwatersrand & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @link      http://avoir.uwc.ac.za,http://www.chisimba.com
 * @see       References to other sections (if any)...
 */

class rename extends object
{

    /**
    * 
    * @var string $oldFilePath The old file including its path,
    *             or the folder if rename a folder.
    *             
    */
    var $oldFilePath;

    /**
    *
    * @var string $newFilePath The new file including its path,
    *             or the folder if rename a folder.
    *
    */
    var $newFilePath;
    
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
        $this->oldFilePath = NULL;
        $this->newFilePath = NULL;
        $this->err = NULL;
        $this->errMsg = NULL;
        // Create an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language'); 
    }
    
    /**
    * 
    * Renames a file/folder. Needs oldFilePath and newFilePath to be set
    * to a valid path before hand
    * 
    * @access Public
    *                
    */
    function rename()
    {
        if ($this->oldFilePath != NULL && $this->newFilePath != NULL) {
                rename($this->oldFilePath,$this->newFilePath);
        //If the file path is null
        } else {
            $this->err = TRUE;
            $this->errMsg = $this->objLanguage->languageText('mod_files_err_nofilepath') 
              . ": Old - " . $this->oldFilePath.",  New - " . $this->newFilePath;
            return FALSE;
        }
    } //function
     
} // end of class
?>
