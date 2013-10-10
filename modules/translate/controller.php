<?php
/* ------------translationmanager class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
* Module Translation Manager
* @author Nic Appleby
* @package Translation
* $Id: controller.php,v 1.0 2006/03/29
*/

class translate extends controller
{
    var $objLanguage;       // Language object for language independant text
    var $objLog;            // Logger object to log module calls
    var $objUser;
    var $config;            // Config object for content base path
    var $objDownload;
    var $objPoFile;
    var $PODirectory;

    /**
    * Standard method to initialise objects
    */
    public function init() {
        try {
            $this->objLanguage = $this->getObject("language", "language");
            $this->objModules = $this->getObject("modules","modulecatalogue");
            $this->objUser = $this->getObject('user', 'security');
            // Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            // Log this module call
            $this->objLog->log();
            // get config object for content base path
            $this->objConfig = $this->getObject('altconfig','config');
            $this->PODirectory = $this->objConfig->getcontentBasePath()."pofiles/";
            $this->objPoFile = $this->newObject('pofile','translate');
            $this->setLayoutTemplate('layout_tpl.php');
        } catch (customException $e) {
			echo customException::cleanUp($e);      
        }
    }

    /**
     * Method to download a pofile from the server
     *
     * @param string $file the name of the pofile
     */
    public function downloadFile($file) {
        $dp = $this->PODirectory.$file; // Download path
        if ($filesize = filesize($dp)) {
            header("Content-Type: application/force-download");
            header("Content-Type: application/download");
            if (preg_match('#Opera(/| )([0-9].[0-9]{1,2})#', getenv('HTTP_USER_AGENT')) or preg_match('#MSIE ([0-9].[0-9]{1,2})#', getenv('HTTP_USER_AGENT'))) {
                    header("Content-Type: application/octetstream");
            } else {
                    header("Content-Type: application/octet-stream");
            }
            header("Content-Disposition: attachment; filename={$file}");
            header("Content-Length: {$filesize}");
            readfile($dp);
            exit;
        }
    }

    /**
    * Main  branch function
    */
    public function dispatch() {
        $this->action = $this->getParam("action", null);
            switch ($this->action) {
                 case 'export':
                    set_time_limit(180);
                    $lang = $this->objLanguage->getRow('id',$this->getParam('language'));
                    
                    // english language table is incorrectly entered as tbl_english instead of tbl_en
                    if ($lang['id'] == "1") {
                        $lang['languagecode'] = 'tbl_en';
                        
/*
$sql='SELECT * FROM 
*/                        
                        // if the language table is swahili
                        if ($lang['id'] =="gen17Srv38Nme37_2172_1251353289") {
                        $lang['languagecode'] = 'tbl_ks';
                        }
                    }
                    
                    $type = $this->getParam('exportSelection');
                    switch ($type) {
                        case "0":
                            $module = $this->getParam('modDrop');
                            $fName = $this->objPoFile->exportModule($this->PODirectory,$module,$lang['languagecode']);
                            break;
                            
                        default:
                            $fName = $this->objPoFile->exportAll($this->PODirectory,$lang['languagecode']);
                            break;
                    }
                    log_debug("PO export done");
                    $this->downloadFile($fName);
                    return $this->nextAction(null,array('mod'=>$module,'feedback'=>'downloaded'));
                    
                case 'import':
                    set_time_limit(180);
                    $lang = $this->getParam('imlanguage');
                    $type = $this->getParam('importSelection');
                    
                    $name=$_POST['name'];
                    $meta=$_POST['meta'];
		    $error_text=$_POST['error_text'];
		    $values=array('name'=>$name,'meta'=>$meta,'error_text'=>$error_text);
		    $objDbform=$this->newObject('dbform','translate');
		    //echo  var_dump($values);die();
		    $objDbform->addfields($values);
                    
                    $uploadFile = $this->PODirectory.basename($_FILES['pofile']['name']);
                    if (!move_uploaded_file($_FILES['pofile']['tmp_name'], $uploadFile)) {
                        return $this->nextAction(null,array('feedback'=>'uploadfailed'));
                    }
                    $extension = strrchr($uploadFile,'.');

                    switch ($type) {
                        case "0":
                            if ($extension != ".po") {
                                return $this->nextAction(null,array('feedback'=>'filetype'));
                            }
                            $module = $this->getParam('importDrop');
                            $result = $this->objPoFile->importPOFile($lang,$module,$uploadFile);
                            break;
                            
                        default:
                            if ($extension != ".zip") {
                                return $this->nextAction(null,array('feedback'=>'filetype'));
                            }
                            $result = $this->objPoFile->importPOFileArchive($lang,$uploadFile);
                            break;
                    }
                    unlink($uploadFile);
                    return $this->nextAction(null,array('feedback'=>'import','imlanguage'=>$lang));

                default: 
                    return "form_tpl.php";
            }
    }

}
?>
