<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * The controller for the cmsadmin module that extends the base controller
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Wesley  Nitsckie
 * @author Warren Windvogel
 * @author Charl Mert
 */

class cmsadmin extends controller {
    /**
     * The contextcore  object
     *
     * @access private
     * @var object
     */
    protected $_objContextCore;

    /**
     * The sections  object
     *
     * @access private
     * @var object
     */
    protected $_objSections;

    /**
     * The Content object
     *
     * @access private
     * @var object
     */
    protected $_objContent;

    /**
     * The FrontPage object
     *
     * @access private
     * @var object
     */
    protected $_objFrontPage;

    /**
     * The CMS Utilities object
     *
     * @access private
     * @var object
     */
    protected $_objUtils;

    /**
     * The user object
     *
     * @access private
     * @var object
     */
    protected $_objUser;

    /**
     * The layout object
     *
     * @access private
     * @var object
     */
    protected $_objLayout;

    /**
     * The config object
     *
     * @access private
     * @var object
     */
    protected $_objConfig;

    /**
     * The language object
     *
     * @access private
     * @var object
     */
    public $objLanguage;

    /**
     * The blocks object
     *
     * @access private
     * @var object
     */
    public $_objBlocks;

    /** the id of the current page
     *
     * @access public
     * @var object
     */
    public $currentPageId;

    /**
     * The tree menu object
     *
     * @access public
     * @var object
     */
    public $objTreeMenu;

    /**
     * The tree object
     *
     * @access public
     * @var object
     */
    public $objTreeNodes;

    /**
     * The security object
     *
     * @access public
     * @var object
     */
    public $_objSecurity;

    /**
     * The Flag object
     *
     * @access public
     * @var object
     */
    public $_objFlag;


    /**
     * The Flag Options object
     *
     * @access public
     * @var object
     */
    public $_objFlagOptions;


    /**
     * Class Constructor
     *
     * @access public
     * @return void
     */
    public function init() {
        try {
            // Supressing Prototype and Setting jQuery Version with Template Variables
            $this->setVar('SUPPRESS_PROTOTYPE', true);
            $this->setVar('SUPPRESS_JQUERY', false);
            $this->setVar('JQUERY_VERSION', '1.3.2');

            $this->_objJQuery = $this->newObject('jquery', 'jquery');

            $this->_objUserPerm = $this->getObject ('dbuserpermissions', 'cmsadmin');
            $this->_objDisplay = $this->newObject('cmsdisplay', 'cmsadmin');
            $this->_objDbUserPermissions = $this->newObject('dbuserpermissions', 'cmsadmin');
            $this->_objFlag =  $this->newObject('dbflag', 'cmsadmin');
            $this->_objFlagOptions =  $this->newObject('dbflagoptions', 'cmsadmin');
            $this->_objFlagEmail =  $this->newObject('dbflagemail', 'cmsadmin');
            $this->_objFlagUtils =  $this->newObject('flagutils', 'cmsadmin');

            $this->_objBox = $this->newObject('jqboxy', 'jquery');

            $this->_objValidate =  $this->newObject('validate', 'utilities');
            $this->_objTemplate =  $this->newObject('dbtemplate', 'cmsadmin');
            $this->_objPreview =  $this->newObject('dbcontentpreview', 'cmsadmin');
            $this->_objPageMenu =  $this->newObject('dbpagemenu', 'cmsadmin');
            $this->_objSecurity =  $this->newObject('dbsecurity', 'cmsadmin');
            $this->_objTree =  $this->newObject('cmstree', 'cmsadmin');
            $this->_objSections =  $this->newObject('dbsections', 'cmsadmin');
            $this->_objContent =  $this->newObject('dbcontent', 'cmsadmin');
            $this->_objBlocks =  $this->newObject('dbblocks', 'cmsadmin');
            $this->_objHtmlBlock =  $this->newObject('dbhtmlblock', 'cmsadmin');
            $this->_objUtils =  $this->newObject('cmsutils', 'cmsadmin');
            $this->_objLayouts =  $this->newObject('dblayouts', 'cmsadmin');
            $this->_objAdminLayout =  $this->newObject('cmsadminlayouts', 'cmsadmin');
            $this->_objUser =  $this->newObject('user', 'security');
            $this->objLanguage =  $this->newObject('language', 'language');
            $this->_objFrontPage =  $this->newObject('dbcontentfrontpage', 'cmsadmin');
            $this->_objConfig =  $this->newObject('altconfig', 'config');
            $this->objProxy = $this->newObject('proxyparser', 'utilities');
            $this->objModule = $this->newObject('modules', 'modulecatalogue');
            $this->objTreeMenu = $this->newObject('buildtree', 'cmsadmin');
            $this->objTreeNodes =  $this->newObject('treenodes', 'cmsadmin');
            $this->dbMenuStyle =  $this->newObject('dbmenustyles', 'cmsadmin');
            $this->_objCMSLayouts =  $this->newObject('cmslayouts', 'cms');

            //feeds classes
            $this->objFeed = $this->getObject('feeds', 'feed');

            if ($this->objModule->checkIfRegistered('context')) {
                $this->_objDBContext =  $this->newObject('dbcontext', 'context');
                $this->inContextMode = $this->_objDBContext->isInContext();
                $this->contextCode = $this->_objDBContext->getContextCode();
            } else {
                $this->inContextMode = FALSE;
                $this->contextCode = NULL;
            }

            //Get the activity logger class and log this module call
            $objLog = $this->getObject('logactivity', 'logger');
            $objLog->log();

            //Loading CMSAdmin Common Styles
            $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('_common.css'.'">'));

            //Loading CMSAdmin Common Styles IE <6 Fixes
            $loadie6 = '
				<!--[if lte IE 6]>
					<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('_common_ie6.css').'">
				<![endif]-->
				';
            $this->appendArrayVar('headerParams', $loadie6);

            //Loading CMSAdmin Common Styles IE 7> Fixes
            $loadie7 = '
				<!--[if gte IE 7]>
					<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('_common_ie7.css').'">
				<![endif]-->
				';
            $this->appendArrayVar('headerParams', $loadie7);

            //jQuery pngFix behaves wierdly in ies4linux but works on windows machines
            $this->_objJQuery->loadPngFixPlugin();

            //Loading the ipod style menu
            $this->_objJQuery->loadFgMenuPlugin();

            //jQuery 1.2.6 SuperFish Menu
            /*				$this->_objJQuery->loadSuperFishMenuPlugin();

				ob_start();
?>

				<script type="text/javascript"> 
				// initialise Superfish 
				jQuery(document).ready(function(){ 
						jQuery("ul.sf-menu").superfish({
							animation: {opacity:'show'},   // slide-down effect without fade-in 
							width: 300,
							delay: 0,               // 1.2 second delay on mouseout 
							speed: 'fast'
							}); 
						}); 

				</script>
<?PHP

$script = ob_get_contents();
ob_end_clean();

ob_start();
?>
<script type="text/javascript">
var simpleTreeCollection;
jQuery(document).ready(function(){
		simpleTreeCollection = jQuery('.simpleTree').simpleTree({
autoclose: true,
drag: false,
afterClick:function(node){
var turl = jQuery('.active a:first', node).attr('href');
	document.location.href = turl;
		},
		/*
afterDblClick:function(node){
		//alert("text-"+$('span:first',node).text());
		},
afterMove:function(destination, source, pos){
		//alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);
		},
afterAjax:function()
{
		//alert('Loaded');
		},
		 * /
animate:true
//,docToFolderConvert:true
});
});
</script>
<?php
$script = ob_get_contents();
ob_end_clean();

$this->appendArrayVar('headerParams', $script);

$this->_objJQuery->loadSimpleTreePlugin();
            */
        } catch (customException $e) {
            throw customException($e->getMessage());
            exit();
        }
    }

    /**
     *
     * This is a method that overrides the parent class to stipulate whether
     * the current module requires login. Having it set to false gives public
     * access to this module including all its actions.
     *
     * @access public
     * @return bool FALSE
     */
    public function requiresLogin() {
        $action = $this->getParam('action', '');
        switch ($action) {
            case 'ajaxforms':
                return FALSE;
                break;

            case 'indexallcontent':
                return FALSE;
                break;

            case 'flagcontent':
                return FALSE;
                break;

            default :
                return TRUE;
                break;
        }
    }

    /**
     * Method to handle actions from templates
     *
     * @access public
     * @param string $action Action to be performed
     * @return mixed Name of template to be viewed or function to call
     */
    public function dispatch() {
        $action = $this->getParam('action');
        $this->setLayoutTemplate('cms_layout_tpl.php');
        $this->setVar('pageSuppressXML',TRUE);
        $myid = $this->_objUser->userId();

        if (!($this->_objUser->inAdminGroup($myid,'CMSAuthors')) && !($this->_objUser->inAdminGroup($myid,'Site Admin'))) {
            $this->setVar('message', $this->objLanguage->languageText('mod_cmsadmin_nopermissions', 'cmsadmin'));
            return 'cms_nopermissions_tpl.php';
        }
        switch ($action) {

            default:
                if ($this->inContextMode) {
                    return 'cms_context_view_tpl.php';//continue;// 'cms_notincontext_view_tpl.php';
                }

                $topNav = $this->_objUtils->topNav('home');
                $cpanel =  $this->_objUtils->getControlPanel();
                $this->setVarByRef('topNav',$topNav);
                $this->setVarByRef('cpanel',$cpanel);

                return 'cms_main_tpl.php';

            case 'removetrashindex':
                $this->_objContent->removeAllTrashedIndexes();
                return 'cms_main_tpl.php';

            case 'indexallcontent':
                $this->_objContent->indexAllContent();
                return 'cms_main_tpl.php';

            case 'deleteuserperm':
                $id = $this->getParam('id');
                $this->_objUserPerm->deleteRecord($id);

                return $this->nextAction('permissionsuser', array(NULL), 'cmsadmin');

            case 'addedituserpermissions':
                $id = $this->getParam('id');
                $userId = $this->getParam('drp_owner');
                $canFrontpage = $this->getParam('chk_frontpage');

                if ($canFrontpage == 'on') {
                    $canFrontpage = TRUE;
                } else {
                    $canFrontpage = FALSE;
                }

                if ($id == '') {
                    //Adding a new user to grant/revoke rights to
                    $this->_objUserPerm->addUserPermission($userId, $canFrontpage);
                } else {
                    //Updating existing rights
                    $this->_objUserPerm->editUserPermission($id, $userId, $canFrontpage);
                }
                return $this->nextAction('permissionsuser', array(NULL), 'cmsadmin');

            case 'deleteflagemail':
                $id = $this->getParam('id');
                $this->_objFlagEmail->deleteEmail($id);

                return $this->nextAction('flag', array(NULL), 'cmsadmin');

            case 'addeditflagemail':
                $id = $this->getParam('id', '');

                $name = $this->getParam('txtName', '');
                $email = $this->getParam('txtEmail', '');

                if ($id != '') {
                    $this->_objFlagEmail->editEmail($id, $name, $email);
                } else {
                    $this->_objFlagEmail->addEmail($name, $email);
                }
                return $this->nextAction('flag', array(NULL), 'cmsadmin');

            case 'flag':
                $topNav = $this->_objUtils->topNav('flag');
                $arrFlagOptions = $this->_objFlagOptions->getOptions();
                $arrEmail = $this->_objFlagEmail->getAll();

                if ($arrFlagOptions == FALSE) {
                    $arrFlagOptions = array();
                }

                if ($arrEmail == FALSE) {
                    $arrEmail = array();
                }

                $this->setVarByRef('topNav',$topNav);
                $this->setVarByRef('arrFlagOptions', $arrFlagOptions);
                $this->setVarByRef('arrEmail', $arrEmail);
                return 'cms_flag_list_tpl.php';
                break;

            case 'flagcontent':
                $contentId = $this->getParam('id');
                $optionId = $this->getParam('flag_options');
                $this->_objFlag->addFlag($contentId, $optionId);

                $result = $this->_objFlagUtils->sendEmailAlerts($contentId, $optionId);

                $this->setVarByRef('id', $contentId);
                $this->setVarByRef('result', $result);
                $this->setLayoutTemplate('cms_single_column_layout_tpl.php');

                return 'cms_flag_success_tpl.php';

            case 'deleteflagoption':
                $id = $this->getParam('id');
                $this->_objFlagOptions->deleteOption($id);

                return $this->nextAction('flag', array(NULL), 'cmsadmin');

            case 'flagpublish':
                $id = $this->getParam('id');
                $mode = $this->getParam('mode');
                $this->_objFlagOptions->publish($id, $mode);

                return $this->nextAction('flag');

            case 'addeditflagoption':
                $id = $this->getParam('id', '');

                $title = $this->getParam('txtTitle', '');
                $text = $this->getParam('txtText', '');

                if ($id != '') {
                    $this->_objFlagOptions->editOption($id, $title, $text);
                } else {
                    $this->_objFlagOptions->addOption($title, $text);
                }
                return $this->nextAction('flag', array(NULL), 'cmsadmin');


            case 'ajaxforms':
                $this->setContentType('text/html');

                return 'cms_ajax_forms_tpl.php';

            case 'previewcontent':
            //var_dump('HERE'); exit;
                $previewId = $this->_objPreview->add();
                return $this->nextAction('previewcontent', array('id' => $previewId), 'cms');

            case 'edittemplate':
                $templateId = $this->getParam('id', null);
                $this->_objTemplate->edit();
                return $this->nextAction('templates', array('id' => $templateId), 'cmsadmin');

            case 'deletetemplate':
                $this->_objTemplate->deleteTemplate($this->getParam('id'));
                return $this->nextAction('templates', array(NULL), 'cmsadmin');

            case 'templatepublish':
                $id = $this->getParam('id');
                $mode = $this->getParam('mode');
                $this->_objTemplate->publish($id, $mode);

                return $this->nextAction('templates');

            case 'templates':
                $topNav = $this->_objUtils->topNav('viewtemplates');
                $arrTemplates = $this->_objTemplate->getTemplatePages();

                $this->setVarByRef('topNav',$topNav);
                $this->setVarByRef('arrTemplates', $arrTemplates);
                return 'cms_templates_list_tpl.php';

            case 'addtemplate':
                $templateid = $this->getParam('id');

                if ($templateid != '') {
                    //TODO: Enable Template Security
                    //Security Check for Write Access
                    //if(!$this->_objSecurity->canUserWriteTemplate($templateid)){
                    //    $this->setVarByRef('securityType', 'no_write');
                    //    return 'cms_template_no_permissions_tpl.php';
                    //}
                }

                //Checking weather user has any sections to write to
                $treeCount = $this->_objTree->getTreeCount();

                if ($treeCount == 0) {
                    $this->setVarByRef('securityType', 'no_sections');
                    return 'cms_template_no_permissions_tpl.php';
                }

                $parentid = $this->getParam('parent', NULL);
                //Get top navigation
                $topNav = $this->_objUtils->topNav('createtemplate');
                $this->setVarByRef('topNav',$topNav);
                $templateForm = $this->_objUtils->getAddEditTemplateForm($templateid, $parentid,$this->getParam('frommodule'),$this->getParam('fromaction'),$this->getParam('s_param'));
                $this->setVarByRef('templateForm', $templateForm);
                $this->setVarByRef('id', $this->getParam('id'));
                return 'cms_template_add_tpl.php';

            case 'createtemplate':

            //Get details of the new entry
                $title = $this->getParam('title');
                $description = $this->getParam('description');
                $body = $this->getParam('body');
                $imagePath = $this->getParam('imagepath',null);
                $published = ($this->getParam('published') == '1') ? 1 : 0;

                $templateId = $this->_objTemplate->addTemplate($title, $description, $body, $imagePath, $published);
                return $this->nextAction('templates', array('id' => $templateId), 'cmsadmin');

            case 'getmenuchildnodes':

                $this->setContentType('text/html');
                //Retrieve the section id from the querystring
                $id = $this->getParam('id');
                //var_dump($id);
                $objSimpleTree = $this->getObject('simplecontenttree', 'cmsadmin');
                $content = $objSimpleTree->getMenuChildNodes($id,TRUE);
                //var_dump($content);

                $this->setVar('content', $content);

                return "menu_child_node_tpl.php";

            /* ** Trash manager section ** */
            case 'trashmanager':
                $text = $this->getParam('txtfilter');
                $data = $this->_objContent->getArchivePages($text); // Get trashed content data
                $sectionData = $this->_objSections->getArchiveSections($text); // Get trashed section data
                $topNav = $this->_objUtils->topNav('trash');
                $this->setVarByRef('topNav',$topNav);
                $this->setVarByRef('data', $data);
                $this->setVarByRef('sectionData', $sectionData);
                return 'cms_trash_list_tpl.php';

            case 'restore':
                $items = $this->getParam('arrayList');
                $this->unarchiveContentPages($items);
                return $this->nextAction('trashmanager');

            case 'editmenu':

                $submit = $this->getParam('save');
                if ($submit != '') {
                    $this->_objPageMenu->addMenu();
                }

                $menuType = $this->getParam('menutype');
                $isSub = $this->getParam('sub');
                $menuId = $this->getParam('id');

                //Get top navigation
                $topNav = $this->_objUtils->topNav('editmenu');

                $this->setVarByRef('topNav',$topNav);

                if ($isSub != '1') {
                    $editMenuForm = $this->_objUtils->getEditMenuForm("Edit Main/Default Menu", $menuId, $menuType);
                } else {
                    $editMenuForm = $this->_objUtils->getEditMenuForm("Edit Sub Menu", $menuId, $menuType);
                }

                $this->setVarByRef('addEditForm', $editMenuForm);
                $this->setVarByRef('id', $this->getParam('id'));

                return 'cms_page_menu_add_tpl.php';

            case 'addmenu':
                $menuType = $this->getParam('menutype');
                $mustApply = $this->getParam('must_apply');

                $this->_objPageMenu->addMenu();

                if ($mustApply == '1') {

                    $menuType = $this->getParam('menutype');
                    $isSub = $this->getParam('sub');
                    $menuId = $this->getParam('id');

                    //Get top navigation
                    $topNav = $this->_objUtils->topNav('editmenu');

                    $this->setVarByRef('topNav',$topNav);

                    if ($isSub != '1') {
                        $editMenuForm = $this->_objUtils->getEditMenuForm("Edit Main/Default Menu", $menuId, $menuType);
                    } else {
                        $editMenuForm = $this->_objUtils->getEditMenuForm("Edit Sub Menu", $menuId, $menuType);
                    }

                    $this->setVarByRef('addEditForm', $editMenuForm);
                    $this->setVarByRef('id', $this->getParam('id'));

                    return 'cms_page_menu_add_tpl.php';


                }

                //Displaying the menustyle page
                $data = $this->dbMenuStyle->getStyles();
                $topNav = $this->_objUtils->topNav('menu');
                $this->setVarByRef('topNav', $topNav);
                $this->setVarByRef('data', $data);

                return 'cms_menu_switch_tpl.php';

            case 'deletemenu':

                $menuType = $this->getParam('menutype');
                $isSub = $this->getParam('sub');
                $menuId = $this->getParam('id');

                //Deleting the Menu Item
                $this->_objPageMenu->deleteMenu($menuId);


                //Get top navigation
                $topNav = $this->_objUtils->topNav('editmenu');

                $this->setVarByRef('topNav',$topNav);

                if ($isSub != '1') {
                    $editMenuForm = $this->_objUtils->getEditMenuForm("Edit Main/Default Menu", $menuId, $menuType);
                } else {
                    $editMenuForm = $this->_objUtils->getEditMenuForm("Edit Sub Menu", $menuId, $menuType);
                }

                $this->setVarByRef('addEditForm', $editMenuForm);
                $this->setVarByRef('id', $this->getParam('id'));

                return 'cms_page_menu_add_tpl.php';

            case 'restoresections':
                $items = $this->getParam('arrayList');
                $this->unarchiveSections($items);
                return $this->nextAction('trashmanager');

            //----------------------- section section
            case 'sections':
                $this->getsections();
                return 'cms_section_list_tpl.php';
            //return 'cms_section_grid_tpl.php'; //New Grid Based Layout

            //Returning JSON grid for sections manager
            case 'jsonsectiongrid':

            //$this->setPageTemplate('');
            //$this->setLayoutTemplate('');
            //$this->setContentType('application/x-json');
                $this->setContentType('text/html');
                return 'cms_section_json_tpl.php';

            // User Specific Permissions
            case 'permissionsuser':

            //Security Check
                if (!$this->_objUserPerm->canEditUserPermissions()) {
                    return 'cms_nopermissions_tpl.php';
                }

                //Boxy New Form
                $innerHtml = $this->_objDisplay->getAddUserPermissionsForm();
                $this->_objBox->setHtml($innerHtml);
                $this->_objBox->setTitle('Add User Permission');
                $this->_objBox->attachClickEvent('btn_new');

                $arrUserPermissions = $this->_objDbUserPermissions->getAll();
                $this->setVarByRef('arrUserPermissions', $arrUserPermissions);
                return 'cms_permissions_user_list_tpl.php';

            // Simpler view of the sections list with permissions management at the core
            case 'permissions':
                $this->getsections(null, null, 'permissions');
                return 'cms_permissions_list_tpl.php';

            case 'setpublicaccess':
                $id = $this->getParam('id');
                $cid = $this->getParam('cid');
                $mode = $this->getParam('mode');

                if ($id != '') {
                    $this->_objSecurity->setSectionPublicAccess($id, $mode);
                }

                if ($cid != '') {
                    $this->_objSecurity->setContentPublicAccess($cid, $mode);
                }

                $subView = $this->getParam('subview');
                if ($subView == 1) {
                    $parentId = $this->getParam('parent');
                    $params = array('id' => "$parentId");
                    return $this->nextAction('view_permissions_section', $params, 'cmsadmin');
                }

                $sectionId = $this->getParam('sectionid');
                if(!empty($sectionId)) {
                    return $this->nextAction('viewsection', array('id' => $sectionId));
                }
                return $this->nextAction('permissions');
            //return 'cms_permissions_list_tpl.php';


            case 'viewsection':
                $this->viewsections();
                return 'cms_section_view_tpl.php';

            case 'ajaxviewsection':
                $this->viewsections(); //Hiding the left nav
                $this->setContentType('text/html'); //Hiding the template
                $this->setVarByRef('hideLeftColumn', true);
                return 'cms_section_view_tpl.php';

            case 'changesectionorder':
            //Get the sections details
                $id = $this->getParam('id');
                $ordering = $this->getParam('ordering');
                $sectionId = $this->getParam('parent');
                //Change the ordering
                $this->_objSections->changeOrder($id, $ordering, $sectionId);

                if (!empty($sectionId)) {
                    return $this->nextAction('viewsection', array('id' => $sectionId), 'cmsadmin');
                } else {
                    return $this->nextAction('sections', array(NULL), 'cmsadmin');
                }

            case 'addsection':
                if (!isset($sectionid) ) {
                    $sectionid = $this->getParam('id');
                }
                if (!isset($parentid)) {
                    $parentid = $this->getParam('sectionid');
                }

                $this->addEditsection($sectionid, $parentid);
                return 'cms_section_add_tpl.php';

            case 'addpermissions':
                $sectionid = $this->getParam('id');

                $this->addEditSectionPermissions($sectionid);

                $submitButton = $this->getParam('save_btn');
                if ($submitButton == 'Save') {
                    $this->getsections(null, null, 'permissions');
                    return 'cms_permissions_list_tpl.php';
                } else {
                    return 'cms_permissions_add_tpl.php';
                }

            case 'add_content_permissions':
                $contentid = $this->getParam('id');

                $button = $this->getParam('save_btn');
                if ($button != 'Save') {
                    if ($sectionid == null) {
                        $sectionid = $this->getParam('id');
                    }
                } else {
                    if ($sectionid == null) {
                        $sectionid = $this->getParam('parent');
                    }
                    $contentid = $this->getParam('id');
                }

                $this->processContentPermissionsMemberForm($contentid);

                $this->addEditContentPermissions($contentid);
                $parent = $this->getParam('parent');
                $button = $this->getParam('save_btn');

                //if ($button == 'Save'){
                //return $this->nextAction('view_permissions_section',array('modname'=>'cmsadmin', 'id' => $contentid, 'parent' => $parent), 'redirect');
                //}

                return 'cms_permissions_content_add_tpl.php';


            case 'add_section_members':
                $sectionid = $this->getParam('id');
                //Updating the permission members for SECTIONS
                $this->processSectionPermissionsMemberForm($sectionid);

                //Taking User back to the Section Permissions Form
                $this->addEditSectionPermissions($sectionid);

                return 'cms_permissions_add_tpl.php';

            case 'view_permissions_section':
                $returnSubView = $this->getParam('subview');
                $sectionid = $this->getParam('cid');

                if ($returnSubView == 1) {
                    //Processing the subview permissions changes
                    $this->addEditSectionPermissions($sectionid, $returnSubView);
                    //$this->processSectionPermissionsMemberForm($sectionid);

                }

                $this->viewPermissionsSections();

                return 'cms_permissions_view_tpl.php';

            case 'addpermissions_content_user_group':
                $this->addEditContentPermissionsUserGroup($sectionid);

                return 'cms_permissions_content_add_user_group_tpl.php';

            case 'addpermissions_user_group':
                $sectionId = $this->getParam('id');
                $this->addEditSectionPermissionsUserGroup($sectionId);
                return 'cms_permissions_add_user_group_tpl.php';

            case 'createsection':
            //Save the section
                $parentId = $this->getParam('parent');
                $title = $this->getParam('title');
                //$menuText = $this->getParam('menutext');
                $menuText = $title;
                $access = $this->getParam('access');
                $description = $this->getParam('introtext');
                $published = $this->getParam('published');
                $layout = $this->getParam('display');
                $showIntroduction = $this->getParam('show_introduction');
                $showTitle = $this->getParam('show_title');
                $showAuthor = $this->getParam('show_author');
                $showDate = $this->getParam('show_date');
                $customNum = $this->getParam('customnumber');
                $pageNum = $this->getParam('pagenum');
                $pageOrder = $this->getParam('pageorder');
                $imageUrl = '';
                $contextCode = '';


                $this->_objSections->addSection($title,
                        $parentId,
                        $menuText,
                        $access,
                        $description,
                        $published,
                        $layout,
                        $showIntroduction,
                        $showTitle,
                        $showAuthor,
                        $showDate,
                        $pageNum,
                        $customNum,
                        $pageOrder,
                        $imageUrl,
                        $contextCode);

                //$this->_objSections->add();

                $parent = $this->getParam('parentid');
                if (!empty($parent)) {
                    return $this->nextAction('viewsection', array('id' => $parent), 'cmsadmin');
                } else {
                    return $this->nextAction('sections');
                }

            case 'editsection':

            //Edit the section
                $id = $this->getParam('id');
                $parentId = $this->getParam('parent');
                $rootId = $this->getParam('rootid');
                $title = $this->getParam('title');
                //$menuText = $this->getParam('menutext');
                $menuText = $title;
                $access = $this->getParam('access');
                $description = $this->getParam('introtext');
                $published = $this->getParam('published');
                $layout = $this->getParam('display');
                $showIntroduction = $this->getParam('show_introduction');
                $showTitle = $this->getParam('show_title');
                $showAuthor = $this->getParam('show_author');
                $showDate = $this->getParam('show_date');
                $customNum = $this->getParam('customnumber');
                $pageNum = $this->getParam('pagenum');
                $pageOrder = $this->getParam('pageorder');
                $imageUrl = '';
                $contextCode = '';

                $this->_objSections->editSection($id,
                        $parentId,
                        $rootId,
                        $title,
                        $menuText,
                        $access,
                        $description,
                        $published,
                        $layout,
                        $showIntroduction,
                        $showTitle,
                        $showAuthor,
                        $showDate,
                        $pageNum,
                        $customNum,
                        $pageOrder,
                        $imageUrl,
                        $contextCode);
                return $this->nextAction('viewsection', array('id' => $id), 'cmsadmin');

            case 'select':
                $item = $this->getParam('arrayList');
                $task = $this->getParam('task');
                $this->processSelection($item, $task);
                return $this->nextAction('sections');

            case 'filter':
                $text = $this->getParam('txtfilter',null);
                $drop_filter = $this->getParam('drp_filter',null);

                //echo '<pre>'; print_r($_POST); echo '</pre>';
                $publish = FALSE;
                if($drop_filter == 'published') {
                    $publish = '1';
                }else if($drop_filter == 'unpublished') {
                    $publish = '0';
                }

                $arrSections = $this->_objSections->getFilteredSections($text, $publish);

                /*get filtered sections
                                                                  if ($txt_filter!=null) {
                                                                  $arrSections = $this->_objSections->getFilteredSections(FALSE,$txt_filter);
                                                                  }elseif ($drop_filter!='select'){
                                                                  $arrSections = $this->_objSections->getFilteredSections(TRUE,$txt_filter);
                                                                  }
                */

                $topNav = $this->_objUtils->topNav('sections');
                $this->setVarByRef('topNav',$topNav);
                $this->setVarByRef('arrSections', $arrSections);
                $this->setVar('viewType', 'root');
                return 'cms_section_list_tpl.php';

            case 'filter_permissions':
                $text = $this->getParam('txtfilter',null);
                $drop_filter = $this->getParam('drp_filter',null);

                //echo '<pre>'; print_r($_POST); echo '</pre>';
                $publish = FALSE;
                if($drop_filter == 'published') {
                    $publish = '1';
                }else if($drop_filter == 'unpublished') {
                    $publish = '0';
                }

                $arrSections = $this->_objSections->getFilteredSections($text, $publish);

                /*get filtered sections
                                                                  if ($txt_filter!=null) {
                                                                  $arrSections = $this->_objSections->getFilteredSections(FALSE,$txt_filter);
                                                                  }elseif ($drop_filter!='select'){
                                                                  $arrSections = $this->_objSections->getFilteredSections(TRUE,$txt_filter);
                                                                  }
                */

                $topNav = $this->_objUtils->topNav('permissions');
                $this->setVarByRef('topNav',$topNav);
                $this->setVarByRef('arrSections', $arrSections);
                $this->setVar('viewType', 'root');
                return 'cms_permissions_list_tpl.php';

            case 'sectionpublish':
                $id = $this->getParam('id');
                $mode = $this->getParam('mode');
                $this->_objSections->publish($id, $mode);

                $sectionId = $this->getParam('sectionid');
                if(!empty($sectionId)) {
                    return $this->nextAction('viewsection', array('id' => $sectionId));
                }
                return $this->nextAction('sections');

            case 'deletesection':
                $this->_objSections->deleteSection($this->getParam('id'));
                return $this->nextAction('sections', array(NULL), 'cmsadmin');

            case 'removesection':
                $this->_objSections->permanentlyDelete($this->getParam('id'));
                return $this->nextAction('trashmanager');

            //----------------------- front page section
            case 'frontpages':
            //Frontpage Security
                if (!$this->_objUserPerm->canAddToFrontPage()) {
                    $message = $this->objLanguage->languageText('mod_cmsadmin_nofrontpageaccess', 'cmsadmin');
                    $this->setVarByRef('message',$message);
                    //$this->setVarByRef('type',$type);

                    return 'cms_nopermissions_tpl.php';
                }

                $topNav = $this->_objUtils->topNav('frontpage');
                $this->setVarByRef('topNav',$topNav);
                $this->setVar('files', $this->_objFrontPage->getFrontPages());
                return 'cms_frontpage_manager_tpl.php';

            case 'publishfrontpage':
                $list = $this->getParam('arrayList');
                $task = $this->getParam('task');
                $this->publishFrontContent($list, $task);
                return $this->nextAction('frontpages');

            case 'removefromfrontpage':
                $id = $this->getParam('id');
                $this->_objFrontPage->remove($id);
                return $this->nextAction('frontpages', array(NULL), 'cmsadmin');

            case 'changefpstatus':
                $id = $this->getParam('id');
                $sectionId = $this->getParam('sectionid');
                $mode = $this->getParam('mode');

                $this->_objFrontPage->changeStatus($id, $mode);
                return $this->nextAction('viewsection', array('id' => $sectionId), 'cmsadmin');

            case 'changefporder':
            //Get front page details
                $id = $this->getParam('id');
                $pos = $this->getParam('position');
                $ordering = $this->getParam('ordering');

                //Change the ordering on the front page
                $this->_objFrontPage->changeOrder($id, $ordering, $pos);
                return $this->nextAction('frontpages', array(NULL), 'cmsadmin');

            //----------------------- content section

            case 'ajaxaddcontent':
                $contentid = $this->getParam('id');

                if ($contentid != '') {
                    //Security Check for Write Access
                    if(!$this->_objSecurity->canUserWriteContent($contentid)) {
                        $this->setVarByRef('securityType', 'no_write');
                        return 'cms_content_no_permissions_tpl.php';
                    }
                }

                //Checking weather user has any sections to write to
                $treeCount = $this->_objTree->getTreeCount();

                if ($treeCount == 0) {
                    $this->setVarByRef('securityType', 'no_sections');
                    return 'cms_content_no_permissions_tpl.php';
                }

                $parentid = $this->getParam('parent', NULL);
                //Get top navigation
                $topNav = $this->_objUtils->topNav('createcontent');
                $this->setVarByRef('topNav',$topNav);
                $addEditForm = $this->_objUtils->getAddEditContentForm($contentid, $parentid,$this->getParam('frommodule'),$this->getParam('fromaction'),$this->getParam('s_param'));
                $this->setVarByRef('addEditForm', $addEditForm);
                $this->setVarByRef('section', $parentid);
                $this->setVarByRef('id', $this->getParam('id'));

                $this->setContentType('text/html');
                $this->setLayoutTemplate('cms_ajax_layout_tpl.php');

                $this->setVarByRef('hideLeftColumn', true);
                return 'cms_content_add_tpl.php';

            case 'addcontent':
                $contentid = $this->getParam('id');

                if ($contentid != '') {
                    //Security Check for Write Access
                    if(!$this->_objSecurity->canUserWriteContent($contentid)) {
                        $this->setVar('securityType', 'no_write');
                        return 'cms_content_no_permissions_tpl.php';
                    }
                }

                //Checking weather user has any sections to write to
                $treeCount = $this->_objTree->getTreeCount();

                if ($treeCount == 0) {
                    $this->setVar('securityType', 'no_sections');
                    return 'cms_content_no_permissions_tpl.php';
                }

                $parentid = $this->getParam('parent', NULL);
                //Get top navigation
                $topNav = $this->_objUtils->topNav('createcontent');
                $this->setVar('topNav',$topNav);
                $addEditForm = $this->_objUtils->getAddEditContentForm($contentid, $parentid,$this->getParam('frommodule'),$this->getParam('fromaction'),$this->getParam('s_param'));
                $this->setVar('addEditForm', $addEditForm);
                $this->setVar('section', $parentid);
                $this->setVar('id', $this->getParam('id'));
                return 'cms_content_add_tpl.php';


            case 'createcontent':
            //Save the content page

            //Get details of the new entry
                $title = $this->getParam('title');
                $sectionId = $this->getParam('parent');
                $published = ($this->getParam('published') == '1') ? 1 : 0;
                $override_date = $this->getParam('overide_date',null);
                $start_publish = $this->getParam('publish_date',null);
                $end_publish = $this->getParam('end_date',null);
                $creatorid = $this->getParam('creator',null);

                $show_title = $this->getParam('show_title','g');
                $show_author = $this->getParam('show_author','g');
                $show_date = $this->getParam('show_date','g');

                $show_pdf = $this->getParam('show_pdf','g');
                $show_email = $this->getParam('show_email','g');
                $show_print = $this->getParam('show_print','g');

                $show_flag = $this->getParam('show_flag','g');

                $access = $this->getParam('access');
                $created_by = $this->getParam('title_alias',null);
                $introText = $this->getParam('intro');
                $fullText = $this->getParam('body');
                $metakey = $this->getParam('keyword',null);
                $metadesc = $this->getParam('description',null);
                $ccLicence = $this->getParam('creativecommons',null);

                $this->_objContent->addContent( $title,
                        $published,
                        $override_date,
                        $start_publish,
                        $end_publish,
                        $creatorid,
                        $show_title,
                        $show_author,
                        $show_date,
                        $show_pdf,
                        $show_email,
                        $show_print,
                        $show_flag,
                        $access,
                        $created_by,
                        $introText,
                        $fullText,
                        $metakey,
                        $metadesc,
                        $ccLicence,
                        $sectionId);

                if(!empty($sectionId)) {
                    return $this->nextAction('viewsection', array('id' => $sectionId), 'cmsadmin');
                } else {
                    return $this->nextAction('frontpages', array('filter' => 'trash'));
                }

            case 'editcontent':

                $contentId = $this->getParam('id');
                $title = $this->getParam('title');
                $sectionId = $this->getParam('parent');
                $published = $this->getParam('published');
                $access = $this->getParam('access');
                $introText = $this->getParam('intro');
                $fullText = $this->getParam('body');
                $override_date = $this->getParam('overide_date',null);
                $start_publish = $this->getParam('publish_date',null);
                $end_publish = $this->getParam('end_date',null);
                $access = $this->getParam('access');
                $metakey = $this->getParam('keyword',null);
                $metadesc = $this->getParam('description',null);
                $ccLicence = $this->getParam('creativecommons');

                $show_title = $this->getParam('show_title','g');
                $show_author = $this->getParam('show_author','g');
                $show_date = $this->getParam('show_date','g');

                $show_pdf = $this->getParam('show_pdf','g');
                $show_email = $this->getParam('show_email','g');
                $show_print = $this->getParam('show_print','g');
                $show_flag = $this->getParam('show_flag','g');

                //Validation
                $valid = $this->_objValidate->valRequired($contentId);
                $this->setVar('cmsErrorMessage', 'Content ID Wasn\'t Specified');
                if ($valid) {
                    $valid = $this->_objValidate->valRequired($title);
                    $this->setVar('cmsErrorMessage', 'Please specify a title');
                } else if ($valid) {
                    $valid = $this->_objValidate->valRequired($fullText);
                    $this->setVar('cmsErrorMessage', 'Please enter some text in the body');
                } else if ($valid) {
                    //Fetching the parent from the db if non submitted
                    if ($sectionId == '') {
                        $contentRecord = $this->_objContent->getContentPage($contentId);
                        $sectionId = $contentRecord['sectionid'];
                    }
                    $valid = $this->_objValidate->valRequired($sectionId);
                    $this->setVar('cmsErrorMessage', 'No section id was specified. Please select a section.');
                }

                if ($valid) {
                    $this->_objContent->editContent($contentId ,
                            $title ,
                            $sectionId ,
                            $published ,
                            $access ,
                            $introText ,
                            $fullText ,
                            $override_date ,
                            $start_publish ,
                            $end_publish ,
                            $metakey ,
                            $metadesc ,
                            $ccLicence ,
                            $show_title ,
                            $show_author ,
                            $show_date ,
                            $show_pdf ,
                            $show_email ,
                            $show_print,
                            $show_flag);

                    $this->setVar('cmsErrorMessage', '');
                } else {
                    return $this->nextAction('addcontent', array('id' => $contentId), 'cmsadmin');
                }

                $mustApply = $this->getParam('must_apply');

                $is_front = $this->getParam('frontman', FALSE);
                $fromAction = $this->getParam('fromaction');
                $fromModule = $this->getParam('frommodule',FALSE);

                if ($mustApply == '1') {
                    return $this->nextAction('addcontent', array('id' => $contentId, 'frontman' => $is_front), 'cmsadmin');
                }

                if ($fromModule && $fromModule != "") {
                    if ($fromAction == "") {
                        $fromAction = NULL;
                    }
                    $s_param = $this->getParam('s_param');
                    $a_param = array();
                    if ($s_param) {
                        $a_param = unserialize($s_param);
                    }
                    return $this->nextAction($fromAction,$a_param,$fromModule);
                }
                if (!empty($sectionId) && !$is_front) {
                    return $this->nextAction('viewsection', array('id' => $sectionId), 'cmsadmin');
                } else if ($is_front) {
                    return $this->nextAction('frontpages', array(), 'cmsadmin');
                } else {
                    return $this->nextAction('default', array(), 'cmsadmin');
                }
                break;

            case 'viewcontent':
                $id = $this->getParam('id');
                $sectionId = $this->getParam('sectionid');
                return $this->nextAction('addcontent', array('id' => $id, 'parent' =>$sectionId));
            /*$this->_objContent->edit();

                                                                  if (!empty($sectionId)) {
                                                                  return $this->nextAction('viewsection', array('id' => $sectionId), 'cmsadmin');
                                                                  } else {
                                                                  return $this->nextAction('frontpages', array('action' => 'frontpages'), 'cmsadmin');
                                                                  }*/

            case 'contentpublish':
                $id = $this->getParam('id');
                $mode = $this->getParam('mode');
                $this->_objContent->publish($id, $mode);

                $sectionId = $this->getParam('sectionid');
                if(!empty($sectionId)) {
                    return $this->nextAction('viewsection', array('id' => $sectionId));
                }
                return $this->nextAction('frontpages');

            case 'trashcontent':
                $sectionId = $this->getParam('sectionid', NULL);
                $return = $this->_objContent->trashContent($this->getParam('id'));

                if (!empty($sectionId)) {
                    return $this->nextAction('viewsection', array('id' => $sectionId), 'cmsadmin');
                }
                return $this->nextAction('frontpages');

            case 'deletecontent':
                $this->_objContent->deleteContent($this->getParam('id'), 'controller.php');
                $sectionId = $this->getParam('sectionid', NULL);

                return $this->nextAction('trashmanager');
                if (!empty($sectionId)) {
                    return $this->nextAction('viewsection', array('id' => $sectionId), 'cmsadmin');
                } else {
                    return $this->nextAction('frontpages', array('filter' => 'trash'));
                }

            case 'changecontentorder':
            //Get content details
                $id = $this->getParam('id');
                $ordering = $this->getParam('ordering');
                $sectionId = $this->getParam('sectionid');
                //Change content order
                $this->_objContent->changeOrder($sectionId, $id, $ordering);
                return $this->nextAction('viewsection', array('id' => $sectionId), 'cmsadmin');

            case 'addblock':
                $blockCat = $this->getParam('blockcat', FALSE);
                $sectionId = $this->getParam('sectionid', NULL);
                $pageId = $this->getParam('pageid', NULL);
                $closePage = $this->getParam('closePage', FALSE);
                $blockForm  = $this->_objBlocks->getPositionBlockForm($pageId, $sectionId, $blockCat);

                $this->setVarByRef('closePage', $closePage);
                $this->setVarByRef('blockForm', $blockForm);
                return 'cms_blocks_tpl.php';

            case 'positionblock':
                $blockCat = $this->getParam('blockcat', FALSE);
                $sectionId = $this->getParam('sectionid', NULL);
                $pageId = $this->getParam('pageid', NULL);
                $closePage = $this->getParam('closePage', FALSE);
                $blockForm  = $this->_objBlocks->getPositionBlockForm($pageId, $sectionId, $blockCat);

                $this->setVarByRef('closePage', $closePage);
                $this->setVarByRef('blockForm', $blockForm);
                return 'cms_blocks_tpl.php';

            case 'saveblock':
                $blockCat = $this->getParam('blockcat', NULL);
                //Get the page id of the page to add the block to
                $pageId = $this->getParam('pageid', NULL);
                //Get the section id of the page to add the block to
                $sectionId = $this->getParam('sectionid', NULL);

                if ($blockCat == 'frontpage') {
                    //Get blocks on the frontpage
                    $currentBlocks = $this->_objBlocks->getBlocksForFrontPage();
                } else if ($blockCat == 'content') {
                    //Get all blocks already on the page
                    $currentBlocks = $this->_objBlocks->getBlocksForPage($pageId);
                } else {
                    //Get all blocks already on the section
                    $currentBlocks = $this->_objBlocks->getBlocksForSection($sectionId);
                }

                //Get all available blocks
                $blocks = $this->_objBlocks->getBlockEntries();

                foreach($blocks as $block) {
                    $exists = FALSE;
                    $blockName = $block['blockname'];
                    $blockId = $block['id'];

                    //Charl Mert : Added Positioning
                    $position = $this->getParam('position_'.$blockId, '1');

                    if(!empty($currentBlocks)) {
                        foreach($currentBlocks as $cb) {
                            if($cb['blockid'] == $blockId) {
                                $exists = TRUE;
                            }
                        }
                    }
                    //var_dump($blockId . ' check: [' . $this->getParam($blockId) . '] <br/>');
                    //Deleting all blocks that match the given category
                    $this->_objBlocks->deleteAllBlocks($pageId, $sectionId, $blockId, $blockCat);

                    //Get all blocks to be added
                    if($this->getParam($blockId) == 'on') {
                        //Check if it already exists before adding it
                        //if(!$exists) {
                        $this->_objBlocks->editPosition($pageId, $sectionId, $blockId, $blockCat, $position);
                        //}
                        //If block isn't in list to be added check if it exists and delete it
                    } else {
                        //var_dump('deleting pageId[' . $pageId . '] => blockId[' . $blockId . '] => blockCat[' . $blockCat . '] => check: [' . $this->getParam($blockId) . '] <br/>');

                        //if($this->getParam($blockId) != 'on') {
                        //$this->_objBlocks->deleteBlockExplicit($pageId, $sectionId, $blockId, $blockCat);
                        //}
                    }
                }

                $loadPosition = $this->getParam('loadposition', '');

                if ($loadPosition = '1') {
                    $action = 'positionblock';
                } else {
                    $action = 'addblock';
                }

                if ($blockCat == 'frontpage') {
                    return $this->nextAction($action, array('blockcat' => 'frontpage'), 'cmsadmin');
                } else if ($blockCat == 'content') {
                    //Get all blocks already on the page
                    return $this->nextAction($action, array('blockcat' => 'content', 'pageid' => $pageId), 'cmsadmin');
                } else {
                    //Get all blocks already on the section
                    return $this->nextAction($action, array('blockcat' => 'section', 'sectionid' => $sectionId), 'cmsadmin');
                }


            case 'changeblocksorder':
            //Get block entry details
                $id = $this->getParam('id');
                $ordering = $this->getParam('ordering');
                $pageId = $this->getParam('pageid', NULL);
                $sectionId = $this->getParam('sectionid', NULL);
                //Change order of blocks on page
                $this->_objBlocks->changeOrder($id, $ordering, $pageId, $sectionId);
                if ($blockCat == 'frontpage') {
                    return $this->nextAction('addblock', array('blockcat' => 'frontpage'), 'cmsadmin');
                } else if ($blockCat == 'content') {
                    //Get all blocks already on the page
                    return $this->nextAction('addblock', array('blockcat' => 'content', 'pageid' => $pageId), 'cmsadmin');
                } else {
                    //Get all blocks already on the section
                    return $this->nextAction('addblock', array('blockcat' => 'section', 'sectionid' => $sectionId), 'cmsadmin');
                }

            /* *** Add / remove blocks *** */

            case 'adddynamicpageblock':
                $pageId = $this->getParam('pageid');
                $blockId = $this->getParam('blockid');
                $this->_objBlocks->add($pageId, NULL, $blockId, 'content');
                echo $this->createReturnBlock($blockId, 'usedblock');
                break;

            case 'addleftpageblock':
                $pageId = $this->getParam('pageid');
                $blockId = $this->getParam('blockid');
                $this->_objBlocks->add($pageId, NULL, $blockId, 'content', 1);
                echo $this->createReturnBlock($blockId, 'leftblocks');
                break;

            case 'removedynamicpageblock':
                $pageId = $this->getParam('pageid');
                $blockId = $this->getParam('blockid');
                $this->_objBlocks->deleteBlock($pageId, NULL, $blockId, 'content');
                echo $this->createReturnBlock($blockId, 'addblocks');
                break;

            case 'adddynamicfrontpageblock':
                $blockId = $this->getParam('blockid');
                $this->_objBlocks->add(NULL, NULL, $blockId, 'frontpage');
                echo $this->createReturnBlock($blockId, 'usedblock');
                break;

            case 'addleftfrontpageblock':
                $blockId = $this->getParam('blockid');
                $this->_objBlocks->add(NULL, NULL, $blockId, 'frontpage', 1);
                echo $this->createReturnBlock($blockId, 'leftblocks');
                break;

            case 'removedynamicfrontpageblock':
                $blockId = $this->getParam('blockid');
                $this->_objBlocks->deleteBlock(NULL, NULL, $blockId, 'frontpage');
                echo $this->createReturnBlock($blockId, 'addblocks');
                break;

            case 'adddynamicsectionblock':
                $sectionId = $this->getParam('sectionid');
                $blockId = $this->getParam('blockid');
                $this->_objBlocks->add('', $sectionId, $blockId, 'section');
                echo $this->createReturnBlock($blockId, 'usedblock');
                break;

            case 'addleftsectionblock':
                $sectionId = $this->getParam('sectionid');
                $blockId = $this->getParam('blockid');
                $this->_objBlocks->add('', $sectionId, $blockId, 'section', 1);
                echo $this->createReturnBlock($blockId, 'leftblocks');
                break;

            case 'removedynamicsectionblock':
                $sectionId = $this->getParam('sectionid');
                $blockId = $this->getParam('blockid');
                $this->_objBlocks->deleteBlock('', $sectionId, $blockId, 'section');
                echo $this->createReturnBlock($blockId, 'addblocks');
                break;

            /* *** Rss functions *** */

            case 'uploadimage':
                return 'cms_attachment_popup.php';
                break;
            case 'createfeed':

                return 'rssedit_tpl.php';
                break;
            case 'addrss':
                $this->addRss();
                $this->nextAction('createfeed');
                break;

            case 'rssedit':
                $mode = $this->getParam('mode');
                $rssname = $this->getParam('name');
                $rssurl = $this->getParam('rssurl');
                $rssdesc = $this->getParam('description');
                $userid = $this->_objUser->userId();
                $id = $this->getParam('id');

                if($mode == 'edit') {
                    //	         			$addarr = array('id' => $id, 'userid' => $userid, 'url' => $rssurl, 'name' => $rssname, 'description' => $rssdesc);
                    //	        			$this->_objLayouts->addRss($addarr, 'edit');
                    $id = $this->getParam('id');
                    $rdata = $this->_objLayouts->getRssById($id);
                    $this->setVarByRef('rdata', $rdata);
                }
                $userid = $this->_objUser->userid();
                $this->setVarByRef('userid', $userid);
                return 'rssedit_tpl.php';
                break;

            case 'deleterss':
                $id = $this->getParam('id');

                $this->_objLayouts->delRSS($id);
                $this->nextAction('rssedit');
                break;

            // Switch menu style - Megan Watson 26/03/2007
            case 'menustyle':
                $objSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
                if ($objSysconfig->getValue('admin_only_menu', 'cmsadmin') == 'TRUE') {
                    if (!$this->_objUser->inAdminGroup($this->_objUser->userId())) {
                        $this->setVar('message', $this->objLanguage->languageText('mod_cmsadmin_nomenupermissions', 'cmsadmin'));
                        return 'cms_nopermissions_tpl.php';
                    }
                }
                $data = $this->dbMenuStyle->getStyles();
                $topNav = $this->_objUtils->topNav('menu');
                $this->setVarByRef('topNav', $topNav);
                $this->setVarByRef('data', $data);
                return 'cms_menu_switch_tpl.php';
                break;

            case 'updatemenustyle':
                $objSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
                if ($objSysconfig->getValue('admin_only_menu', 'cmsadmin') == 'TRUE') {
                    if (!$this->_objUser->inAdminGroup($this->_objUser->userId())) {
                        $this->setVar('message', $this->objLanguage->languageText('mod_cmsadmin_nomenupermissions', 'cmsadmin'));
                        return 'cms_nopermissions_tpl.php';
                    }
                }
                $styleId = $this->getParam('style');
                $this->dbMenuStyle->updateActive($styleId);
                $this->_objCMSLayouts->getMenuStyle(TRUE);
                return $this->nextAction('menustyle');
                break;

            case 'configleftblocks':
                $block = $this->_objHtmlBlock->getBlock($this->contextCode);
                $topNav = $this->_objUtils->topNav('menu');
                $this->setVarByRef('topNav', $topNav);
                $this->setVarByRef('block', $block);
                return 'cms_config_leftblocks_tpl.php';
                break;

            case 'createblock':
                $id = $this->getParam('id');
                $this->_objHtmlBlock->updateBlock($id);
                return $this->nextAction('configleftblocks');
                break;

            // End switch menu style

            case 'managemenus':
                $pageId = $this->getParam('pageid',null);
                $content = $this->objTreeNodes->getRootNodes();
                $this->setVar('content',$content);
                $this->currentPageId = $pageId;
                $this->setVar('pageId', $pageId);
                $topNav = $this->_objUtils->topNav('menu');
                $this->setVarByRef('topNav',$topNav);
                return 'cms_menu_list_tpl.php';
                break;
            case 'savemenu':
                if (($this->getParam('published') == 'on') || ($this->getParam('published') == TRUE)) {
                    $published = 1;
                } else {
                    $published = 0;
                }
                $pageId = $this->getParam('id');
                $this->objTreeNodes->edit($pageId, $this->getParam('title'), $this->getParam('nodetype'), $this->getParam('linkreference'), $this->getParam('banner'), $this->getParam('parentid'), $this->getParam('layout'), $this->getParam('css'), $published, $this->getParam('publisherid'), $this->getParam('ordering'));
                return $this->nextAction('managemenus', array('pageid'=>$pageId), 'cmsadmin');
            case 'moveup':
                $pageId = $this->getParam('pageid');
                $this->objTreeNodes->moveNodeUp($pageId);
                return $this->nextAction('managemenus', array('pageid'=>$pageId), 'cmsadmin');
            case 'movedown':
                $pageId = $this->getParam('pageid');
                $this->objTreeNodes->moveNodeDown($pageId);
                return $this->nextAction('managemenus', array('pageid'=>$pageId), 'cmsadmin');
            case 'addnewmenu':
                $this->currentPageId = $this->getParam('pageid',NULL);
                return $this->menuAdd();
            case 'addmenu':
                try {
                    if (($this->getParam('published') == 'on') || ($this->getParam('published') == TRUE)) {
                        $published = 1;
                    } else {
                        $published = 0;
                    }
                    $orderNum = $this->objTreeNodes->getNewOrderNum($this->getParam('parentid'));
                    $this->objTreeNodes->add($this->getParam('title'), $this->getParam('nodetype'), $this->getParam('linkreference'), $this->getParam('banner'), $this->getParam('parentid'), $this->getParam('layout'), $this->getParam('css'), $published, $this->getParam('publisherid'), $orderNum);

                    $pageId = $this->objTreeNodes->getLastInsertId();
                    return $this->nextAction('addnewmenu', array('pageid'=>$pageId), 'cmsadmin');
                }catch (Exception $e) {
                    //if add fails do this
                    return $this->nextAction('addnewmenu', array(), 'cmsadmin');
                }

            case 'save':
                $this->objContent->edit();
                return $this->nextAction('addnewmenu', array('pageid'=>$pageId), 'cmsadmin');
                break;

            case 'deletemenu':
                $heading = '<h1><center>'.$this->objLanguage->languageText('phrase_menuadmin').'</center></h1><br /><br />';
                $this->setVarByRef('heading', $heading);

                $pageId = $this->getParam('pageid');
                if (!isset($pageId)) {
                    $pageId = $this->getParam('id');
                }
                $node = $this->objTreeNodes->getNode($pageId);
                //var_dump($node);
                if(isset($node[0]['parent_id'])) {
                    $parentId = $node[0]['parent_id'];
                    $this->objTreeNodes->deleteWithChildren($pageId);
                    return $this->nextAction('managemenus', array('pageid'=>$parentId), 'cmsadmin');
                }
                else {
                    $parentId = array();
                    return $this->nextAction('managemenus', array('pageid'=>$parentId), 'cmsadmin');
                }
            case 'showcmspages':
                $contentId = $this->getParam('id');
                $pageId = $this->getParam('pageid');
                $page = $this->_objContent->getContentPage($contentId);

                $this->setVar('content', $this->_objUtils->showBody($contentId, $pageId, FALSE));

                if (count($page) == 0) {
                    $contentId = NULL;
                    $sectionId = NULL;
                } else {
                    $sectionId = $page['sectionid'];
                }
                $this->setVar('contentId', $contentId);
                $this->setVar('sectionId', $sectionId);
                $this->setLayoutTemplate('menu_cms_layout_tpl.php');
                return  'cms_test_tpl.php';

            case 'showgrouplist':
                $groupId = $this->getParam('groupid');
                $this->setVar('groupId', $groupId);
                $this->setLayoutTemplate('');
                $this->setVar('content', $this->_objUtils->showGroupContent($groupId));
                return  'cms_test_tpl.php';
                break;
        }


    }


    /**
     * Method to update the Contents Permissions for the given content
     *
     * @access private
     * @return string  the template file name.
     */
    function processContentPermissionsMemberForm($contentid = null) {
        $redirect = 'addpermissions';
        if ( $this->getParam( 'button' ) == 'save' && $contentid != '') {
            // Get the revised member ids
            $list = $this->getParam( 'list2' ) ? $this->getParam( 'list2' ): array();
            // Get the original member ids
            $fields = array ( 'tbl_users.id' );


            //Getting origonal members from DB
            $membersDb = $this->_objSecurity->getAuthorizedContentMembers($contentid);
            //Preparing a list of USER ID's
            $memberCount = count($membersDb);
            $userList = array();
            for ($i = 0; $i < $memberCount; $i++) {
                $memberId = $membersDb[$i]['id'];
                //using the surname to check weather this is a user or group
                $memberType = (( isset($membersDb[$i]['surname']) ? 'user':'group' ));

                if ($memberType == 'user') {
                    array_push($userList, $memberId);
                }

            }

            //Preparing a list of GROUP ID's
            $groupList = array();
            for ($i = 0; $i < $memberCount; $i++) {
                $memberId = $membersDb[$i]['id'];
                //using the surname to check weather this is a user or group
                $memberType = (( isset($membersDb[$i]['surname']) ? 'user':'group' ));

                if ($memberType == 'group') {
                    array_push($groupList, $memberId);
                }

            }

            //Comparing USER and GROUPS against the current list
            //Will build list of users and groups to delete

            $requestGroupList = array();
            $requestUserList = array();
            foreach( $list as $id_type ) {
                //echo "Adding Permissions ContentID : $contentid, User ID : $userid <br/>";
                //splitting the value to check for the type
                $parts = explode('|', $id_type);
                $id = $parts[0];
                $type = $parts[1];

                //Must check for User Object first
                if ($type == 'user') {
                    array_push($requestUserList, $id);
                } else if ($type == 'group') {
                    array_push($requestGroupList, $id);
                }

                // Deleting REMOVED ITEMS from the SECTION_USERS and SECTION_GROUPS tables
            }

            $deleteUserList = array_diff($userList, $requestUserList);
            $deleteGroupList = array_diff($groupList, $requestGroupList);


            //Now we have the list of USER and GROUPS to DELETE
            foreach ($deleteUserList as $id) {
                $this->_objSecurity->deleteContentPermissionsUser($contentid, $id);
            }

            foreach ($deleteGroupList as $id) {
                $this->_objSecurity->deleteContentPermissionsGroup($contentid, $id);
            }

            //
            // Updating the SECTION_USERS and SECTION_GROUPS table
            //

            foreach( $list as $id_type ) {
                //echo "Adding Permissions ContentID : $contentid, User ID : $userid <br/>";
                //splitting the value to check for the type
                $parts = explode('|', $id_type);
                $id = $parts[0];
                $type = $parts[1];
                //Must check for User Object first
                if ($type == 'user') {
                    $this->_objSecurity->addContentPermissionsUser( $contentid, $id );

                } else if ($type == 'group') {
                    $this->_objSecurity->addContentPermissionsGroup( $contentid, $id );
                }

                // Deleting REMOVED ITEMS from the SECTION_USERS and SECTION_GROUPS tables
            }



            return true;
        }

        if ( $this->getParam( 'button' ) == 'cancel' ) {
        }

        return true;
    }


    /**
     * Method to update the Sections Permissions for the given section
     *
     * @access private
     * @return string  the template file name.
     */
    function processSectionPermissionsMemberForm($sectionid = null) {

        $redirect = 'addpermissions';
        if ( $this->getParam( 'button' ) == 'save' && $sectionid != '') {
            // Get the revised member ids
            $list = $this->getParam( 'list2' ) ? $this->getParam( 'list2' ): array();
            // Get the original member ids
            $fields = array ( 'tbl_users.id' );


            //Getting origonal members from DB
            $membersDb = $this->_objSecurity->getAuthorizedSectionMembers($sectionid);
            //Preparing a list of USER ID's
            $memberCount = count($membersDb);
            $userList = array();
            for ($i = 0; $i < $memberCount; $i++) {
                $memberId = $membersDb[$i]['id'];
                //using the surname to check weather this is a user or group
                $memberType = (( isset($membersDb[$i]['surname']) ? 'user':'group' ));

                if ($memberType == 'user') {
                    array_push($userList, $memberId);
                }

            }

            //Preparing a list of GROUP ID's
            $groupList = array();
            for ($i = 0; $i < $memberCount; $i++) {
                $memberId = $membersDb[$i]['id'];
                //using the surname to check weather this is a user or group
                $memberType = (( isset($membersDb[$i]['surname']) ? 'user':'group' ));

                if ($memberType == 'group') {
                    array_push($groupList, $memberId);
                }

            }

            //Comparing USER and GROUPS against the current list
            //Will build list of users and groups to delete

            $requestGroupList = array();
            $requestUserList = array();
            foreach( $list as $id_type ) {
                //echo "Adding Permissions SectionID : $sectionid, User ID : $userid <br/>";
                //splitting the value to check for the type
                $parts = explode('|', $id_type);
                $id = $parts[0];
                $type = $parts[1];

                //Must check for User Object first
                if ($type == 'user') {
                    array_push($requestUserList, $id);
                } else if ($type == 'group') {
                    array_push($requestGroupList, $id);
                }

                // Deleting REMOVED ITEMS from the SECTION_USERS and SECTION_GROUPS tables
            }

            $deleteUserList = array_diff($userList, $requestUserList);
            $deleteGroupList = array_diff($groupList, $requestGroupList);

            //Now we have the list of USER and GROUPS to DELETE
            foreach ($deleteUserList as $id) {
                $this->_objSecurity->deleteSectionPermissionsUser($sectionid, $id);
            }

            foreach ($deleteGroupList as $id) {
                $this->_objSecurity->deleteSectionPermissionsGroup($sectionid, $id);
            }

            //
            // Updating the SECTION_USERS and SECTION_GROUPS table
            //

            foreach( $list as $id_type ) {
                //echo "Adding Permissions SectionID : $sectionid, User ID : $userid <br/>";
                //splitting the value to check for the type
                $parts = explode('|', $id_type);
                $id = $parts[0];
                $type = $parts[1];
                //Must check for User Object first
                if ($type == 'user') {
                    $this->_objSecurity->addSectionPermissionsUser( $sectionid, $id );

                } else if ($type == 'group') {
                    $this->_objSecurity->addSectionPermissionsGroup( $sectionid, $id );
                }

                // Deleting REMOVED ITEMS from the SECTION_USERS and SECTION_GROUPS tables
            }




            return true;
        }

        if ( $this->getParam( 'button' ) == 'cancel' ) {
        }

        return true;
    }

    /**
     * Method to query for sections
     * This method can be used for queries related to sections
     *
     * @param string $contextcode
     * @param string $rootType: this can be 'all' or 'root' depending what
     * type of results you want
     * @return array of sections
     */

    public function getsections($contextcode=null,$rootType=null, $topNav='sections') {
        //Check whether the contextcode is set
        if (isset($contextcode)) {
            $this->contextCode = $contextcode;
        }
        //Check whether to display all nodes or root nodes only
        if (isset($rootType)) {
            $viewType = $rootType;
        }else {
            $viewType = $this->getParam('viewType', 'all');
        }
        $topNav = $this->_objUtils->topNav($topNav);
        $this->setVarByRef('topNav',$topNav);

        if($viewType == 'root') {
            $arrSections = $this->_objSections->getRootNodes(false,$this->contextCode);
        } elseif($this->objModule->checkIfRegistered('context', 'context')) {
            $arrSections = $this->_objUtils->getSectionLinks(TRUE,$this->contextCode);
        }
        $this->setVarByRef('arrSections', $arrSections);
        $this->setVarByRef('viewType', $viewType);

        return $arrSections;
    }

    /**
     * Method to query for sections for use with Grid layout
     * This method can be used for queries related to sections
     *
     * @param string $contextcode
     * @param string $rootType: this can be 'all' or 'root' depending what
     * type of results you want
     * @return array of sections
     */

    public function getGridSections($sortId = 'title', $sortOrder = 'ASC', $page = '1', $rows = '100') {
        //$arrSections = $this->_objUtils->getSectionLinks(TRUE,''/*$this->contextCode*/);
        $arrSections = $this->_objUtils->getGridSectionLinks($sortId, $sortOrder);
        $this->setVarByRef('arrSections', $arrSections);

        return $arrSections;
    }

    /**
     * This method accepts a section id and
     * returns an array of sections.For cms purposes
     * it sets by reference subsections as well for
     * the cms display template
     *
     * @param int $sectionid
     * @param int $subsectionid same as the section id
     * @return array sections
     */
    public function viewsections($sectionid=null,$subsectionid=null, $showLeftNav = true) {

        if (isset($sectionid) && ($this->inContextMode == FALSE)) {
            $id = $sectionid;
        }else {
            $id = $this->getParam('id');
        }

        if($id == NULL && $this->_objDBContext->isInContext()) {
            $arrSection = $this->_objSections->getSectionByContextCode($this->contextCode);
            $id = $arrSection['id'];
        }


        //Get section data
        $section = $this->_objSections->getSection($id);

        $this->setVarByRef('section', $section);
        //Get sub sections
        $subSections = $this->_objSections->getSubSectionsInSection($id);
        $this->setVarByRef('subSections', $subSections);
        //Get content pages
        $pages = $this->_objContent->getPagesInSectionJoinFront($id);
        //Get top Nav
        $topNav = $this->_objUtils->topNav('viewsection');
        $this->setVarByRef('topNav',$topNav);
        $this->setVarByRef('pages', $pages);
        $this->setVarByRef('showLeftNav', $showLeftNav);

        return $section;
    }

    /**
     * This method accepts a section id and
     * returns an array of sections.For cms purposes
     * it sets by reference subsections as well for
     * the cms display template
     *
     * This function lends this and is taylored to the PERMISSIONS Management
     * by making the content items browseable and therefore elegible for permissions editing
     *
     * @author Charl Mert
     * @param int $sectionid
     * @param int $subsectionid same as the section id
     * @return array sections
     */
    public function viewPermissionsSections($sectionid=null,$subsectionid=null) {

        $button = $this->getParam('save_btn');

        /*
                                   if (isset($sectionid) && ($this->inContextMode == FALSE)) {
                                   $id = $sectionid;
                                   }else{
                                   $id = $this->getParam('id');
                                   }

                                   if($id == NULL && $this->_objDBContext->isInContext()){
                                   $arrSection = $this->_objSections->getSectionByContextCode($this->contextCode);
                                   $id = $arrSection['id'];
                                   }
        */

        if ($sectionid == null) {
            $sectionid = $this->getParam('id');
        }
        $contentid = $this->getParam('cid');

        //Processing Edit Here

        if ($button == 'Save') {
            $chkCount = $this->getParam('chkCount', 0);

            if ($chkCount > 0) {
                $chkRead = array();
                $chkWrite = array();
                for ($i = 0; $i < $chkCount; $i++) {
                    $chkRead[$i] = $this->getParam('chk_read-'.$i);
                    $chkWrite[$i] = $this->getParam('chk_write-'.$i);
                }

                //Updating Checks for the assigned users
                $usersList = $this->_objSecurity->getAssignedContentUsers($contentid);
                $usersCount = count($usersList);

                //Preparing a list of GROUP_ID's
                $groupsList = $this->_objSecurity->getAssignedContentGroups($contentid);
                $groupsCount = count($groupsList);

                $globalChkCounter = 0;

                //Updating Groups Permissions
                for ($x = 0; $x < $groupsCount; $x++) {
                    $memberId = $groupsList[$x]['group_id'];

                    $chkReadValue = (($chkRead[$globalChkCounter] == 'on') ? 1 : 0);
                    $chkWriteValue = (($chkWrite[$globalChkCounter] == 'on') ? 1 : 0);

                    //echo "chkRead : ".$chkReadValue."<br/>";
                    //echo "chkWrite : ".$chkWriteValue."<br/><br/>";
                    $this->_objSecurity->setContentPermissionsGroup($contentid, $memberId, $chkReadValue, $chkWriteValue);

                    $globalChkCounter += 1;

                }	//End Loop

                //Updating User Permissions
                for ($x = 0; $x < $usersCount; $x++) {
                    $memberId = $usersList[$x]['user_id'];

                    $chkReadValue = (($chkRead[$globalChkCounter] == 'on') ? 1 : 0);
                    $chkWriteValue = (($chkWrite[$globalChkCounter] == 'on') ? 1 : 0);

                    //echo "chkRead : ".$chkReadValue."<br/>";
                    //echo "chkWrite : ".$chkWriteValue."<br/><br/>";

                    $this->_objSecurity->setContentPermissionsUser($contentid, $memberId, $chkReadValue, $chkWriteValue);

                    $globalChkCounter += 1;

                }       //End Loop
            }

            $owner = $this->getParam('drp_owner');
            $this->_objSecurity->setContentOwner($contentid, $owner);

            $chkNoPublic = $this->getParam('chk_public');

            if ($chkNoPublic == 'on') {
                $this->_objSecurity->setContentPermissionsPublicAccess($contentid, false);
            } else {
                $this->_objSecurity->setContentPermissionsPublicAccess($contentid, true);
            }


            //echo "Set owner $owner for $contentid";
            //exit;
        }


        //echo "Section ID : $sectionid";
        $id = $sectionid;
        //Get section data
        $section = $this->_objSections->getSection($id);
        $this->setVarByRef('section', $section);
        //Get sub sections

        $subSections = $this->_objSections->getSubSectionsInSection($id);
        $this->setVarByRef('subSections', $subSections);

        //Get content pages
        $pages = $this->_objContent->getPagesInSectionJoinFront($id);

        //Get top Nav
        $topNav = $this->_objUtils->topNav('view_permissions_section');
        $this->setVarByRef('topNav',$topNav);
        $this->setVarByRef('pages', $pages);

        //echo "HERE"; exit;

        return $section;
    }


    /**
     * Method to return the sections form. The form comes with
     * ajax attached so no need to get that as well.
     * The method accepts two params that of the parent
     * and section ids. This is so you can have (n)levels
     *
     * @param int $sectionid
     * @param int $parentid
     * @return string form
     */
    public function addEditsection($sectionid=null,$parentid=null) {
        // Generation of Ajax Scripts
        //$ajax = $this->getObject('xajax', 'ajaxwrapper');
        //$ajax->setRequestURI($this->uri(array('action'=>'addsection'), 'cmsadmin'));
        //$ajax->registerFunction(array($this, 'processSection')); // Register another function in this controller
        //$ajax->processRequests(); // XAJAX method to be called
        //$this->appendArrayVar('headerParams', $ajax->getJavascript()); // Send JS to header
        //Get form
        if (!isset($sectionid) ) {
            $sectionid = $this->getParam('id');
        }
        if (!isset($parentid)) {
            $parentid = $this->getParam('parentid');
        }
        $addEditForm = $this->_objUtils->getAddEditSectionForm($sectionid, $parentid);

        //Get Edit Form
        $this->setVarByRef('addEditForm', $addEditForm);
        $parentid = $this->getParam('parentid');
        $level = $this->_objSections->getLevel($parentid);
        $this->setVarByRef('parentid', $parentid);
        $this->setVarByRef('sectionid', $sectionid);
        return $addEditForm;
    }


    /**
     * Method to return the PERMISSIONS form. The form comes with
     * ajax attached so no need to get that as well.
     * The method accepts two params that of the parent
     * and permissions ids. This is so you can have (n)levels
     *
     public function addEditSectionPermissions($sectionid=null, $returnSubView = 0){
     * @param int $permissionsid
     * @param int $parentid
     * @return string form
     */
    public function addEditSectionPermissions($sectionid=null, $returnSubView = 0) {
        //Get form
        if (!isset($sectionid) ) {
            $sectionid = $this->getParam('id');
        }

        if ($sectionid == '') {
            $sectionid = $this->getParam('cid');
        }

        //Processing Edit Here

        if ($this->getParam('save_btn') == 'Save') {

            $chkPropagate = $this->getParam('chk_propagate');
            $chkNoPublic = $this->getParam('chk_public');
            $chkPropagateOwner = $this->getParam('chk_propagate_owner');

            $chkCount = $this->getParam('chkCount');
            if ($chkCount > 0) {
                $chkRead = array();
                $chkWrite = array();
                for ($i = 0; $i < $chkCount; $i++) {
                    $chkRead[] = $this->getParam('chk_read-'.$i);
                    $chkWrite[] = $this->getParam('chk_write-'.$i);
                }

                //Updating Checks for the assigned users
                $usersList = $this->_objSecurity->getAssignedSectionUsers($sectionid);
                $usersCount = count($usersList);
                //Preparing a list of GROUP_ID's
                $groupsList = $this->_objSecurity->getAssignedSectionGroups($sectionid);
                $groupsCount = count($groupsList);

                $globalChkCounter = 0;

                //Updating Groups Permissions
                for ($x = 0; $x < $groupsCount; $x++) {
                    $memberId = $groupsList[$x]['group_id'];

                    $chkReadValue = (($chkRead[$globalChkCounter] == 'on') ? 1 : 0);
                    $chkWriteValue = (($chkWrite[$globalChkCounter] == 'on') ? 1 : 0);

                    $this->_objSecurity->setPermissionsGroup($sectionid, $memberId, $chkReadValue, $chkWriteValue);

                    if ($chkPropagate == 'on') {
                        // Updating Child Sections
                        $this->_objSecurity->setPermissionsGroupPropagate($sectionid, $memberId, $chkReadValue, $chkWriteValue);
                        // Updating Child Content
                        $this->_objSecurity->setContentPermissionsGroupPropagate($sectionid, $memberId, $chkReadValue, $chkWriteValue);
                    } else {
                    }

                    $globalChkCounter += 1;

                }


                //Updating User Permissions
                for ($x = 0; $x < $usersCount; $x++) {
                    $memberId = $usersList[$x]['user_id'];

                    $chkReadValue = (($chkRead[$globalChkCounter] == 'on') ? 1 : 0);
                    $chkWriteValue = (($chkWrite[$globalChkCounter] == 'on') ? 1 : 0);

                    $this->_objSecurity->setPermissionsUser($sectionid, $memberId, $chkReadValue, $chkWriteValue);
                    if ($chkPropagate == 'on') {
                        $this->_objSecurity->setPermissionsUserPropagate($sectionid, $memberId, $chkReadValue, $chkWriteValue);
                        //Updating Child Content
                        $this->_objSecurity->setContentPermissionsUserPropagate($sectionid, $memberId, $chkReadValue, $chkWriteValue);
                    }

                    $globalChkCounter += 1;

                }
            }

            if ($chkCount == 0 || $usersCount == 0 || $groupsCount == 0) {
                //This Means that there were NO groups to be Authorized
                //To propagate this through to the children the child group access must also be removed

                if ($chkPropagate == 'on') {
                    if ($groupsCount == 0) {
                        //Clearing the Permissions Section - Group for the given section
                        $this->_objSecurity->deletePermissionsGroupPropagate($sectionid);

                        //Clearing the Permissions Content - Group for the given section
                        $this->_objSecurity->deleteContentPermissionsGroupPropagate($sectionid);

                    }

                    if ($usersCount == 0) {
                        //Clearing the Permissions Section - User for the given section
                        $this->_objSecurity->deletePermissionsUserPropagate($sectionid);

                        //Clearing the Permissions Section - User for the given section
                        $this->_objSecurity->deleteContentPermissionsUserPropagate($sectionid);
                    }

                }

            }

            $owner = $this->getParam('drp_owner');

            if (!$chkPropagateOwner == 'on') {
                $this->_objSecurity->setOwner($sectionid, $owner);
            } else {
                $this->_objSecurity->setOwner($sectionid, $owner);

                //Propagating Owner down to child Sections
                $this->_objSecurity->setOwnerPropagate($sectionid, $owner);

                //Propagating Owner down to child Content
                $this->_objSecurity->setContentOwnerPropagate($sectionid, $owner);

            }


            $public = ($chkNoPublic == 'on')? false : true;
            $this->_objSecurity->setSectionPermissionsPublicAccess($sectionid, $public);
            if ($chkPropagate == 'on') {
                //Propagate
                $this->_objSecurity->setSectionPermissionsPublicAccessPropagate($sectionid, $public);
            }
        }

        //Get Edit Form
        $addEditSectionPermissionsForm = $this->_objUtils->getAddEditPermissionsSectionForm($sectionid, $returnSubView);
        $this->setVarByRef('addEditSectionPermissionsForm', $addEditSectionPermissionsForm);
    }


    /**
     * Method to return the PERMISSIONS form for CONTENT. The form comes with
     *
     * @param int $permissionsid
     * @param int $parentid
     * @return string form
     */
    public function addEditContentPermissions($contentid=null) {

        if (!isset($contentid) ) {
            $contentid = $this->getParam('id');
        }

        $addEditContentPermissionsForm = $this->_objUtils->getAddEditPermissionsContentForm($contentid);

        //Get Edit Form

        $this->setVarByRef('addEditContentPermissionsForm', $addEditContentPermissionsForm);
        $this->setVarByRef('contentid', $contentid);
        return $addEditContentPermissionsForm;
    }


    /**
     * Method to return the PERMISSIONS User Group form. The form comes with
     * ajax attached so no need to get that as well.
     * The method accepts two params that of the parent
     * and permissions ids. This is so you can have (n)levels
     *
     * @param int $permissionsid
     * @param int $parentid
     * @return string form
     */
    public function addEditSectionPermissionsUserGroup($sectionid=null) {
        if (!isset($sectionid) ) {
            $sectionid = $this->getParam('id');
        }

        $addEditSectionPermissionsUserGroupForm = $this->_objUtils->getAddEditPermissionsSectionUserGroupForm($sectionid);

        //Get Edit Form
        $this->setVarByRef('addEditSectionPermissionsUserGroupForm', $addEditSectionPermissionsUserGroupForm);
        $this->setVarByRef('sectionid', $sectionid);
        return $addEditSectionPermissionsUserGroupForm;
    }

    /**
     * Method to return the PERMISSIONS User Group form. The form comes with
     * ajax attached so no need to get that as well.
     * The method accepts two params that of the parent
     * and permissions ids. This is so you can have (n)levels
     *
     * @param int $permissionsid
     * @param int $parentid
     * @return string form
     */
    public function addEditContentPermissionsUserGroup($contentid=null) {
        // Generation of Ajax Scripts
        //$ajax = $this->getObject('xajax', 'ajaxwrapper');
        //$ajax->setRequestURI($this->uri(array('action'=>'addcontent'), 'cmsadmin'));
        //$ajax->registerFunction(array($this, 'processcontent')); // Register another function in this controller
        //$ajax->processRequests(); // XAJAX method to be called
        //$this->appendArrayVar('headerParams', $ajax->getJavascript()); // Send JS to header
        //Get form
        if (!isset($contentid) ) {
            $contentid = $this->getParam('id');
        }

        $section_id = $this->getParam('parent');

        $addEditContentPermissionsUserGroupForm = $this->_objUtils->getAddEditPermissionsContentUserGroupForm($contentid);

        //Get Edit Form
        $this->setVarByRef('addEditContentPermissionsUserGroupForm', $addEditContentPermissionsUserGroupForm);
        $this->setVarByRef('contentid', $contentid);
        return $addEditContentPermissionsUserGroupForm;
    }


    /**
     * Method creates the return blcks layout
     * Facilitates the ajax return type for a block
     *
     * @param string $blockId: which bock is dragged
     * @param string $cssClass: css layout class
     * @return string block
     */

    private function createReturnBlock($blockId, $cssClass) {
        $objModuleBlocks = $this->getObject('dbmoduleblocks', 'modulecatalogue');
        $objBlocks = $this->getObject('blocks', 'blocks');
        $this->loadClass('layer', 'htmlelements');

        $blockRow = $objModuleBlocks->getRow('id', $blockId);

        $lbDisabled = $this->objLanguage->languageText('mod_cmsadmin_warnlinkdisabled', 'cmsadmin');

        $str = trim($objBlocks->showBlock($blockRow['blockname'], $blockRow['moduleid'], '', 20, TRUE, TRUE, 'none'));
        $str = preg_replace('/type\\s??=\\s??"submit"/', 'type="button"', $str);
        $str = preg_replace('/href=".+?"/', 'href="javascript:alert(\''.$lbDisabled.'\');"', $str);

        //return '<div class="'.$cssClass.'" id="'.$blockRow['id'].'" style="border: 1px solid lightgray; padding: 5px; width:150px; float: left; z-index:20;">'.$str.'</div>';

        $objLayer = new layer();
        $objLayer->str = $str;
        $objLayer->id = $blockRow['id'];
        $objLayer->cssClass = $cssClass;
        return $objLayer->show();
    }

    /**
     * Method to get the menu for the cms admin
     *
     * @access public
     * @return string The html to produce the navigation
     */
    public function getCMSMenu() {
        return $this->_objUtils->getNav();
    }

    /**
     * fuction to process publish or unpublish in sections
     *
     * @param array $itemsArray
     */
    private function processSelection($itemsArray, $publish) {
        if(!empty($itemsArray)) {
            foreach ($itemsArray as $line) {
                $this->_objSections->publish($line, $publish);
            }
        }
    }

    /**
     * Method to undelete a set of content pages
     *
     * @author Megan Watson
     * @access private
     * @param array $itemsArray The pages to undelete/unarchive
     * @return
     */
    private function publishFrontContent($itemsArray, $publish = 'publish') {
        if(!empty($itemsArray)) {
            foreach ($itemsArray as $item) {
                $this->_objContent->publish($item, $publish);
            }
        }
    }

    /**
     * Method to undelete a set of content pages
     *
     * @author Megan Watson
     * @access private
     * @param array $itemsArray The pages to undelete/unarchive
     * @return
     */
    private function unarchiveContentPages($itemsArray) {
        if(!empty($itemsArray)) {
            foreach ($itemsArray as $item) {
                $this->_objContent->undelete($item);
            }
        }
    }

    /**
     * Method to undelete a set of sections with their contents
     *
     * @author Megan Watson
     * @access private
     * @param array $itemsArray The sections to undelete/unarchive
     * @return
     */
    private function unarchiveSections($itemsArray) {
        if(!empty($itemsArray)) {
            foreach ($itemsArray as $item) {
                $this->_objSections->unarchiveSection($item);
            }
        }
    }

    private function addRss() {

        $rssname = $this->getParam('name');
        $rssurl = $this->getParam('rssurl');
        $rssdesc = $this->getParam('description');
        $userid = $this->_objUser->userId();
        $mode = $this->getParam('mode');
        if($mode == 'edit') {
            $id = $this->getParam('id');
            $addarr = array('id' => $id, 'userid' => $userid, 'url' => $rssurl, 'name' => $rssname, 'description' => $rssdesc);
            $this->_objLayouts->addRss($addarr, 'edit');


            return 'rssedit_tpl.php';
        }
        
        //get the cache
        //get the proxy info if set
        $proxyArr = $this->objProxy->getProxy();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rssurl);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
            curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'].":".$proxyArr['proxy_port']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'].":".$proxyArr['proxy_pass']);
        }

        $rsscache = curl_exec($ch);
        curl_close($ch);
        //put in a timestamp
        $addtime = time();
        $addarr = array('userid' => $userid, 'url' => $rssurl, 'name' => $rssname, 'description' => $rssdesc, 'rsscache' => htmlentities($rsscache), 'rsstime' => $addtime);

        //write the file down for caching
        $path = $this->_objConfig->getContentBasePath() . "cms/";
        $path =  str_replace('\\', '/',$path);

        if(!is_dir($path)) {
            mkdir($path, 0700);
        }
        $path .= "rsscache/";
        $path =  str_replace('\\', '/',$path);

        if(!is_dir($path)) {
            mkdir($path, 0700);
        }

        $rsstime = time();
        if(!file_exists($path)) {
            $filename = $path . $userid . "_" . $rsstime . ".xml";
            if(!file_exists($filename)) {
                touch($filename);

            }
            $handle = fopen($filename, 'wb');
            fwrite($handle, $rsscache);
        }
        else {
            $filename = $path . $userid . "_" . $rsstime . ".xml";
            $handle = fopen($filename, 'wb');
            fwrite($handle, $rsscache);
        }

        //add into the db

        $rssurl = htmlentities($rssurl, ENT_QUOTES);
        $rssname = htmlentities($rssname, ENT_QUOTES);
        $rssdesc = htmlentities($rssdesc, ENT_QUOTES);


        $addarr = array('userid' => $userid, 'url' => $rssurl, 'name' => $rssname, 'description' => $rssdesc, 'rsscache' => $filename, 'rsstime' => $rsstime);
        $result = $this->_objLayouts->addRss($addarr);

        return ;
    }
    /**
     * The method is designed to handle the menu form
     * sets up the add/edit of menus
     * @access private
     * @return admin template
     */

    private function menuAdd() {
        $add = $this->getParam('add',FALSE);

        $menuNode = $this->objTreeMenu->getNode($this->currentPageId, FALSE);

        if (!$add) {
            if (count($menuNode) > 0) {

                $this->setVar('editForm', $this->_objUtils->showEditNode($this->currentPageId));
            }
        } else {
            $this->setVar('editForm', $this->_objUtils->showAddNode($this->currentPageId));
        }
        if (count($menuNode) > 0) {
            $this->setVar('menuNodeParent', $menuNode[0]['parent_id']);
            if ($menuNode[0]['node_type'] == 1) {
                $this->setVar('content', $this->_objUtils->showBody($menuNode[0]['link_reference'], $this->currentPageId));
            }
        }
        return  'cms_menuadmin_tpl.php';
    }


}

?>
