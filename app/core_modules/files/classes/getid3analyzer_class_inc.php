<?php

 /**
 * Wrapper to Get Id3
 *
 * This class is a wrapper to GetId3 which is a media analyser script
 * It provides information on media files such as width, height,
 * compression, codecs, bitrates, play length, etc.
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
 * @author Tohir Solomons
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */
/**
* Wrapper to Get Id3
*
* This class is a wrapper to GetId3 which is a media analyser script
* It provides information on media files such as width, height,
* compression, codecs, bitrates, play length, etc.
*
* @author Tohir Solomons
*/

class getid3analyzer extends object
{
    
    /**
    * Get Id3 Object
    * @var $_objGetID3
    */
    private $_objGetID3;
    
    /**
    * Constructor
    */
    public function init()
    {
        require_once($this->getResourceUri('getid3/getid3.php'));
    }
    
    /**
    * Method to Analyze a Media File
    * @param  string $file Path to File
    * @return array  Media Information
    */
    public function analyze($file)
    {
        // Turn Off Errors
        $displayErrors = ini_get('display_errors');
        error_reporting(0);
        
        $getID3 = new getID3();
                
        $ThisFileInfo = $getID3->analyze($file);
        
        getid3_lib::CopyTagsToComments($ThisFileInfo);
        
        // Turn Errors back On
        error_reporting($displayErrors);
        
        return $ThisFileInfo;
    }
}

?>
