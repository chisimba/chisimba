<?php

/**
 * Class to detemine which subfolder a file should be placed in
 *
 * It does this based on an analysis of either:
 * 1) mimetype
 * 2) extension
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
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */


/**
 * Class to detemine which subfolder a file should be placed in
 *
 * It does this based on an analysis of either:
 * 1) mimetype
 * 2) extension
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
class filefolder extends object
{

    /**
    * Constructor
    */
    function init()
    {
        $this->objFileParts = $this->getObject('fileparts', 'files');
    }
    
    /**
    * Method to determine which sub folder a file should be placed in
    *
    * Note: This function is pretty hardcoded in determining the result
    * More dynamic options are welcome.
    *
    * @param  string $name     Name of the File
    * @param  string $mimetype Mimetype of the File
    * @return string Sub Folder file must be placed in
    */
    function getFileFolder($name, $mimetype)
    {
        $mimeSplit = explode ('/', $mimetype);
        $extension = $this->objFileParts->getExtension($name);
        
        // Possible Folders: 'images', 'audio', 'video', 'documents', 'flash', 'freemind', 'archives', 'other', 'obj3d', 'scripts'
        
        // Check by Full Mimetype
        switch ($mimetype)
        {
            case 'application/x-shockwave-flash': return 'flash'; break;
            case 'application/x-shockwave-flash2-preview': return 'flash'; break;
            case 'application/vnd.ms-excel': return 'documents'; break;
            case 'application/msword': return 'documents'; break;
            case 'application/powerpoint': return 'documents'; break;
            case 'application/vnd.ms-powerpoint': return 'documents'; break;
            case 'application/pdf': return 'documents'; break;
            case 'application/x-rar-compressed' : return 'archives'; break;
            case 'application/x-javascript' : return 'scripts'; break;
            case 'text/x-sql' : return 'scripts'; break;
            case 'text/css' : return 'scripts'; break;
            case 'video/x-theora' : return 'video'; break;
            case 'audio/x-vorbis' : return 'audio'; break;
            default : break;
        }
        
        // Check First Part of Mimetype
        switch ($mimeSplit[0])
        {
            case 'image': return 'images'; break;
            case 'audio': return 'audio'; break;
            case 'video': return 'video'; break;
            // text is excluded as it may be a script
            default : break;
        }
        
        // Check Second Part of Mimetype
        /* // Not Checked at the moment
        switch ($mimeSplit[1])
        {
            default : break;
        }*/
        
        // Check by extension
        switch ($extension)
        {
            case 'psd':
            case 'wbmp':
                return 'images'; break;
            case 'doc': // Microsoft Office
            case 'xls':
            case 'xlt':
            case 'ppt':
            case 'pps':
            case 'odb': // Open Office 2
            case 'odf':
            case 'odg':
            case 'odm':
            case 'odp':
            case 'ods':
            case 'odt':
            case 'otg':
            case 'oth':
            case 'otp':
            case 'ots':
            case 'ott':
            case 'sxc': // Open Office
            case 'sxd':
            case 'sxi':
            case 'sxw':
            case 'mdb': // MS Access Database
            case 'vsd': // Visio
            case 'chm': // Windows Help Files
            case 'rss': // RSS Feeds
                return 'documents'; break;
            case 'mm': 
                return 'freemind'; break;
            case 'zip': // Archives
            case 'tar':
            case 'gz':
            case 'rar':
            case 'arj':
            case 'ace':
                return 'archives'; break;
            case 'ogg':
            case 'mp3':
                return 'audio'; break;
            case 'rm';
            case '3gp':
            case 'flv':
                return 'video'; break;
            case 'wrl': // VRML
            case 'vrml':
            case 'obj':
                return 'obj3d'; break;
            case 'php': // Programming Scripts
            case 'phps': // Programming Scripts
            case 'css':
            case 'js':
            case 'sql':
            case 'java':
            case 'py':
            case 'pl':
            case 'cgi':
            case 'jsp':
            case 'asp':
            case 'aspx':
            case 'cfm':
            case 'xml':
                return 'scripts'; break;
            case 'ttf':
                return 'fonts'; break;
            default:
                break;
        }
        
        // Check First Part of Mimetype
        switch ($mimeSplit[0])
        {
            case 'image': return 'images'; break;
            case 'audio': return 'audio'; break;
            case 'video': return 'video'; break;
            case 'text': return 'documents'; break;
            default : break;
        }
        
        // If no other folder is possible, return 'other'
        return 'other';
    
    }
     

}

?>