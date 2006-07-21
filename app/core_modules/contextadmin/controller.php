<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * The class contextadmin that manages
 * the contexts
 * @package contextadmin
 * @category context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Wesley Nitsckie
 *
 * The importing process
 * -------------------------
 *  1. It takes the zipped file .. uploads it to the php's temp directory
*   2. It creates a import_dir folder in your nextgen folder
*   3. Copies the zipped file to the import_dir folder then unzips it using the OS unzip command
*   4. Loops recursively through the unzipped folder adding folders and html files as nodes
*   5. Images and other docs are added as blobs
*
*
*    Therefore :
*   a) Your php should be set to upload files
*   b) Your SITE_ROOT_PATH needs to exist
*   c) Linux will have the unzip command working
*        but Windows sometimes dont. To make it
*        work on windows download the unzip.exe from
*        http://kngforge.uwc.ac.za/resources/unzip.exe
*        and copy it into c:\winnt\system32\ folder
*
 */

class contextadmin extends controller {

    /**
    * @var  object $objLanguage
    */
    var $objLanguage;

   /**
    * @var object $objDBNodes
    */
    var $objDbNodes;

   /**
    * @var object $objConfig
    */
    var $objConfig;

   /**
    * @var object $objUser
    */
    var $objUser;

   /**
    * @var object $objButtons
    */
    var $objButtons;

    /**
    * @var object $Table
    */
    var $Table;

   /**
    * @var object objModule
    */
    var $objModule;

    /**
    * @var object objDBContext
    */
    var $objDBContext;

   /**
    * @var object objDBContextModules
    */
    var $objDBContextModules;

   /**
    * @var object objDBParentNodes
    */
    var $objDBParentNodes;

    /**
    *@var object $objectConfirm
    */
    var $objConfirm;

    /*
    * @var object objExportContent
    */
    var $objExportContent;

    /*
    * @var object objIcon
    */
    var $objIcon;

    /*
    * @var object objLink
    */
    var $objLink;

    /*
    * @var string contextCode
    */
    var $contextCode;

    /*
    * @var object $objContextGroups
    */
    var $objContextGroups;

     /*
    * @var object $objToolBar
    */
    var $objToolBar;


    /*
    * Method that initializes the objects
    */
    function init()
    {
        $this->objDBContextModules=&$this->getObject('dbcontextmodules','context');
        $this->objButtons = &$this->getObject('navbuttons', 'navigation');
        $this->objDBContext = &$this->getObject('dbcontext', 'context');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->objDBContentNodes = &$this->getObject('dbcontentnodes', 'context');
        $this->objDBParentNodes = &$this->getObject('dbparentnodes', 'context');

        $this->objConfig = &$this->getObject('config', 'config');
        $this->Table = &$this->getObject('htmltable', 'htmlelements');
        $this->objModule=& $this->getObject('modules','modulecatalogue');
        $this->loadClass('checkbox','htmlelements');
        $this->objConfirm=&$this->newObject('confirm','utilities');
        $this->objExportContent = & $this->newObject('export','contextadmin');
        $this->objIcon = & $this->newObject('geticon','htmlelements');
        $this->objLink = & $this->newObject('link','htmlelements');
        $this->objZip = &$this->newObject('zip','utilities');
        $this->objContextGroups = $this->newObject('managegroups', 'contextgroups');
        $this->objToolBar = &$this->newObject('tools','toolbar');

        $this->contextCode = $this->objDBContext->getContextCode();
        $this->contextTitle = $this->objDBContext->getTitle();
        $this->objPage =& $this->getObject('page', 'toolbar');

         //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();


    }

    /**
    *Method that the engine class requires
    */
    function dispatch()
    {
        $action = $this->getParam("action", null);
        switch ($action) {
            case null:

            case 'list':
                //$this->setVar('list', $this->listContexts());
                 $this->setVar('ar', $this->objContextGroups->userContexts());
                return 'main_tpl.php';
            case 'courseadmin':
                if($this->objDBContext->isInContext()){
                    $context = TRUE;
                }else{
                    $context = FALSE;
                }
                $modules = $this->objPage->getPage('lecturer', $context);
                $this->setVarByRef('modules',$modules);
                return 'lecturer_tpl.php';
            case 'add':
                return 'add_tpl.php';
            case 'create';
                $contextId = $this->doAdd();
                if($contextId){
                    $row = $this->objDBContext->getRow('id', $contextId);
                    $params = array ('contextCode' => $row['contextCode']);
                    return $this->nextAction('joincontext', $params);
                }else {
                    return $this->nextAction('list');
                }
            case 'joincontext':
                $this->objDBContext->leaveContext();
                $this->objDBContext->joinContext();
                return $this->nextAction('courseadmin');
            case 'edit':
                if(!$this->getParam('contextCode') == ''){
                    $this->objDBContext->leaveContext();
                    $this->objDBContext->joinContext();
                }
                return 'edit_tpl.php';
            case 'save':
                $this->saveContext();
                return $this->nextAction('list');
            case 'delete':
                $this->deleteContext($this->getParam('contextCode'));
                return $this->nextAction('list');
            case 'listmodules':
               return 'listmodules_tpl.php';
                break;
            case 'savemodules':
                $this->saveModules();
                $this->setVar('savedTime', $this->objDBContext->getDate());
                return 'listmodules_tpl.php';
            case 'showimport':
                return 'addstatic_tpl.php';
            case 'xml':
                $this->objDBContext->getContextXML();
                return 'main_tpl.php';
            case 'deletecontent':
                $this->objDBContentNodes->getArray("DELETE FROM tbl_context_parentnodes_has_tbl_context WHERE tbl_context_id='".$this->getParam('contextid')."' ");
                return $this->nextAction('courseadmin');
            case 'exportcontent':
                $this->objExportContent->exportStaticContent();
                $this->objExportContent->writeCSS();
                $this->setVar('list',$this->createLinkToStatic());
                return 'exportcontent_tpl.php';

           //the next few cases deals with importing content

           case 'importone':
               if($this->objDBParentNodes->getParentNodeId())
               {
                   $this->setVar('txt', $this->objLanguage->languageText("mod_contextadmin_importhelp",'contextadmin'));
                    $this->setLayoutTemplate('import_layout_tpl.php');
                    return 'importone_tpl.php';
                } else {
                    $params = array('itype' =>'1');
                    return $this->nextAction('delallimport', $params);
                }
            case 'import_type':
                return $this->getImportType();
            case 'delallimport':
                 $this->setVar('itype', $this->getParam('itype'));
                 $this->setLayoutTemplate('import_layout_tpl.php');
                return 'delall_tpl.php';
            case 'importtonode':
                $this->setVar('itype', $this->getParam('itype'));
                $this->setLayoutTemplate('import_layout_tpl.php');
                return 'shownodes_tpl.php';
            case 'showimportnode':
                $this->setLayoutTemplate('import_layout_tpl.php');
                return 'showimport_tpl.php';
            case 'success':
                if($this->getParam('error') == '1'){
                        $this->setVar('error', '1');
                }
                $this->setLayoutTemplate('import_layout_tpl.php');
                return 'import_success_tpl.php';
            case 'import':
                $tmp = $this->unzip();
                if(!$tmp){
                    //return $this->nextAction('success');
                 $this->setVar('error', '1');
                //} else {
                    //return $this->nextAction('success', array('error' => '1'));
                }
                $this->setLayoutTemplate('import_layout_tpl.php');
                return 'import_success_tpl.php';
            case 'replace':
                $this->repl();
        }
    }

    function repl(){

     $content = $this->newObject('dbpagecontent', 'context');
     $all = $content->getAll();
     foreach($all as $one){
        $body = str_replace('�','',$one['body']);
        $body = str_replace('�','\'',$body);
        $cnt = $cnt+1;
        $content->update('id', $one['id'], array('body'=>$body));
     }
     echo $cnt .' pages';
    }
    /**
    * Method to get the type of import that needs be done
    * @return template
    */
    function getImportType(){
        $type = $this->getParam('itype');
        $params = array('itype' =>$type);
        if($type == '1'){
            return $this->nextAction('delallimport', $params);
        } else {
            return $this->nextAction('importtonode', $params);
        }

    }

    /**
    * Method to create a linkto the the
    * static content that was exported
    */
    function createLinkToStatic()
    {
        $contextCode = $this->objDBContext->getContextCode();
        $this->objLink->href = $this->objConfig->siteRoot().'usrfiles/content/'.$contextCode.'/staticcontent/index.html';
        $this->objLink->link = $this->objLanguage->languageText("mod_contextadmin_gotostatic",'contextadmin');
        $ret = $this->objLink->show();

        $this->objLink->href = $this->objConfig->siteRoot().'usrfiles/content/'.$contextCode.'/'.$contextCode.'.zip';
        $this->objLink->link =$this->objLanguage->languageText("mod_contextadmin_downloadstaticcontent",'contextadmin');
        $ret .= '<p><p>'.$this->objLink->show();

        return $ret;
    }

    /**
     * Method to save the Context
     * @access public
     * @return null
     */
    function saveContext()
    {
        $this->objDBContext->saveContext('edit');
    }

    /**
     * Method to create a context
     * @access public
     * @return null
     */
    function doAdd(){
        $contextCode = addslashes(TRIM($_POST['contextCode']));
        if (!$this->objDBContext->valueExists('contextCode', $contextCode)) {
            return  $this->objDBContext->createContext();
        }
    }

    /**
    * Method to Delete a context
    */
    function deleteContext($contextCode){
        if ($contextCode) {
            $this->objDBContext->deleteContext($contextCode);
        }
    }


    /**
    *Method to show the edit section
     *@access public
     *@return null
     */
    function showEdit()
    {

            return false;

    }


    /**
    * Method that creates a checkbox
    * if the module is registered and
    * also sets the checkbox if the module
    * is visible
    * @param $name string : the name or id of the module
    * @param $text string : the text for the checkbox
    * @return $str string : returns the html string
     *@access public
    */
    function makeModuleItem($name=null,$text=null){
        //check if the module is registered
        if($this->objModule->checkIfRegistered(null,$name)){
            //create the checkbox
            $checkbox=new checkbox($name);

            //check if the module visible
            if ($this->objDBContextModules->isVisible($name,$this->objDBContext->getContextCode())) {
                 $checkbox->setChecked(true);
            }
                $str=$checkbox->show().'&nbsp;'.$text;
                return $str;
        }else{
            $icon=&$this->newObject('geticon','htmlelements');
            $icon->setModuleIcon('modulelist');
            $icon->alt=$name.'&nbsp;'.$this->objLanguage->languageText("mod_contextadmin_notregistered",'contextadmin');
            return $icon->show().'&nbsp;'.$name.'&nbsp;';
        }
    }

    /**
    * Method that saves the
    * context modules status
     *@access public
     *@return null
    */
    function saveModules(){
        //first delete all the entries for the course
        $this->objDBContextModules->deleteModulesForContext($this->objDBContext->getContextCode());
        foreach($_POST as $post => $v){
            if($v=='on'){
                //add an entry if the checkbox is ticked
                $this->objDBContextModules->setVisible($post,$this->objDBContext->getContextCode());
            }
        }
    }


    /**
    *Method to unzip the imported content
     *@access public
     *@return null
    */
    function unzip()
    {
        $zip = $this->getObject('zip', 'utilities');
         $dirObj=$this->getObject('dircreate','utilities');

         //get the uplaoded file
        $userfile=$_FILES['userfile']['type'];

        if(is_dir($this->objConfig->siteRootPath().'import_dir'))
        {
            $zip->deldir($this->objConfig->siteRootPath().'import_dir');
         }


        if (mkdir ($this->objConfig->siteRootPath().'import_dir',0777))
        {

            $size=$_FILES['userfile']['size'];
            $type=$_FILES['userfile']['type'];
            $location=$_FILES['userfile']['tmp_name'];

            $contextId=$this->objDBContext->getContextId();//$this->getParam('contextid');
            $title=$this->objDBContext->getTitle();//$this->getParam('title');
            $contextCode=$this->objDBContext->getContextCode();//$this->getParam('contextcode');

            $path=$this->objConfig->siteRootPath().'import_dir';


                //check for zip files only


            $dirObj->makeFolder('user_images');

           //copy the file to kng_content folder
           $tmpUpFileName=$_FILES['userfile']['tmp_name'];

           //replace the spaces in filename with ' _ '
           $upFileName=str_replace(" ","_",$_FILES['userfile']['name']);

           //copy the uploaded file to the root directory
           if (move_uploaded_file( $tmpUpFileName, $this->objConfig->siteRootPath(). $upFileName))
           {

                //unzip the file
                if($this->doUnzip($upFileName))
                {
                     //create a import object
                     $import=&$this->newObject('importu','contextadmin');
                    if($this->getParam('itype') ==  '1')
                    {
                        //delete the content
                        $this->objDBContentNodes->getArray("DELETE FROM tbl_context_parentnodes_has_tbl_context WHERE tbl_context_id='".$this->objDBContext->getContextId()."' ");
                        //import new content
                        $import->insertRootNode($contextId,$title,$contextCode,$path);
                    } else {
                        //import into a node
                        $import->insertToNode($this->getParam('nodeid'),$path);
                    }
                } else {
                    return FALSE;
                }

               //delete the zipped file
                unlink($this->objConfig->siteRootPath().$upFileName);

               //delete the unzipped folder

                $zip->deldir($this->objConfig->siteRootPath().'import_dir');

                return TRUE;
            }
            else
            {
                return FALSE;
            }

        } else {
            return FALSE;
        }
    }


    /**
    *Method that does the actual
    *unzipping of a file
    *@param $string $file : The full path to the file
    */
    function doUnzip($file)
    {
        $path ='import_dir'; //$this->objConfig->siteRootPath();//.
        $file = $this->objConfig->siteRootPath().$file;

        $objUnzip = & $this->newObject('wzip','utilities');
        $objUnzip->unzip($file, $path);

        //check if the file unzipped successfully
        return TRUE;
    }

    /**
    * Method to create a link to the course home
    *@return null
    */
    function getContextLinks()
    {
        $this->objIcon->setIcon("home");
        $this->objIcon->alt=$this->objLanguage->languageText("mod_context_coursehome",'contextadmin');
        $this->objIcon->align = "absmiddle";

        $this->objLink->href=$this->URI(null,'context');
        $this->objLink->link=$this->objIcon->show();
        $str = $this->objLink->show();

        return $str;
    }

     /**
    * Method to create links to the contents
    * and to the course
    *@return null
    */
    function getContentLinks()
    {
        $this->objIcon->setModuleIcon("content");
        $this->objIcon->alt=$this->objLanguage->languageText("mod_context_coursecontent,'contextadmin'");
        $this->objIcon->align = "absmiddle";

        $params = array('nodeid' => $this->getParam('nodeid'), 'action' => 'content');
        $this->objLink->href=$this->URI($params,'context');
        $this->objLink->link=$this->objIcon->show();
        $str = $this->objLink->show();

        return $str;
    }


} //end of class
?>
