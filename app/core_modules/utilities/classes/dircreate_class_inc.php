<?
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
* Class to create a Diroectory on the File Sysmtem
* @package utilities
* @category context
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version
* @author Wesley  Nitsckie
* @example :
*/

class dircreate extends object
{
	/**
	* @var object $objConfig
	*/
	var  $objConfig;

	function init(){
		$this->objConfig=&$this->newObject('config','config');
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
}
?>
