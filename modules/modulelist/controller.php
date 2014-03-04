<?php
/* -------------------- modulelist class extends controller ----------------*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Module class to handle displaying the module list
*
* @author Paul Scott <pscott@uwc.ac.za>
*
* $Id: controller.php 23836 2012-03-21 11:43:32Z dkeats $
*/
class modulelist extends controller
{
	public $objUser;

    function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
    }

    function dispatch($action)
    {
        $this->requiresLogin();
        // See if we want packages or core_modules
        $whichDir = $this->getParam('moduletype', 'packages');
        
        
        // ignore action at moment as we only do one thing - list modules
        $siteRootPath = $this->objConfig->getSiteRootPath();
        foreach(glob($siteRootPath . $whichDir .'/*') as $dirs) {
            $chkdirs = str_replace($siteRootPath . $whichDir . '/', "", $dirs);
            if($chkdirs == 'COPYING' || $chkdirs == 'build.xml'|| $chkdirs == 'build.xml~' || $chkdirs == 'chisimba_modules.txt' || $chkdirs == 'modlist.php') {
                continue;
            } else {
                $dirname = $dirs;
                if(file_exists(("$dirs/register.conf"))) {
                    $moddata = file_get_contents("$dirs/register.conf");
                }
                preg_match_all('/(MODULE_DESCRIPTION:(.*))/', $moddata, $results);
                if(isset($results[2][0])) {
                    $descrip = $results[2][0];
                }
                preg_match_all('/(MODULE_STATUS:(.*))/', $moddata, $results);
                if(isset($results[2][0])) {
                    $status = strtolower($results[2][0]);
                } else {
                    $status = "unset";
                }
                preg_match_all('/(MODULE_NAME:(.*))/', $moddata, $results);
                if(isset($results[2][0])) {
                    $modName = strtolower($results[2][0]);
                } else {
                    $modName = "unset";
                }
                
                preg_match_all('/(MODULE_VERSION:(.*))/', $moddata, $results);
                if(isset($results[2][0])) {
                    $modVer = strtolower($results[2][0]);
                } else {
                    $modVer = "unset";
                }
                preg_match_all('/(MODULE_AUTHORS:(.*))/', $moddata, $results);
                if(isset($results[2][0])) {
                    $modAuthors = $results[2][0];
                } else {
                    $modAuthors = "unset";
                }
                
                $dirSize = $this->foldersize($dirname);
                
                $moduleList[] = array(
                  'modname' => $chkdirs, 
                  'longname' => $modName,
                  'version' => $modVer,
                  'description' => $descrip,
                  'authors' => $modAuthors,
                  'status' => $status,
                  'dirsize' => $dirSize);
            }
        }
        
        $this->setVar('moduleList', $moduleList);
        return "list_tpl.php";
    }
    
    public function foldersize($path) {
        $cmd = "du -h -s " . $path;
        return "<pre>" . shell_exec ( $cmd ) . "</pre>";
        
        $total_size = 0;
        $files = scandir($path);
        $cleanPath = rtrim($path, '/'). '/';

        foreach($files as $t) {
            if ($t<>"." && $t<>"..") {
                $currentFile = $cleanPath . $t;
                if (is_dir($currentFile)) {
                    $size = $this->foldersize($currentFile);
                    $total_size += $size;
                }
                else {
                    $size = filesize($currentFile);
                    $total_size += $size;
                }
            }   
        }
        return $this->formatSize($total_size);
    }
    
    public function formatSize($size) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $mod = 1024;
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        $endIndex = strpos($size, ".")+3;
        return substr( $size, 0, $endIndex).' '.$units[$i];
    }

    
    /**
     * Overide the login object in the parent class
     *
     * @param  void
     * @return bool
     * @access public
     */
    public function requiresLogin() {
        return FALSE;
    }

}

?>