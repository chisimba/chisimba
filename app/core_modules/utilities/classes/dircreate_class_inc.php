<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* Class to create a Directory on the File System
*
* @category context
* @package utilities
* @author Wesley  Nitsckie
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version $Id$
* @link      http://avoir.uwc.ac.za
*/

class dircreate extends object
{
	/**
	* @var object $objConfig
	*/
	var  $objConfig;

	function init(){
		$this->objConfig=$this->newObject('altconfig','config');
	}

    /**
    * method to create specified folder
    * @author James Scoble
    * @param string $userId
    */
    function makeFolder($folder,$root='')
    {
        if ($root==''){
            $dir = $this->objConfig->getSiteRootPath().'/'.$folder;
        } else {
            $dir=$root.'/'.$folder;
        }
        if (!(file_exists($dir))){
            $oldumask = umask(0);
            mkdir($dir, 0777);
            umask($oldumask);
        }
    }
    
    
     /**
     * Method to delete a folder recursively
     * @param string $dir The full path to the folder
     * @access public
     * @return boolean
     */
     function deldir($dir) {
         $dh=@opendir($dir);
         while ($file=@readdir($dh)) {
             if($file!="." && $file!="..") {
                 $fullpath=$dir."/".$file;
                 if(!is_dir($fullpath)) {
                     unlink($fullpath);
                 } else {
                     $this->deldir($fullpath);
                 }
             }
         }
         @closedir($dh);
         if(@rmdir($dir)) {
             return TRUE;
         } else {
             return FALSE;
         }
     }

}
?>