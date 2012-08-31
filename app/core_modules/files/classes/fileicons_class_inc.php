<?php

/**
 * Fileicons class file
 * 
 * Class for the display of module icons in Chisimba.
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
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
die("You cannot view this page directly");
} 
// end security check


/**
 * Fileicons class file
 * 
 * Class for the display of module icons in Chisimba.
 * 
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class fileicons extends object
{
    /**
    * @var string $size Size of the icon, either small or large
    */
    public $size = 'small';
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
    }

    /**
    * Method to get a file icon by providing the filename
    * @param  string $filename Name of the File
    * @return string Icon Image or NULL if an icon does not exist.
    */
    public function getFileIcon($filename)
    {
        return $this->getExtensionIcon($this->getExtension($filename));
    }
    
    /**
    * Method to get the extension of a file by providing the filename
    * @param  string $filename Name of the File
    * @return string $extension Extension of the File
    */
    public function getExtension($filename)
    {
       // $pathInfo = pathinfo($filename);
     //    return strtolower($pathInfo['extension']);    
       return substr(strrchr($filename, '.'), 1);
 
     }
    
    /**
    * Method to get a file icon by providing the extension of the file
    * @param  string $extension Extension of the File
    * @return string Icon Image or NULL if an icon does not exist.
    */
    public function getExtensionIcon($extension)
    {
        $filedesc = $extension;
        // Get Image name based on extension
        switch (strtolower($extension))
        {
            case 'ace' : $imagename = 'winace'; break;
            case 'ai' : $imagename = 'ai'; break;
            case 'asx':
            case 'asf' : $imagename = 'asf'; break;
            case 'avi' : $imagename = 'avi'; break;
            case 'bat' : $imagename = 'bat'; break;
            case 'bmp' : $imagename = 'bmp'; break;
            case 'chm' : $imagename = 'chm'; break;
            case 'css' : $imagename = 'css'; break;
            case 'csv' : $imagename = 'csv'; break;
            case 'dbf' : $imagename = 'dbf'; break;
            case 'dll' : $imagename = 'dll'; break;
            case 'doc' : $imagename = 'doc'; $filedesc = 'MS Word Document'; break;
            case 'eml' : $imagename = 'eml'; break;
            case 'eps' : $imagename = 'eps'; break;
            case 'exe' : $imagename = 'exe'; break;
            case 'flv' : $imagename = 'flv'; break;
            case 'folder' : $imagename = 'folder'; break;
            case 'gif' : $imagename = 'gif'; break;
            case 'gz' : $imagename = 'tgz'; break; // Tar.gz
            case 'hlp' : $imagename = 'hlp'; break;
            case 'html' :
            case 'htm' : $imagename = 'htm'; $filedesc = 'HTML Document'; break;
            case 'java' : $imagename = 'java'; break;
            case 'jpg' : $imagename = 'jpg'; break;
            case 'jpeg' : $imagename = 'jpg'; break;
            case 'js' : $imagename = 'js'; break;
            case 'mdb' : $imagename = 'mdb'; break;
            case 'mht' : $imagename = 'mht'; break;
            case 'mm' : $imagename = 'freemind'; break;
            case 'mov' : $imagename = 'mov'; break;
            case 'mp3' : $imagename = 'mp3'; break;
            case 'mpg' : $imagename = 'mpg'; break;
            case 'odb' : $imagename = 'odb'; break;
            case 'odf' : $imagename = 'odf'; break;
            case 'odg' : $imagename = 'odg'; break;
            case 'odm' : $imagename = 'odm'; break;
            case 'odp' : $imagename = 'odp'; break;
            case 'odt' : $imagename = 'odt'; break;
            case 'otg' : $imagename = 'otg'; break;
            case 'oth' : $imagename = 'oth'; break;
            case 'otp' : $imagename = 'otp'; break;
            case 'ots' : $imagename = 'ots'; break;
            case 'ott' : $imagename = 'ott'; break;
            case 'ogg' : $imagename = 'ogg'; break;
            case 'pdf' : $imagename = 'pdf'; break;
            case 'phps' :
            case 'php' : $imagename = 'php'; break;
            case 'png' : $imagename = 'png'; break;
            case 'pps' : $imagename = 'pps'; break;
            case 'ppt' : $imagename = 'ppt'; break;
            case 'psd' : $imagename = 'psd'; break;
            case 'mid' : $imagename = 'mid'; break;
            case 'mpp' : $imagename = 'mpp'; break;
            case 'ps' : $imagename = 'ps'; break;
            case 'py' : $imagename = 'python'; break;
            case 'quark' : $imagename = 'quark'; break; // check right extension
            case 'rar' : $imagename = 'rar'; break;
            case 'ra':
            case 'rm' : $imagename = 'real'; break;
            case 'rpm' : $imagename = 'rpm'; break;
            case 'rss' : $imagename = 'rss'; break;
            case 'rtf' : $imagename = 'rtf'; break;
            case 'sharedfolder' : $imagename = 'sharedfolder'; break;
            case 'shockwave' : $imagename = 'shockwave'; break; // check right extension
            case 'swf' : $imagename = 'swf'; break;
            case 'sxc' : $imagename = 'oo-calc'; break;
            case 'sxd' : $imagename = 'oo-draw'; break;
            case 'sxi' : $imagename = 'oo-impress'; break;
            case 'sxw' : $imagename = 'oo-write'; break;
            case 'tgz' : $imagename = 'tgz'; break;
            case 'tif' :
            case 'tiff' : $imagename = 'tif'; break;
            case 'ttf' : $imagename = 'ttf'; break;
            case 'txt' : $imagename = 'txt'; break;
            case 'viewlet' : $imagename = 'viewlet'; break; // check right extension
            case 'vsd' : $imagename = 'visio'; $filedesc = 'MS Visio Document'; break;
            case 'wav' : $imagename = 'wav'; break;
            case 'wma' :
            case 'wmv' : $imagename = 'wmv'; break;
            case 'wri' : $imagename = 'wri'; break;
            case 'xls' : $imagename = 'xls'; break;
            case 'xlt' : $imagename = 'xlt'; break;
            case 'xml' : $imagename = 'xml'; break;
            case 'zip' : $imagename = 'zip'; break;
            default : $imagename = 'clear'; // shows a transparent 16 x 16 image
        }
        
        // Return Image if image exists or null
        if ($imagename == NULL) {
            return NULL;
        } else {
            if ($this->size == 'large') {
                $iconfolder =  'icons/filetypes32/';
            } else {
                $iconfolder = 'icons/filetypes/';
            }
            
            $this->objIcon->setIcon($imagename, NULL, $iconfolder);
            $this->objIcon->alt = $filedesc;
            $this->objIcon->title = $filedesc;
            return $this->objIcon->show();
        }
    }

}
?>
