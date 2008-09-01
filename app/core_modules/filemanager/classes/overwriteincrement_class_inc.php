<?php

/**
 * Class to generate overwrite-increment filenames
 *
 * What is overwrite-increment?
 *
 * User uploads file called 'mydoc.txt', which already exists. Module
 * owners can choose to have it automatically rename to 'mydoc_1.txt'.
 * If that exists, it goes to 'mydoc_2.txt', etc
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
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: overwriteincrement_class_inc.php 9097 2008-05-12 08:00:21Z tohir $
 * @link      http://avoir.uwc.ac.za
 * @see
 */


/**
 * Class to generate overwrite-increment filenames
 *
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see
 */
class overwriteincrement extends object
{



    /**
    * Constructor
    */
    function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objCleanUrl = $this->getObject('cleanurl');
    }
    
    /**
     * Method to check whether a filename is available.
     *
     * If not, return the next available overwrite increment
     *
     * @param string $file Name of the File
     * @param string $path Path the file has to be stored - do not use full path here
     * @return string $filename Filename that should be used
     */
    function checkfile($file, $path)
    {
        // Explode file to get file name and extension
        $fileparts = pathinfo($file);
        
        // Set Default Values
        $match = FALSE;
        $counter = 1;
        
        // Create Full Server Path to Uploaded File
        $savepath = $this->objConfig->getcontentBasePath().'/'.$path.'/';
        // Create Path to File withour usrfiles prefix
        $path = $path.'/';
        
        // Clean Up Paths
        $savepath = $this->objCleanUrl->cleanUpUrl($savepath);
        $path = $this->objCleanUrl->cleanUpUrl($path);
        
        // First check desired name
        if (!file_exists($savepath.$file)) {
            return $file;
        }
        
        // Do a loop until opening is file
        while ($match == FALSE)
        {
            // Generate new filename
            $filename = $fileparts['filename'].'_'.$counter.'.'.$fileparts['extension'];
            
            // If opening exists, update vars, and exit loop
            if (!file_exists($savepath.$filename)) {
                $match = TRUE;
                
                $savepath .= $filename;
                $path .= $filename;
            } else {
                $counter++;
            }
        }
        
        return $filename;
    }

} // end class
?>