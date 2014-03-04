<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
    }
    // end security check

/**
 * This object hold all the utility method that the cms modules might need
 *
 * @package forms
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Wesley  Nitsckie
 * @author Warren Windvogel
 * @author Charl Mert
 */

    class ui extends object
    {
      /**
        * The Skin object
        *
        * @access private
        * @var object
        */
        protected $objSkin;

      /**
        * The User object
        *
        * @access private
        * @var object
        */
        protected $_objUser;

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
                $this->objForms =  $this->newObject('dbforms', 'forms');
                $this->objFormRecords =  $this->newObject('dbformrecords', 'forms');
                $this->objFormSubRecords =  $this->newObject('dbformsubrecords', 'forms');
                $this->_objQuery =  $this->newObject('jquery', 'jquery');
                $this->_objConfig =$this->newObject('altconfig', 'config');
                $this->_objSysConfig =$this->newObject('dbsysconfig', 'sysconfig');
                $this->objSkin =$this->newObject('skin', 'skin');
                $this->_objUser =$this->newObject('user', 'security');
                // $this->_objUserModel =$this->newObject('useradmin_model','security');
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
        * Method to return the add edit form
        *
        * @param string $formId The id of the form item to be edited. Default NULL for adding new form
        * @access public
        * @return string $middleColumnForm The form used to create and edit a form
        * @author Charl Mert
        */
        public function getAddEditForm($formId = NULL)
        {

            $h3 = $this->newObject('htmlheading', 'htmlelements');
            $objIcon = $this->getObject('geticon','htmlelements');

            $published = new checkbox('published');

            $objOrdering = new textinput();
            //$objCCLicence = $this->newObject('licensechooser', 'creativecommons');
            $is_front = FALSE;
            $show_Form = '0';
            if ($formId == NULL) {
                $action = 'createform';
                $editmode = FALSE;
                $titleInputValue = '';
                $imageInputValue = $this->_objConfig->getskinRoot().'_common/icons/cms/form_item.gif';
                $descInputValue = '';

                $bodyInputValue = '
<form name="sample" action="?module=forms&amp;action=savedata" method="post">
    <p><span style="color: rgb(128, 128, 128);"><strong><span style="font-size: larger;">Sample Form:</span></strong></span></p>
    <table width="100%" cellspacing="1" cellpadding="1" border="0" align="left">
        <tbody>
            <tr>
                <td>Employee Name</td>
                <td><input type="text" name="employeename" size="50" maxlength="50" /></td>
            </tr>
            <tr>
                <td>Employee Number</td>
                <td><input type="text" name="employeenumber" size="50" maxlength="50" /></td>
            </tr>
            <tr>
                <td>
                <p><input type="submit" name="submit" value="Submit" /></p>
                </td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
    <p>&nbsp;&nbsp;</p>
</form>
';

                $published->setChecked(TRUE);
                $formId = '';
                $arrForm = null;
            } else {
                $action = 'createform';
                $editmode = TRUE;
                $arrForm = $this->objForms->getForm($formId);
                //Removing Notices:
                $indexes = array('title', 'description', 'body');

                foreach ($indexes as $index){
                    if (!isset($arrForm[$index])) {
                        $arrForm[$index] = '';
                    }
                }

                $titleInputValue = $arrForm['title'];
                $descInputValue = $arrForm['description'];
                $bodyInputValue = stripslashes($arrForm['body']);

            }

            //setup form
            $objForm = new form('addfrm', $this->uri(array('action' => $action, 'id' => $formId), 'forms'));
            $objForm->setDisplayType(3);

            $tableContainer = new htmlTable();
            $tableContainer->width = "100%";
            $tableContainer->cellspacing = "0";
            $tableContainer->cellpadding = "0";
            $tableContainer->border = "0";
            $tableContainer->attributes = "align ='center'";

            $table = new htmlTable();
            //$table->width = "470px";
            $table->width = "100%";
            $table->cellspacing = "0";
            $table->cellpadding = "0";
            $table->border = "0";
            $table->attributes = "align ='center'";
            $this->loadClass('textinput', 'htmlelements');
            // Title Input
            $titleInput = new textinput ('title', $titleInputValue);
            $titleInput->cssId = 'input_form_title';

            $h3->str = $this->objLanguage->languageText('mod_forms_formtext', 'forms');
            $h3->type = 3;

            $table->startRow();
            $table->addCell($h3->show(), null, 'top', null, null, 'colspan="1"');
            //$table->addCell('(Click to Change)', null, 'bottom'); //TEMPLATE_IMAGE
            $table->addCell('', null, 'bottom');
            $table->endRow();

            $table->startRow();
            $table->addCell('', null, 'top', null, null, 'style="padding-bottom:6px"');
            $table->endRow();

            $table_input = new htmltable();

            $table_input->startRow();
            $table_input->addCell($this->objLanguage->languageText('word_title').': ');
            $table_input->addCell($titleInput->show());
            $table_input->endRow();

            $table->startRow();
            $table->addCell($table_input->show());
            $table->endRow();

            $h3->str = $this->objLanguage->languageText('word_introduction').' ('.$this->objLanguage->languageText('word_required').')';
            $h3->type = 3;

            //add hidden text input
            $table->row_attributes = '';

            //Adding the FCK_EDITOR

            if ($editmode) {

                if (isset($arrForm['body'])) {
                    $bodyInputValue = stripslashes($arrForm['body']);
                }else{
                    $bodyInputValue = null;
                }

            }

            $bodyInput = $this->newObject('htmlarea', 'htmlelements');
            $bodyInput->init('body', $bodyInputValue);
            $bodyInput->setContent($bodyInputValue);
            $bodyInput->setFormsToolBar();
            $bodyInput->loadCMSTemplates(); //TODO: Load Form Templates
            $bodyInput->height = '400px';

            //echo $bodyInput->show(); exit;

            $table->startRow();
            $table->addCell('', null, 'top', null, null, 'style="padding-bottom:10px"');
            $table->endRow();

            $table->startRow();
            $table->addCell($bodyInput->show(), null, 'top', null, null, 'colspan="2"');
            $table->endRow();

            //add the main body
            $table2 = new htmltable();
            //$table2->width = "220px";
            $table2->width = "100%";

            $h3->str = $this->objLanguage->languageText('mod_forms_formparams','forms');
            $h3->type = 3;

            $table2->startRow();
            $table2->addCell($h3->show(), null, 'top', null, null, 'colspan="2"');
            $table2->endRow();

            $table2->startRow();
            $table2->addCell('', null, 'top', null, null, 'style="padding-bottom:10px"');
            $table2->endRow();

            $table2->startRow();

            if (!$editmode) {
                $table2->addCell($this->getConfigFormTabs());
            }else{
                $table2->addCell($this->getConfigFormTabs($arrForm));
            }
            $table2->endRow();
            // Form Area

            //Header for main body
            $id = $this->getParam('id', '');
            $hiddenId = new hiddeninput('id', $id);

            $txt_action = new textinput('action',$action,'hidden');
            $table->startRow();
            $table->endRow();

            $pageParams = new layer();
            $pageParams->id = 'AddFormPageParams';
            $pageParams->str = $table2->show();

            $tableContainer->startRow();
            $tableContainer->addCell($table->show(),'', 'top', '', 'AddContentLeft');
            $tableContainer->addCell($pageParams->show(),'', 'top', '', 'AddContentRight');
            $tableContainer->endRow();

            //Add validation for title
            $errTitle = $this->objLanguage->languageText('mod_forms_entertitle', 'forms');
            $objForm->addRule('form_title', $errTitle, 'required');
            $objForm->addToForm($tableContainer->show());
            //$objForm->addToForm($div1->show());
            //add action
            $objForm->addToForm($txt_action);
            $objForm->addToForm($hiddenId->show());

            //body
            // $dialogForm = new form();


            // $dialogForm->addToForm($table2->show());
            //add page header for the body

            $display = $objForm->show();
            return $display;
        }

       /**
        * Method provides tabboxes for page artifacts
        * such as metadata and basic cms page behaviour artifacts
        * @param array Content The content to be modified
        * @return str tabs
        *
        */
        public function getConfigFormTabs($arrForms=NULL){

            //Defining Basic items to be displayed for First Tab

            //Using jQuery UI Tabs
            $tabs =$this->newObject('jqtabs','jquery');

            $tbl_basic = $this->newObject('htmltable','htmlelements');
            $tbl_advanced = $this->newObject('htmltable','htmlelements');
            $tbl_meta =  $this->newObject('htmltable','htmlelements');
            $tbl_lic =  $this->newObject('htmltable','htmlelements');
            $objdate = $this->getObject('datepickajax', 'popupcalendar');
            $publishing = $this->getObject('datepickajax', 'popupcalendar');
            $publishing_end = $this->getObject('datepickajax', 'popupcalendar');
            $h3 =$this->newObject('htmlheading', 'htmlelements');
            $published = new checkbox('published');

            if (is_array($arrForms)) {
                //Initializing options

                //Removing Notices:
                $indexes = array('published', 'description');
                foreach ($indexes as $index){
                    if (!isset($arrForms[$index])) {
                        $arrForms[$index] = '';
                    }
                }

                $published->setChecked($arrForms['published']);
                $visible = $arrForms['published'];
                $descInputValue = $arrForms['description'];
            }else{
                //Initializing options
                $published->setChecked(TRUE);
                $visible = '1';
                $descInputValue = '';
            }

            // Description Input
            $descInput = new textarea ('description', $descInputValue);
            $descInput->cssId = 'input_description';


            $tbl_basic->startRow();
            $tbl_basic->addCell($this->objLanguage->languageText('word_publish').': &nbsp; ', '100');
            $tbl_basic->addCell($this->getYesNoRadion('published', $visible, false));
            $tbl_basic->endRow();

            $tbl_basic->startRow();
            $tbl_basic->addCell($this->objLanguage->languageText('word_description').':', null, 'top', null, null, 'colspan="2"');
            $tbl_basic->endRow();

            $tbl_basic->startRow();
            $tbl_basic->addCell($descInput->show(), null, 'top', null, null, 'colspan="2"');
            $tbl_basic->endRow();

            $tabs->addTab($this->objLanguage->languageText('mod_forms_basic','forms'),$tbl_basic->show(),'',False,'');
            $tabs->cssClass = 'addContentTabs';

            return $tabs->show();
        }


       /**
        * Method to get the Top navigation menus for CMS admin
        * The method renders the top navigation based on pages being rendered
        * @param string action which is the page or action being called
        * @param string params which is any params to be passed to top navigation
        * @return str the string top navigation
        * @access public
        */
        public function topNav($action='home',$params=NULL){

            //Declare objects
            $tbl = $this->newObject('htmltable', 'htmlelements');
            $icon_publish = $this->newObject('geticon', 'htmlelements');

            $iconList = '';
            $extra = '';

            // Semi Global FCK Preview Function
            // Also global preview binding to any div id'd 'btn_preview_content' for showing the preview
            // of any FCK instance with id of 'body'
            $script = "<script type='text/javascript'>
                function FCKPreview(fckEditorInstance) {
                    try
                    {
                        var oEditor = FCKeditorAPI.GetInstance(fckEditorInstance);
                        try
                        {
                            oEditor.Commands.GetCommand('Preview').Execute();
                        }
                            catch (e) {}
                        //oEditor.Focus();
                    }
                        catch (e) {}

                }

                jQuery(document).ready(function(){
                    jQuery('#btn_preview_content').click(function(){
                        //jQuery('#must_apply').attr('value') = '1';
                        //jQuery('#must_preview').attr('value) = '1';
                        //document.getElementById('form_addfrm').action = '?module=forms&action=previewcontent';
                        //alert('going to : ' + document.getElementById('form_addfrm').action);
                        //document.getElementById('form_addfrm').submit();
                        //window.open('?module=forms&action=previewcontent&id=". $this->getParam('id') ."','selectimage','width=800,height=600,scrollbars=1,resizable=1');
                        //window.console.log('Getting the Body RAW style: ' + jQuery('#body').attr('value'));
                        //window.console.log('Getting the Body FCK style: ' + FCKPreview('body'));
                        //window.open('?module=cms&action=previewcontent&body=' + escape(jQuery('#body').attr('value')) + '&intro=' + escape(jQuery('#intro').attr('value')) + '&title=' + escape(jQuery('#input_title').attr('value')) + '&hide_title=' + escape(jQuery('#input_hide_title1').attr('value')) + '&hide_user=' + escape(jQuery('#input_hide_user1').attr('value')) + '&hide_date=' + escape(jQuery('#input_hide_date1').attr('value')),'selectimage','width=800,height=600,scrollbars=1,resizable=1');

                        //Will attach this to a better preview engine soon!
                        FCKPreview('body');
                    });
                });

            </script>";
            $this->appendArrayVar('headerParams', $script);

            switch ($action) {
                case 'viewforms':

                // New
                $url = $this->uri(array('action' => 'addform'), 'forms');
                $linkText = $this->objLanguage->languageText('word_new');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'new', $linkText, 'png', 'icons/cms/');

                // Cancel
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'createform':

                // Apply
                $script = '<script type="text/javascript">
                                            function applyChanges(){
                                                document.getElementById(\'must_apply\').value = \'1\';
                                                document.getElementById(\'form_addfrm\').submit();
                                            }
                                          </script>';

                $this->appendArrayVar('headerParams', $script);

                $url = "javascript:applyChanges();";
                $linkText = $this->objLanguage->languageText('word_apply');
                $iconList = $icon_publish->getCleanTextIcon('', $url, 'apply', $linkText, 'png', 'icons/cms/');

                // Save
                $url = "javascript:if(validate_addfrm_form(document.getElementById('form_addfrm')) == true){ document.getElementById('form_addfrm').submit(); }";
                $linkText = $this->objLanguage->languageText('word_save');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'save', $linkText, 'png', 'icons/cms/');

                // Cancel
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                // Preview
                $url = 'javascript:void(0)';
                $linkText = $this->objLanguage->languageText('mod_forms_preview', 'forms');
                $iconList .= $icon_publish->getCleanTextIcon('btn_preview_content', $url, 'preview', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                default:
                // New
                $url = $this->uri(array('action' => 'addform'), 'forms');
                $linkText = $this->objLanguage->languageText('word_new');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'new', $linkText, 'png', 'icons/cms/');

                // Cancel
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;
            }

            return $tbl->show();



        }

       /**
        * Method to get the Yes/No radio  box
        *
        * @param  string $name The name of the radio box
        * @param string $selected The option to set as selected
        * @access public
        * @return string Html for radio buttons
        */
        public function getYesNoRadion($name, $selected = '1', $showIcon = true)
        {
            $visibleIcon = '';
            $notVisibleIcon = '';

            if ($showIcon) {
                //Get visible not visible icons
                $objIcon = $this->newObject('geticon', 'htmlelements');
                //Not visible
                $objIcon->setIcon('not_visible');
                $objIcon->title = $this->objLanguage->languageText('phrase_notpublished');
                $notVisibleIcon = $objIcon->show();
                //Visible
                $objIcon->setIcon('visible');
                $objIcon->title = $this->objLanguage->languageText('word_published');
                $visibleIcon = $objIcon->show();
            }

            $objRadio = new radio ($name);

            $objRadio->addOption('1', $visibleIcon.$this->objLanguage->languageText('word_yes'));
            $objRadio->addOption('0', $notVisibleIcon.$this->objLanguage->languageText('word_no').'&nbsp;'.'&nbsp;');

            $objRadio->setSelected($selected);

            $objRadio->setBreakSpace(' &nbsp; ');

            return $objRadio->show();
        }


       /**
        * Method to the true/false tick
        *
        * @param  $isCheck Booleans value with either TRUE|FALSE
        * @return string icon
        * @access public
        */
        public function getCheckIcon($isCheck, $returnFalse = TRUE)
        {
            $objIcon = $this->newObject('geticon', 'htmlelements');

            if ($isCheck) {
                $objIcon->setIcon('visible', 'gif');
                $objIcon->title = $this->objLanguage->languageText('word_published');
            } else {
                if ($returnFalse) {
                    $objIcon->setIcon('not_visible', 'gif');
                    $objIcon->title = $this->objLanguage->languageText('phrase_notpublished');
                }
            }

            return $objIcon->show();
        }

   /**
    * Method to return the Mapping Form
    *
    * @param string $mapId The id of the mapping to be edited.Default NULL for adding new mapping
    * @access public
    */
        public function getFilterCodeForm($formId = '')
        {
            if ($formId == '') {
                return 'Caught Exception: No formId specified';
            }

            $table = new htmlTable();
            $table->width = "100%";
            $table->cellspacing = "0";
            $table->cellpadding = "10";
            $table->border = "0";
            $table->attributes = "align ='center'";

            //Close the form
            $btnClose = "<input type='button' id='close' name='close' value='Close' style='width:50px;' onclick='Boxy.get(this).hide(); return false'/>";

            $filterText = "<input type='text' value='[FORM]{$formId}[/FORM]' width='200px' />";

            //Adding All to Container here
            $table->startRow();
            $table->addCell($filterText, '', '', 'center', '', '','');
            $table->endRow();

            $table->startRow();
            $table->addCell('&nbsp;', '', '', 'center', '', '','');
            $table->endRow();

            $table->startRow();
            $table->addCell($btnClose, '', '', 'center', '', '','');
            $table->endRow();

            //Stripping New Lines and preparing for boxy input = (Facebook style window)
            $display = str_replace("\n", '', $table->show());

            return $display;
        }


       /**
        * Method to return the form records resultset grid for the given form id
        *
        * @param string $formId The id of the form item to report results on
        * @access public
        * @author Charl Mert
        */
        public function getResultsForm($formId = NULL)
        {

            $h3 = $this->newObject('htmlheading', 'htmlelements');
            $objIcon = $this->getObject('geticon','htmlelements');

			$records = $this->objFormRecords->getRecord($formId);

            $table = new htmlTable();
            $table->cellspacing = "0";
            $table->cellpadding = "0";
            $table->border = "0";
            $table->attributes = "align ='center'";

			$formName = '';
			$formTitle = '';
			$formIp = '';
			$formBrowser = '';
			if (!empty($records)) {
				$formTitle = $records['title'];
				$formName = $records['name'];
				$formIp = $records['ip'];
				$formBrowser = $records['browser'];

            $h3->str = $formTitle;
            $h3->type = 3;

            $table->startRow();
            $table->addCell($h3->show(), null, 'top', null, null, 'colspan="1"');
            $table->endRow();

            $table->startrow();
            $table->addcell('Name', null, 'top', null, null, 'style="padding-bottom:6px"');
            $table->addcell('Title', null, 'top', null, null, 'style="padding-bottom:6px"');
            $table->addcell('IP', null, 'top', null, null, 'style="padding-bottom:6px"');
            $table->addcell('Browser', null, 'top', null, null, 'style="padding-bottom:6px"');
            $table->endrow();



			} else {
	            $table->startrow();
	            $table->addcell('No forms have any records in yet', null, 'top', null, null, 'style="padding-bottom:6px"');
	            $table->endrow();
			}
            $display = $table->show();
            return $display;
        }



    }

?>