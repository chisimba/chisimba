<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
die("You cannot view this page directly");
} 
// end security check
/**
* Display an icon for a file
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version 1
* @author Tohir Solomons
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
    * @param string $filename Name of the File
    * @return string Icon Image or NULL if an icon does not exist.
    */
    public function getFileIcon($filename)
    {
        return $this->getExtensionIcon($this->getExtension($filename));
    }
    
    /**
    * Method to get the extension of a file by providing the filename
    * @param string $filename Name of the File
    * @return string $extension Extension of the File
    */
    public function getExtension($filename)
    {
        // get base name of the filename provided by user
        $filename = basename($filename);
        
        // break file into parts seperated by .
        $filename = explode('.', $filename);
        
        // take the last part of the file to get the file extension
        $extension = $filename[count($filename)-1];
        
        return $extension;
    }
    
    /**
    * Method to get a file icon by providing the extension of the file
    * @param string $extension Extension of the File
    * @return string Icon Image or NULL if an icon does not exist.
    */
    public function getExtensionIcon($extension)
    {
        $filedesc = $extension;
        // Get Image name based on extension
        switch (strtolower($extension))
        {
            case 'ace' : $imagename = 'winace'; break;
            case 'asf' : $imagename = 'asf'; break;
            case 'avi' : $imagename = 'avi'; break;
            case 'bat' : $imagename = 'bat'; break;
            case 'bmp' : $imagename = 'bmp'; break;
            case 'chm' : $imagename = 'chm'; break;
            case 'doc' : $imagename = 'doc'; $filedesc = 'MS Word Document'; break;
            case 'eml' : $imagename = 'eml'; break;
            case 'exe' : $imagename = 'exe'; break;
            case 'gif' : $imagename = 'gif'; break;
            case 'hlp' : $imagename = 'hlp'; break;
            case 'html' :
            case 'htm' : $imagename = 'htm'; $filedesc = 'HTML Document'; break;
            case 'java' : $imagename = 'java'; break;
            case 'jpg' : $imagename = 'jpg'; break;
            case 'mdb' : $imagename = 'mdb'; break;
            case 'mov' : $imagename = 'mov'; break;
            case 'mp3' : $imagename = 'mp3'; break;
            case 'mpg' : $imagename = 'mpg'; break;
            case 'ogg' : $imagename = 'ogg'; break;
            case 'pdf' : $imagename = 'pdf'; break;
            case 'phps' :
            case 'php' : $imagename = 'php'; break;
            case 'png' : $imagename = 'png'; break;
            case 'pps' : $imagename = 'pps'; break;
            case 'ppt' : $imagename = 'ppt'; break;
            case 'mpp' : $imagename = 'project'; break;
            case 'ps' : $imagename = 'ps'; break;
            case 'quark' : $imagename = 'quark'; break; // check right extension
            case 'rar' : $imagename = 'rar'; break;
            case 'ra':
            case 'rm' : $imagename = 'real'; break;
            case 'rtf' : $imagename = 'rtf'; break;
            case 'shockwave' : $imagename = 'shockwave'; break; // check right extension
            case 'swf' : $imagename = 'swf'; break;
            case 'sxc' : $imagename = 'oo-calc'; break;
            case 'sxd' : $imagename = 'oo-draw'; break;
            case 'sxi' : $imagename = 'oo-impress'; break;
            case 'sxw' : $imagename = 'oo-write'; break;
            case 'tgz' : $imagename = 'tgz'; break;
            case 'ttf' : $imagename = 'ttf'; break;
            case 'txt' : $imagename = 'txt'; break;
            case 'viewlet' : $imagename = 'viewlet'; break; // check right extension
            case 'vsd' : $imagename = 'visio'; $filedesc = 'MS Visio Document'; break;
            case 'wav' : $imagename = 'wav'; break;
            case 'wma' : $imagename = 'wma'; break;
            case 'wmv' : $imagename = 'wmv'; break;
            case 'wri' : $imagename = 'wri'; break;
            case 'xls' : $imagename = 'xls'; break;
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