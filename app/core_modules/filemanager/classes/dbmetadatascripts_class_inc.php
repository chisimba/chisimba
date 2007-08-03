<?php

/**
 * Class to handle interaction with table tbl_files_metadata_scripts
 * 
 * This table relates to metadata about scripts
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
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */


/**
 * Class to handle interaction with table tbl_files_metadata_scripts
 * 
 * This table relates to metadata about scripts
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
class dbmetadatascripts extends dbTable
{
    
    /**
    * Constructor
    */
    function init()
    {
        parent::init('tbl_files_metadata_scripts');
        $this->objUser =& $this->getObject('user', 'security');
    }
    
    /**
    * Method to add a file
    * @param  string $fileId Record Id of the File
    * @param  string $script Geshi-highlighted Script
    * @return string Record Id of the entry
    */
    function addScriptHighlight($fileId, $script)
    {
        // Add File Id to Array
        $infoArray['fileid'] = $fileId;
        $infoArray['geshihighlight'] = $script;
        $infoArray['creatorid'] = $this->objUser->userId();
        $infoArray['datecreated'] = strftime('%Y-%m-%d', mktime());
        $infoArray['timecreated'] = strftime('%H:%M:%S', mktime());
        
        return $this->insert($infoArray);
    }

    
    
    

}

?>