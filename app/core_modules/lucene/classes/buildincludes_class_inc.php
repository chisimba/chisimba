<?php

/**
 * Class to Scan for Files for Indexing Purposes
 *
 * This class scans for all files and folders in a directory
 * on the filesystem and returns them as an array
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
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */


$this->loadClass('folderbot', 'files');

/**
 * Class to Scan for Files for Indexing Purposes
 *
 * This class scans for all files and folders in a directory
 * on the filesystem and returns them as an array
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
class buildincludes extends folderbot 
{

    /**
    * @var array $fileIndex Array holding all matched files
    */
    protected $fileIndex;
    
    /**
    * @var array $folderIndex Array holding all matched folders
    */
    protected $folderIndex;
    
    /**
    * Method to scan a directory
    * @param  string $directory Directory to Scan
    * @return array  An array containing list of files and folders
    */
    public function scanDirectory($directory)
    {
        $this->set_recurse(true); // set to false to only list the folder without subfolder.
        $this->scan($directory);
        
        return $this->fileIndex;
    }

    /**
    * Method to trigger a file event and add the file to the output
    * array.  Triggered by the parent class, it is called every 
    * time a file has been found.
    */
    public function file_event()
    {
        if (preg_match('/.*?\.php/', $this->curfile)) {
			$this->fileIndex[] = $this->curfile;
		}
    } 
    
    /**
    * Method to trigger a folder event and add thefolder to the output
    * array.  Triggered by the parent class, it is called every 
    * time a folder has been found.
    */
    public function folder_event()
    {           
        //$this->folderIndex[] = $this->curfile;
    }
    
} // end class
?>