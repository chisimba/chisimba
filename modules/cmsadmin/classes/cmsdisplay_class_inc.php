<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
    }
    // end security check

/**
 * This object holds all the template display forms
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Wesley  Nitsckie
 * @author Warren Windvogel
 * @author Charl Mert
 */

    class cmsdisplay extends object
    {
       /**
        * The context  object
        *
        * @access private
        * @var object
        */
        protected $_objContext;	

      /**
        * The inContextMode  object
        *
        * @access private
        * @var object
        */
        protected $inContextMode;	

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
        * The Skin object
        *
        * @access private
        * @var object
        */
        protected $objSkin;

      /**
        * The Content Front Page object
        *
        * @access private
        * @var object
        */
        protected $_objFrontPage;

      /**
        * The User object
        *
        * @access private
        * @var object
        */
        protected $_objUser;

      /**
        * The user model
        *
        * @access private
        * @var object
        */
        protected $_objUserModel;

      /**
        * The config object
        *
        * @access private
        * @var object
        */
        protected $_objConfig;

      /**
        * The blocks object
        *
        * @access private
        * @var object
        */
        protected $_objBlocks;

      /**
        * Feature box object
        *
        * @var object
        */
        public $objFeatureBox;

      /**
        * The security object
        *
        * @access public
        * @var object
        */
        public $_objSecurity;


      /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                $this->_objUserPerm = $this->getObject ('dbuserpermissions', 'cmsadmin');
                $this->objOps = $this->getObject ('groupops', 'groupadmin');
                $this->_objQuery =  $this->newObject('jquery', 'jquery');
                $this->_objBox = $this->newObject('jqboxy', 'jquery');
                $this->_objUtils =  $this->newObject('cmsutils', 'cmsadmin');
                $this->_objFlag =  $this->newObject('dbflag', 'cmsadmin');
                $this->_objFlagOptions =  $this->newObject('dbflagoptions', 'cmsadmin');
                $this->_objFlagEmail =  $this->newObject('dbflagemail', 'cmsadmin');
                $this->_objTemplate =  $this->newObject('dbtemplate', 'cmsadmin');
                $this->_objPageMenu =  $this->newObject('dbpagemenu', 'cmsadmin');
                $this->_objSecurity =  $this->newObject('dbsecurity', 'cmsadmin');
                $this->_objSections =$this->newObject('dbsections', 'cmsadmin');
                $this->_objDBContext =$this->getObject('dbcontext', 'context');
                $this->_objContent =$this->newObject('dbcontent', 'cmsadmin');
                $this->_objConfig =$this->newObject('altconfig', 'config');
                $this->_objSysConfig =$this->newObject('dbsysconfig', 'sysconfig');
                $this->_objBlocks =$this->newObject('dbblocks', 'cmsadmin');
                $this->objRss = $this->newObject('dblayouts');
                $this->objSkin =$this->newObject('skin', 'skin');
                $this->_objFrontPage =$this->newObject('dbcontentfrontpage', 'cmsadmin');
                $this->_objUser =$this->newObject('user', 'security');
                $this->_objUserModel =$this->newObject('useradmin_model','security');
                $this->objLanguage =$this->newObject('language', 'language');
                $this->_objContext =$this->newObject('dbcontext', 'context');
                $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
                $this->objModule=&$this->getObject('modules','modulecatalogue');
                $this->objDateTime = $this->getObject('dateandtime', 'utilities');

                $this->loadClass('textinput', 'htmlelements');
                $this->loadClass('checkbox', 'htmlelements');
                $this->loadClass('radio', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                $this->loadClass('form', 'htmlelements');
                $this->loadClass('button', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
                $this->loadClass('label', 'htmlelements');
                $this->loadClass('hiddeninput', 'htmlelements');
                $this->loadClass('textarea','htmlelements');
                $this->loadClass('htmltable','htmlelements');
                $this->loadClass('layer', 'htmlelements');

            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

       
      /**
        * Gets the display template contents for User Permissions
        *
        * @access public
        * @param $arrUserPermissions An Array of User Permissions Mappings from dbuserpermissions
        * @return Display Template Contents
        */
        public function getUserPermissionsTemplate($arrUserPermissions) {
            //Boxy Form Test URL
            //http://localhost/test/?module=cmsadmin&action=ajaxforms&type=showuserpermaddform

            //initiate objects
            $table =  $this->newObject('htmltable', 'htmlelements');
            $objH = $this->newObject('htmlheading', 'htmlelements');
            $objH3 = $this->newObject('htmlheading', 'htmlelements');
            $link =  $this->newObject('link', 'htmlelements');
            $objIcon =  $this->newObject('geticon', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
            $objLayer =$this->newObject('layer','htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');

            $topNav = $this->_objUtils->topNav('userpermissions');

            $lblHeading = $this->objLanguage->languageText('mod_cmsadmin_userpermissionsmanager', 'cmsadmin');
            $lblNoRecords = $this->objLanguage->languageText('mod_cmsadmin_nopagesfoundinthissection', 'cmsadmin');
            $lblSelectAll = $this->objLanguage->languageText('mod_cmsadmin_selectall', 'cmsadmin');
            $lblUserName = $this->objLanguage->languageText('mod_cmsadmin_username', 'cmsadmin');
            $lblCanAddToFrontPage = $this->objLanguage->languageText('mod_cmsadmin_canaddtofrontpage', 'cmsadmin');
            $lblSelectAll = $this->objLanguage->languageText("mod_cms_selectall", "cmsadmin");

            $selectbutton=$this->newObject('button','htmlelements'); 
            $selectbutton->setOnClick("javascript:SetAllCheckBoxes('SelectAll', 'arrayList[]', true);"); 
            $selectbutton->setValue($lblSelectAll);
            $selectbutton->setToSubmit(); 

            $tbl = $this->newObject('htmltable', 'htmlelements');
            $tbl->cellpadding = 3;
            $tbl->align = "left";

            //create a heading
            $objH->type = '1';

            //counter for records
            $cnt = 1;
            //Heading box
            $objIcon->setIcon('user_small', 'png', 'icons/cms/');
            $objIcon->title = $lblHeading;
            $objH->str =  $objIcon->show().'&nbsp;'. $lblHeading;

            $hdr = $objH->show();
            
            $objH3->type = '3';
            $objH3->str = $lblHeading;
            $hdr .= $objH3->show();

            $tbl->startRow();
            $tbl->addCell($hdr, '', 'center');
            $tbl->addCell($topNav, '','center','right');
            $tbl->endRow();

            $objLayer->str = $objH->show();
            $objLayer->id = 'cms_header_left';
            $header = $objLayer->show();

            $objLayer->str = $topNav;
            $objLayer->id = 'cms_header_right';
            $header .= $objLayer->show();

            $objLayer->str = '';
            $objLayer->cssClass = 'clearboth';
            $headShow = $objLayer->show();

            $objLayer->str = '&nbsp;';
            $objLayer->id = 'cmsvspacer';
            $vspacer = $objLayer->show();

            //Get Selectall js
            $result = $this->getJavascriptFile('selectall.js');
            $result .= $header.$headShow.$vspacer;//$tbl->show());
            //get the permissions

            //Get cms type
            $cmsType = 'treeMenu';
            //set up select
            // Buttons to Select All

            $txt_task = new textinput('task',null,'hidden');
            //$objCheck->setOnClick("javascript:SetAllCheckBoxes('document.getElementById('form_select')'), 'arrayList[]', true);"); 

            $table = new htmltable();
            $table->cellspacing = '2';
            $table->cellpadding = '5';

            //setup the table headings
            $table->startHeaderRow();
            $table->addHeaderCell("<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"javascript:ToggleCheckBoxes('select', 'arrayList[]', 'toggle');\" />" . " " . $lblSelectAll, '100px', 'top', 'left', 'cms_table_header');
            $table->addHeaderCell($lblUserName, '250px', 'top', 'left', 'cms_table_header');
            $table->addHeaderCell($lblCanAddToFrontPage, '', 'top', 'left', 'cms_table_header');
            $table->addHeaderCell($this->objLanguage->languageText('word_options'));

            $table->endHeaderRow();

            $rowcount = 0;

            //setup the tables rows  and loop though the records
            if (is_array($arrUserPermissions)) {
                foreach($arrUserPermissions as $userPerm) {
                    //Set odd even row colour
                    $oddOrEven = ($rowcount == 0) ? "even" : "odd";
                    
                    //Set up select form
                    $objCheck = new checkbox('arrayList[]');
                    $objCheck->setValue($userPerm['id']);
                    $objCheck->extra = "onclick=\"javascript: ToggleMainBox('select', 'toggle', this.checked);\"";

                    //Trash
                    $objIcon->setIcon('bigtrash');
                    $deleteIcon = "<a id='btn_del_mail_{$userPerm['id']}' title='Delete' href='javascript:void(0)'>".$objIcon->show()."</a>";

                    $innerHtml = $this->_objUtils->getDeleteConfirmForm($userPerm['id'], 'userperm');
                    $this->_objBox->setHtml($innerHtml);
                    $this->_objBox->setTitle('Confirm');
                    $this->_objBox->attachClickEvent("btn_del_mail_{$userPerm['id']}");

                    //Edit
                    $span = '<span id="'.'btn_add_userperm_' . $userPerm['id'].'">'.$objIcon->getEditIcon('#') . '</span>';
                    $editIcon = $span;

                    $options = $editIcon.$deleteIcon;

                    $innerHtml = $this->getAddUserPermissionsForm($userPerm['id']);
                    $this->_objBox->setHtml($innerHtml);
                    $this->_objBox->setTitle('Edit User Permissions');
                    $this->_objBox->attachClickEvent('btn_add_userperm_' . $userPerm['id']);

                    //Username field
                    $userName = '';
                    if (isset($userPerm['user_id'])) {
                        $userName = $this->_objUser->userName($userPerm['user_id']);
                    }

                    //Frontpage Field
                    $chkFront = new checkbox('can_front');
                    if (isset($userPerm['show_on_frontpage'])) {
                        $chkFront->setChecked($userPerm['show_on_frontpage']);
                    }

                    $chkFront->extra = ' disabled';
                    $canFront = $chkFront->show();


                    $table->startRow();
                    $table->addCell($objCheck->show());
                    $table->addCell($userName);
                    $table->addCell($canFront);
                    $table->addCell($options);
                    $table->endRow();

                }
            }else{
                $result .= '<div class="noRecordsMessage">'.$lblNoRecords.'</div>';
            }

            $frm_select = new form('select', $this->uri(array('action' => 'select'), 'cmsadmin'));
            $frm_select->id = 'select';
            $frm_select->addToForm($table->show());
            $frm_select->addToForm($txt_task->show());

            $result .= "<hr />";
            $result .= $filterTable;
            $result .= $frm_select->show();
            $result .= '&nbsp;'.'<br/>';
            $result .= "<hr />";
        
            return $result;
        }


   /**
    * Method to return the Boxy New User Permissions Form
    *
    * @param string $id The id of the form to be edited
    * @access public
    */
        public function getAddUserPermissionsForm($id = '')
        {
            //Boxy Form Test URL
            //http://localhost/test/?module=cmsadmin&action=ajaxforms&type=showuserpermaddform

            //Load Edit Values when supplied with id
            if ($id != ''){
                $arrUserPerms = $this->_objUserPerm->getRecord($id);
            }

            $table = new htmlTable();
            $table->width = "100%";
            $table->cellspacing = "0";
            $table->cellpadding = "10";
            $table->border = "0";
            $table->attributes = "align ='center'";

            $tbl = new htmlTable();
            $tbl->width = "100%";
            $tbl->cellspacing = "0";
            $tbl->cellpadding = "10";
            $tbl->border = "0";
            $tbl->attributes = "align ='center'";

            //TODO: Add Language Items for these
            //Select User
            $lblUser = 'User :';

            $userName = '';
            if (isset($arrUserPerms['user_id'])){
                $id = $arrUserPerms['user_id'];
                $userName = $this->_objUser->userName($id);
            }

            $drpUser = new dropdown('drp_owner');
            $allUsers = $this->_objSecurity->getAllUsers();
            $allUsersCount = count($allUsers);
            //var_dump($allUsers);  
            for ($i = 0; $i < $allUsersCount; $i++){
                //$drpUser->addOption($allUsers[$i]['userid'], $allUsers[$i]['firstname'].' '.$allUsers[$i]['surname']);
                //echo 'Owner ID : '.$allUsers[$i]['userid'].'<br/>';
                $drpUser->addOption($allUsers[$i]['userid'], $allUsers[$i]['username']);
            }

            $drpUser->setSelected($arrUserPerms['user_id']);

            $tbl->startRow();
            $tbl->addCell($lblUser, '', '', '', 'boxy_td_left', '','');
            $tbl->addCell($drpUser->show(), '', 'top', 'left');
            $tbl->endRow();

            //Check Permission Options
            $lblFrontpage = 'Edit Front Page';

            $canEditFrontpage = false;
            if (isset($arrUserPerms['show_on_frontpage'])){
                $canEditFrontpage = $arrUserPerms['show_on_frontpage'];
            }

            $chkFrontpage = new checkbox('chk_frontpage', 'Frontpage', $canEditFrontpage);

            $tbl->startRow();
            $tbl->addCell('&nbsp', '', 'top', null, 'cmsvspacer', 'colspan="2"');
            $tbl->endRow();

            $tbl->startRow();
            $tbl->addCell('Grant Privileges:', '', 'top', null, 'cmsvspacer', 'colspan="2"');
            $tbl->endRow();

            $tbl->startRow();
            $tbl->addCell($chkFrontpage->show(), '', '', '', 'boxy_td_left', '','');
            $tbl->addCell($lblFrontpage, '', 'top', 'left');
            $tbl->endRow();

            //Submit/Cancel
            $btnOk = "<input type='submit' id='sData' name='sData' value='Save' style='width:50px;'/>";
            //$btnOk = "<input type='button' id='frm_submit_btn_$id' name='frm_add_submit' value='Save' style='width:50px;'/>";
            $btnCancel = "<input type='button' id='cancel' name='cancel' value='Cancel' style='width:50px;' onclick='Boxy.get(this).hide(); return false'/>";

            $tbl1 = new htmlTable();
            $tbl1->width = "100%";
            $tbl1->cellspacing = "0";
            $tbl1->cellpadding = "0";
            $tbl1->border = "0";
            $tbl1->attributes = "align ='center'";

            $tbl1->startRow();
            $tbl1->addCell($btnOk.' '.$btnCancel, '', '', 'center', '', '','');
            $tbl1->endRow();

            if ($id != '') {
                $action = '<input type="hidden" name="oper" value="edit" />';
                $action .= '<input type="hidden" name="id" value="'.$id.'" />';
            } else {
                $action = '';
            }

            //Adding All to Container here
            $table->startRow();
            $table->addCell($tbl->show()/*.$layer->show()*/.'<div style="padding-bottom:10px"></div>'.$tbl1->show(), '', '', 'center', '', '','');
            $table->endRow();

            //Stripping New Lines and preparing for boxy input = (Facebook style window)
            $display = '<form id="frm_add_'.$id.'" class="Form" name="frm_addgrid" action="?module=cmsadmin&action=addedituserpermissions" method="POST">';
            $display .= str_replace("\n", '',$table->show());
            $display = str_replace("\n\r", '', $display);

            $display .= $action;
            $display .= '</form>';

            return $display;
        }


   /**
    * Method to return the Boxy New User Permissions Form
    *
    * @param title The title of the alert box
    * @param body The message to be displayed in the body of the alert box
    * @access public
    */
        public function getAlertForm($title, $body)
        {
            $table = new htmlTable();
            $table->width = "100%";
            $table->cellspacing = "0";
            $table->cellpadding = "10";
            $table->border = "0";
            $table->attributes = "align ='center'";

            $tbl = new htmlTable();
            $tbl->width = "100%";
            $tbl->cellspacing = "0";
            $tbl->cellpadding = "10";
            $tbl->border = "0";
            $tbl->attributes = "align ='center'";

            $objIcon = $this->getObject('geticon', 'htmlelements');
            $objIcon->setIcon('errorinfo', 'png', 'icons/cms/');
            $errIcon = $objIcon->show();
            
            $tbl->startRow();
            $tbl->addCell($errIcon, '', 'top', null, 'cmsvspacer', 'colspan="2"');
            $tbl->addCell($body, '270px', 'top', null, 'cmsvspacer', 'colspan="2"');
            $tbl->endRow();

            //Ok Button
            $btnOk = "<input type='button' id='cancel' name='ok' value='Ok' style='width:50px;' onclick='Boxy.get(this).hide(); return false'/>";

            $tbl1 = new htmlTable();                                                
            $tbl1->width = "100%";
            $tbl1->cellspacing = "0";
            $tbl1->cellpadding = "0";
            $tbl1->border = "0";
            $tbl1->attributes = "align ='center'";

            $tbl1->startRow();
            $tbl1->addCell($btnOk, '', '', 'center', '', '','');
            $tbl1->endRow();

            //Adding All to Container here
            $table->startRow();
            $table->addCell($tbl->show() .$tbl1->show(), '', '', 'center', '', '','');
            $table->endRow();

            //Stripping New Lines and preparing for boxy input = (Facebook style window)
            $display = '<form id="frm_err" class="Form" name="frm_err" action="#">';
            $display .= str_replace("\n", '',$table->show());
            $display = str_replace("\n\r", '', $display);

            //$display .= $action;
            $display .= '</form>';

            return $display;
        }



    }

?>
