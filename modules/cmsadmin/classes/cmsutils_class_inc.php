<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
    }
    // end security check

/**
 * This object hold all the utility method that the cms modules might need
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert
 */

    class cmsutils extends object
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
                $this->_objQuery =  $this->newObject('jquery', 'jquery');
                $this->_objBox = $this->newObject('jqboxy', 'jquery');
                $this->_objUserPerm = $this->newObject('dbuserpermissions', 'cmsadmin');
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

                $objModule =$this->newObject('modules', 'modulecatalogue');
                if ($objModule->checkIfRegistered('context')) {
                    $this->inContextMode = $this->_objContext->getContextCode();
                    $this->contextCode = $this->_objContext->getContextCode();
                } else {
                    $this->inContextMode = FALSE;
                }

                $this->objDateTime = $this->getObject('dateandtime', 'utilities');

                // Load scriptaclous since we can no longer guarantee it is there
                $scriptaculous = $this->getObject('scriptaculous', 'prototype');
                $this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));

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
        * Method to detemine the access
        *
        * @param int $access The access
        * @return string Registered if 1 else Public
        * @access public
        */
        public function getAccess($access)
        {
            if ($access == 1) {
                return $this->objLanguage->languageText('word_registered');
            } else {
                return $this->objLanguage->languageText('word_public');
            }
        }

       /**
        * Method to get the Yes/No radio  box
        *
        * @param  string $name The name of the radio box
        * @param string $selected The option to set as selected
        * @access public
        * @return string Html for radio buttons
        */
        public function getYesNoRadion($name, $selected = 1, $showIcon = true)
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

            $objRadio->addOption(1, $visibleIcon.$this->objLanguage->languageText('word_yes'));
            $objRadio->addOption(0, $notVisibleIcon.$this->objLanguage->languageText('word_no').'&nbsp;'.'&nbsp;');

            $objRadio->setSelected($selected);

            //$objRadio->setBreakSpace(' &nbsp; ');

            return $objRadio->show();
        }

       /**
        * Method to get the Access List dropdown
        *
        * @access public
        * @param string $name The name of the field
        * @return string Html for access dropdown
        */
        public function getAccessList($name)
        {
            $objDropDown = new dropdown($name);
            //fill the drop down with the list of images
            //TODO
            $objDropDown->addOption('0', $this->objLanguage->languageText('word_public'));
            $objDropDown->addOption('1', $this->objLanguage->languageText('word_registered'));
            $objDropDown->setSelected('0');
            $objDropDown->extra = 'size="2"';
            return $objDropDown->show();
        }

       /**
        * Method to get the layout options for a section
        * At the moment there are 4 types of layouts
        * The layouts will be diplayed as images for selection
        * The layouts templates will be displayed as images
        *
        * @param string $name The of the of the field
        * @return string Html for selecting layout type
        * @access public
        */
        public function getLayoutOptions($name, $id)
        {
            $objLayouts =$this->newObject('dblayouts', 'cmsadmin');
            $arrLayouts = $objLayouts->getLayouts();
            $arrSection = $this->_objSections->getSection($id);
            $str = '<table><tr>';

            $firstOneChecked = 'checked="checked"';
            foreach ($arrLayouts as $layout) {
                if ($arrSection['layout'] == $layout['id']) {
                    $firstOneChecked = '';
                    break;
                }
            }

            $i = 0;
            foreach ($arrLayouts as $layout) {
                if ($firstOneChecked != '') {
                    if ($i == 0) {
                        $checked = $firstOneChecked;
                    } else {
                        $checked = '';
                    }
                } else {
                    if ($arrSection['layout'] == $layout['id']) {
                        $checked = 'checked="checked"';
                    } else {
                        $checked = '';
                    }
                }

                $str .= '<td align="center">
                        <input type="radio" name="'.$name.'" value="'.$layout['id'].'" class="transparentbgnb" id="input_layout0" '.$checked.' />&nbsp;'.$layout['description'].'
                        <p/>
                        <label for ="input_layout0">
                        <img src ="'.$this->getResourceUri($layout['imagename'], 'cmsadmin').'"/>
                        </label>
                        </td>';
                $i++;
            }

            $str .= '</tr></table>';
            return $str;
        }


      /**
        * Method to get the control panel for the context
        * 
        * @param 
        * @return string
        * 
        */
        public function getContextControlPanel()
        {

            $objLayer = $this->newObject('layer', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');

            $tbl = new htmltable();
            $tbl->cellspacing = '5';
            $tbl->cellpadding = '5';
            $tbl->width = "45%";
            $tbl->align = "left";

            $link =$this->newObject('link', 'htmlelements');
            $objIcon = $this->newObject('geticon', 'htmlelements');

            //view content link
            $link = $this->objLanguage->languageText('mod_cmsadmin_viewcontent', 'cmsadmin');
            $arrSection = $this->_objSections->getSectionByContextCode();
            $url = $this->uri(array('action' => 'viewsection','id' => $arrSection['id']));
            $icnViewContent = $objIcon->getBlockIcon($url, 'add_article', $link, 'png', 'icons/cms/');

            //content link
            $link = $this->objLanguage->languageText('mod_cmsadmin_contentitem', 'cmsadmin');
            $url = $this->uri(array('action' => 'addcontent'));
            $icnContent = $objIcon->getBlockIcon($url, 'add_article', $link, 'png', 'icons/cms/');

            //Import link
            $link = $this->objLanguage->languageText('mod_cmsadmin_import', 'cmsadmin'); 
            $url = $this->uri(array('action' => 'import'));
            $icnImport = $objIcon->getBlockIcon($url, 'import', $link, 'png', 'icons/cms/');

            //Create export manager link
            $link = $this->objLanguage->languageText('mod_cmsadmin_export', 'cmsadmin');
            $url = $this->uri(array('action' => 'export'), 'cmsadmin');
            $icnExport = $objIcon->getBlockIcon($url, 'export', $link, 'png', 'icons/cms/');

            //Page Organisor
            $link = $this->objLanguage->languageText('mod_cmsadmin_organisor', 'cmsadmin');
            $url = $this->uri(array('action' => 'organisor'), 'cmsadmin');
            $icnPageOrganisor = $objIcon->getBlockIcon($url, 'organisor', $link, 'png', 'icons/cms/');

            // Create archive / trash manager link
            $url = $this->uri(array('action' => 'trashmanager'));
            $link = $this->objLanguage->languageText('mod_cmsadmin_archive', 'cmsadmin');
            $icnArchive = $objIcon->getBlockIcon($url, 'trash', $link, 'png', 'icons/cms/');

            // RSS feeds manager link
            $url = $this->uri(array('action' => 'createfeed'));
            $link = $this->objLanguage->languageText('mod_cmsadmin_rss', 'cmsadmin');
            $icnRss = $objIcon->getBlockIcon($url, 'rss', $link, 'png', 'icons/cms/');

            // Menu manager link
            //$url = $this->uri(array('action' => 'managemenus'));
            $url = $this->uri(array('action' => 'menustyle'));
            $link = $this->objLanguage->languageText('mod_cmsadmin_menu', 'cmsadmin');
            $icnMenu = $objIcon->getBlockIcon($url, 'menu2', $link, 'png', 'icons/cms/');

            // File manager link
            $url = $this->uri('', 'filemanager');
            $link = $this->objLanguage->languageText('phrase_uploadfiles');
            $icnFiles = $objIcon->getBlockIcon($url, 'media', $link, 'png', 'icons/cms/');

            $tbl->startRow();
            $tbl->addCell($icnViewContent);
            $tbl->addCell($icnContent);
            //$tbl->addCell($icnImport);
            // $tbl->addCell($icnExport);
            $tbl->addCell($icnMenu);
            //$tbl->addCell($icnPageOrganisor);
            $tbl->endRow();

            $tbl->startRow();
            $tbl->addCell($icnArchive);
            $tbl->addCell($icnRss);

            $tbl->addCell($icnFiles);
            $tbl->addCell('');
            $tbl->endRow();

            $tbl->startRow();
            $tbl->addCell('&nbsp;');
            $tbl->endRow();

            $tbl->startRow();
            $tbl->endRow();

            $objLayer->str = $tbl->show();
            $objLayer->id = 'cpanel';
            $fboxcontent = $objLayer->show();

            return $this->objFeatureBox->showContent('',$fboxcontent);
        }


       /**
        * Method to get the Control Panel
        * The control panel provides the first navigation screen to the CMS admin module
        * 
        * @return string The control Panel display data
        *@access public
        */
        public function getControlPanel()
        {
            $objLayer = $this->newObject('layer', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');

			$this->_objQuery->loadCornerPlugin();

			ob_start();
			?>
			<script type="text/javascript">
				jQuery(function(){
			        jQuery('#cpanel div.icon a').corner();
			        jQuery('#cpanel div.icon a:hover').corner();
			        jQuery('#cpanel div.icon').corner();
			        //jQuery('.smallicon').corner();
			    });
			</script>
			<?php
			$script = ob_get_contents();
			ob_end_clean();

			$this->appendArrayVar('headerParams', $script);


            $tbl = new htmltable();
            $tbl->cellspacing = '5';
            $tbl->cellpadding = '5';
            $tbl->width = "45%";
			$tbl->border = '0';
            $tbl->align = "left";

            $link = $this->newObject('link', 'htmlelements');
            $objIcon = $this->newObject('geticon', 'htmlelements');


            //content link
            $link = $this->objLanguage->languageText('mod_cmsadmin_contentitem', 'cmsadmin');
            $url = $this->uri(array('action' => 'addcontent'));
            $icnContent = $objIcon->getCleanBlockIcon($url, 'new_big', $link, 'png', 'icons/cms/');

            //sections link
            $link = $this->objLanguage->languageText('mod_cmsadmin_sectionmanager', 'cmsadmin'); 
            $url = $this->uri(array('action' => 'sections'));
            $icnSection = $objIcon->getCleanBlockIcon($url, 'section', $link, 'png', 'icons/cms/');

            //Frontpage Security
            if ($this->_objUserPerm->canAddToFrontPage()) {
                $link = $this->objLanguage->languageText('mod_cmsadmin_frontpagemanager', 'cmsadmin');
                $url = $this->uri(array('action' => 'frontpages'), 'cmsadmin');
                $icnFront = $objIcon->getCleanBlockIcon($url, 'frontpage', $link, 'png', 'icons/cms/');
            } else {
                $this->_objDisplay = $this->getObject('cmsdisplay', 'cmsadmin');

                $message = $this->objLanguage->languageText('mod_cmsadmin_nofrontpageaccess', 'cmsadmin');

                $innerHtml = $this->_objDisplay->getAlertForm('', $message);
                $this->_objBox->setHtml($innerHtml);
                $this->_objBox->setTitle('Notice: Access Denied');
                $this->_objBox->attachClickEvent('btn_frontpage');// . $userPerm['id']);

                $url = '#';
                $link = $this->objLanguage->languageText('mod_cmsadmin_frontpagemanager', 'cmsadmin');
                $icnFront = $objIcon->getCleanBlockIconId('btn_frontpage', $url, 'frontpage', $link, 'png', 'icons/cms/');
    
            }

            // Create archive / trash manager link
            $url = $this->uri(array('action' => 'trashmanager'));
            $link = $this->objLanguage->languageText('mod_cmsadmin_archive', 'cmsadmin');
            $icnArchive = $objIcon->getCleanBlockIcon($url, 'trash', $link, 'png', 'icons/cms/');

            // RSS feeds manager link
            $url = $this->uri(array('action' => 'createfeed'));
            $link = $this->objLanguage->languageText('mod_cmsadmin_rss', 'cmsadmin');
            $icnRss = $objIcon->getCleanBlockIcon($url, 'rss', $link, 'png', 'icons/cms/');

            // Menu manager link
            //$url = $this->uri(array('action' => 'managemenus'));
            $url = $this->uri(array('action' => 'menustyle'));
            $link = $this->objLanguage->languageText('mod_cmsadmin_menu', 'cmsadmin');
            $icnMenu = $objIcon->getCleanBlockIcon($url, 'menu2', $link, 'png', 'icons/cms/');

            // File manager link
            $url = $this->uri('', 'filemanager');
            $link = $this->objLanguage->languageText('phrase_uploadfiles');
            $icnFiles = $objIcon->getCleanBlockIcon($url, 'media', $link, 'png', 'icons/cms/');

            //permissions link
            $link = $this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin'); 
            //$link = "Permissions Manager"; 
            $url = $this->uri(array('action' => 'permissions'));
            $icnPermissions = $objIcon->getCleanBlockIcon($url, 'permissions', $link, 'png', 'icons/cms/');

            //Templates link
            $link = $this->objLanguage->languageText('mod_cmsadmin_templates', 'cmsadmin'); 
            $url = $this->uri(array('action' => 'templates'));
            $icnTemplates = $objIcon->getCleanBlockIcon($url, 'templates', $link, 'png', 'icons/cms/');

            //Short URL link
            $link = $this->objLanguage->languageText('mod_cmsadmin_shorturl', 'cmsadmin'); 
            $url = '?module=shorturl&ref=cmsadmin';
            $icnShortURL = $objIcon->getCleanBlockIcon($url, 'shorturl60x60', $link, 'png', 'icons/cms/');

			//Flag URL link
            $link = $this->objLanguage->languageText('mod_cmsadmin_flag', 'cmsadmin'); 
            $url = $this->uri(array('action' => 'flag'));
            $icnFlag = $objIcon->getCleanBlockIcon($url, 'flag', $link, 'png', 'icons/cms/');
			
            //Configuration link
            $link = $this->objLanguage->languageText('mod_cmsadmin_config', 'cmsadmin'); 
            $url = '?module=sysconfig&action=step2&pmodule_id=cmsadmin';
            $icnConfig = $objIcon->getCleanBlockIcon($url, 'config', $link, 'png', 'icons/cms/');


            $tbl->startRow();
            $tbl->addCell($icnContent);
            $tbl->addCell($icnSection);
            $tbl->addCell($icnFront);
            $tbl->addCell($icnArchive);
            $tbl->addCell($icnTemplates);
            $tbl->endRow();

            $tbl->startRow();
            $tbl->addCell($icnRss);
            
            //$test = $this->_objSysConfig->getValue('admin_only_menu', 'cmsadmin');
            //var_dump($test);
            if ($this->_objSysConfig->getValue('admin_only_menu', 'cmsadmin') == 'TRUE') {
                if ($this->_objUser->inAdminGroup($this->_objUser->userId())) {
                    $tbl->addCell($icnMenu);
                }
            } else {
                $tbl->addCell($icnMenu);
            }
            
            $tbl->addCell($icnFiles);
            $tbl->addCell($icnPermissions);
            $tbl->addCell($icnShortURL);
            $tbl->endRow();

            $tbl->startRow();
            $tbl->addCell($icnFlag);
            $tbl->addCell($icnConfig);
            $tbl->endRow();

            $tbl->startRow();
            $tbl->addCell('&nbsp;');
            $tbl->endRow();

            $tbl->startRow();
            $tbl->endRow();

            $objLayer->str = $tbl->show();
            $objLayer->id = 'cpanel';
            $fboxcontent = $objLayer->show();

            return $this->objFeatureBox->showContent('',$fboxcontent);

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
                        //document.getElementById('form_addfrm').action = '?module=cmsadmin&action=previewcontent';
                        //alert('going to : ' + document.getElementById('form_addfrm').action);
                        //document.getElementById('form_addfrm').submit();
                        //window.open('?module=cmsadmin&action=previewcontent&id=". $this->getParam('id') ."','selectimage','width=800,height=600,scrollbars=1,resizable=1');
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

                case 'flag':

                // New
                $url = '#';
                $linkText = $this->objLanguage->languageText('word_new');
                $iconList .= $icon_publish->getCleanTextIcon('btn_new', $url, 'new', $linkText, 'png', 'icons/cms/');

                // Cancel
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;
            
                case 'viewtemplates':

                // New
                $url = $this->uri(array('action' => 'addtemplate'), 'cmsadmin');
                $linkText = $this->objLanguage->languageText('word_new');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'new', $linkText, 'png', 'icons/cms/');

                // Cancel           
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'createtemplate':

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
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_viewcms', 'cmsadmin');
                $iconList .= $icon_publish->getCleanTextIcon('btn_preview_content', $url, 'preview', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'createfeed':

                /*
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
                */
                // Save
                
                //$url = "javascript:if(validate_addfrm_form(document.getElementById('form_addfrm')) == true){ document.getElementById('form_addfrm').submit(); }";
                $url = "javascript:document.getElementById('form_addrss').submit();";
                $linkText = $this->objLanguage->languageText('word_save');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'save', $linkText, 'png', 'icons/cms/');

                // Cancel           
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;


                case 'editmenu':

                // Apply
                //$url = $this->uri(array('action' => 'releaselock'), 'cms');
                $script = '
<script type="text/javascript">

    function applyChanges(){
            document.getElementById(\'must_apply\').value = \'1\';
            document.getElementById(\'form_addmenu\').submit();
            return true;
    }
</script>';

                $this->appendArrayVar('headerParams', $script);

                //$url = "javascript:document.getElementById(must_apply).value = '1';document.getElementById('form_addmenu').submit();";
                $url = "javascript:applyChanges()";
                $linkText = $this->objLanguage->languageText('word_apply');
                $iconList = $icon_publish->getCleanTextIcon('', $url, 'apply', $linkText, 'gif', 'icons/cms/');

                // Save
                $url = "javascript:document.getElementById('form_addmenu').submit();";
                $linkText = $this->objLanguage->languageText('word_save');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'save', $linkText, 'gif', 'icons/cms/');

                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'cancel', $linkText, 'gif', 'icons/cms/');

                // Preview
                $url = 'javascript:void(0)';
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_viewcms', 'cmsadmin');
                $iconList .= $icon_publish->getCleanTextIcon('btn_preview_content', $url, 'preview', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;



                case 'createcontent':

                // Apply
                $script = "
                    <script type='text/javascript'>
                        jQuery(document).ready(function(){
                            jQuery('#btn_add_content_apply').click(function(){
                                if(validate_addfrm_form(document.getElementById('form_addfrm')) == true){ 
                                    document.getElementById('must_apply').value = '1';
                                    document.getElementById('form_addfrm').submit();
                                } 
                            })
                        });
                    </script>";
                $this->appendArrayVar('headerParams', $script);                

                $url = "javascript:void(0)";
                $linkText = $this->objLanguage->languageText('word_apply');
                $iconList = $icon_publish->getCleanTextIcon('btn_add_content_apply', $url, 'apply', $linkText, 'png', 'icons/cms/');

                // Save

                // Save jQuery Handle
                $script = "
                    <script type='text/javascript'>
                        jQuery(document).ready(function(){
                            jQuery('#btn_add_content').click(function(){
                                if(validate_addfrm_form(document.getElementById('form_addfrm')) == true){ 
                                    document.getElementById('form_addfrm').submit();
                                } 
                            })
                        });
                    </script>";

                $this->appendArrayVar('headerParams', $script);

                //$url = "javascript:if(validate_addfrm_form(document.getElementById('form_addfrm')) == true){ document.getElementById('form_addfrm').submit(); }";

                $url = "#";
                $linkText = $this->objLanguage->languageText('word_save');

                $objLayer = new layer();
                $objLayer->id = 'btn_add_content';
                $objLayer->str = $icon_publish->getCleanTextIcon('', $url, 'save', $linkText, 'png', 'icons/cms/');
                $iconList .= $objLayer->show();
                //$iconList .= $icon_publish->getCleanTextIcon('', $url, 'save', $linkText, 'png', 'icons/cms/');

                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                // Preview
                $url = "javascript:void(0)";
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_viewcms', 'cmsadmin');
                $iconList .= $icon_publish->getCleanTextIcon('btn_preview_content', $url, 'preview', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'sections':

                // New - add
                $url = $this->uri(array('action' => 'addsection'), 'cmsadmin');
                $linkText = $this->objLanguage->languageText('word_new');
                $iconList = $icon_publish->getCleanTextIcon('', $url, 'new_folder', $linkText, 'png', 'icons/cms/');

                // Publish
                $alertText = $this->objLanguage->languageText('mod_cmsadmin_selectpublishlist', 'cmsadmin');
                $url = "javascript:if(checkSelect('select','input_arrayList[]')==false){alert('{$alertText}');}else{submitbutton('select','publish');}";
                $linkText = $this->objLanguage->languageText('word_publish');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'publish', $linkText, 'png', 'icons/cms/');

                // Unpublish
                $alertText = $this->objLanguage->languageText('mod_cmsadmin_selectunpublishlist', 'cmsadmin');
                $url = "javascript:if(checkSelect('select','input_arrayList[]')==false){alert('{$alertText}');}else{submitbutton('select','unpublish');}";
                $linkText = $this->objLanguage->languageText('word_unpublish');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'unpublish', $linkText, 'png', 'icons/cms/');


                /*
                // Copy
                $url = $this->uri('', 'cmsadmin');
                $linkText = $this->objLanguage->languageText('word_copy');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'copy', $linkText, 'gif', 'icons/cms/');
                */


                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                // Preview
                //$url = 'javascript:void(0)';
                //$linkText = $this->objLanguage->languageText('mod_cmsadmin_viewcms', 'cmsadmin');
                //$iconList .= $icon_publish->getCleanTextIcon('btn_preview_content', $url, 'preview', $linkText, 'png', 'icons/cms/', $extra);

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'frontpage':
                // Publish
                $alertText = $this->objLanguage->languageText('mod_cmsadmin_selectpublishlist', 'cmsadmin');
                $url = "javascript: if(checkSelect('select','input_arrayList[]')==false){alert('{$alertText}');}else{submitbutton('select','publish');}";
                $linkText = $this->objLanguage->languageText('word_publish');
                $iconList = $icon_publish->getCleanTextIcon('', $url, 'publish', $linkText, 'png', 'icons/cms/');

                // Unpublish
                $alertText = $this->objLanguage->languageText('mod_cmsadmin_selectunpublishlist', 'cmsadmin');
                $url = "javascript:if(checkSelect('select','input_arrayList[]')==false){alert('{$alertText}');}else{submitbutton('select','unpublish');}";
                $linkText = $this->objLanguage->languageText('word_unpublish');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'unpublish', $linkText, 'png', 'icons/cms/');

                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                // Preview
                $url = 'javascript:void(0)';
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_viewcms', 'cmsadmin');
                $iconList .= $icon_publish->getCleanTextIcon('btn_preview_content', $url, 'preview', $linkText, 'png', 'icons/cms/', $extra);

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'permissions':


                if ($this->_objUserPerm->canEditUserPermissions()) {
                    //user link
                    $link = $this->objLanguage->languageText('mod_cmsadmin_permissionsusericontext', 'cmsadmin');
                    $url = $this->uri(array('action' => 'permissionsuser'));
                    $iconList .= $icon_publish->getCleanTextIcon('', $url, 'user_med', $link, 'png', 'icons/cms/');
                }

                //permissions link
                $link = $this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin'); 
                //$link = "Permissions Manager"; 
                $url = $this->uri(array('action' => 'permissions'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'permissions_med', $link, 'png', 'icons/cms/');

                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'userpermissions':

                // New
                $url = '#';
                $linkText = $this->objLanguage->languageText('word_new');
                $iconList .= $icon_publish->getCleanTextIcon('btn_new', $url, 'new', $linkText, 'png', 'icons/cms/');

                if ($this->_objUserPerm->canEditUserPermissions()) {
                    //user link
                    $link = $this->objLanguage->languageText('mod_cmsadmin_permissionsusericontext', 'cmsadmin');
                    $url = $this->uri(array('action' => 'permissionsuser'));
                    $iconList .= $icon_publish->getCleanTextIcon('', $url, 'user_med', $link, 'png', 'icons/cms/');
                }

                //permissions link
                $link = $this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin'); 
                //$link = "Permissions Manager"; 
                $url = $this->uri(array('action' => 'permissions'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'permissions_med', $link, 'png', 'icons/cms/');

                // Cancel           
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;


                case 'addpermissions':

                //permissions link
                $link = $this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin'); 
                //$link = "Permissions Manager"; 
                $url = $this->uri(array('action' => 'permissions'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'permissions_med', $link, 'png', 'icons/cms/');

                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'view_permissions_section':

                //permissions link
                $link = $this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin'); 
                //$link = "Permissions Manager"; 
                $url = $this->uri(array('action' => 'permissions'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'permissions_med', $link, 'png', 'icons/cms/');

                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'addpermissions_user_group':

                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'addsection':

                // Upload
                //$url = $this->uri(array('action' => 'uploadimage'), 'cmsadmin');
                //$linkText = $this->objLanguage->languageText('word_upload');
                //$iconList = $icon_publish->getCleanTextIcon('', $url, 'upload', $linkText, 'gif', 'icons/cms/');

                // Save
                $url = "javascript: if(validate_addsection_form(document.getElementById('addsection')) == true){ document.getElementById('addsection').submit(); }";
                $linkText = $this->objLanguage->languageText('word_save');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'save', $linkText, 'png', 'icons/cms/');

                // Cancel
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                // Preview
                $url = 'javascript:void(0)';
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_viewcms', 'cmsadmin');
                $iconList .= $icon_publish->getCleanTextIcon('btn_preview_content', $url, 'preview', $linkText, 'png', 'icons/cms/', $extra);

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'viewsection':

                // Section manager
                $url = $this->uri(array('action' => 'sections'), 'cmsadmin');
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_sectionmanager', 'cmsadmin');
                $iconList = $icon_publish->getCleanTextIcon('', $url, 'section_tool', $linkText, 'png', 'icons/cms/');

                //Frontpage Security
                if ($this->_objUserPerm->canAddToFrontPage()) {
                    // front page manager
                    $url = $this->uri(array('action' => 'frontpages'), 'cmsadmin');
                    $linkText = $this->objLanguage->languageText('mod_cmsadmin_frontpagemanager', 'cmsadmin');
                    $iconList .= $icon_publish->getCleanTextIcon('', $url, 'frontpage_small', $linkText, 'png', 'icons/cms/');
                } else {
                    $this->_objDisplay = $this->getObject('cmsdisplay', 'cmsadmin');

                    $message = $this->objLanguage->languageText('mod_cmsadmin_nofrontpageaccess', 'cmsadmin');

                    $innerHtml = $this->_objDisplay->getAlertForm('', $message);
                    $this->_objBox->setHtml($innerHtml);
                    $this->_objBox->setTitle('Notice: Access Denied');
                    $this->_objBox->attachClickEvent('btn_frontpage');// . $userPerm['id']);

                    $url = '#';
                    $linkText = $this->objLanguage->languageText('mod_cmsadmin_frontpagemanager', 'cmsadmin');
                    $iconList .= $icon_publish->getCleanTextIcon('btn_frontpage', $url, 'frontpage_small', $linkText, 'png', 'icons/cms/');
                }


                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');


                $script = "<script type='text/javascript'>
                jQuery(document).ready(function(){
                    jQuery('#btn_preview_section').click(function(){
                        window.open('?module=cms&action=showsection&id=" . $this->getParam('id', '') . "','width=800,height=600,scrollbars=1,resizable=1');
                    });
                });

                </script>";

                $this->appendArrayVar('headerParams', $script);

                // Preview
                $url = 'javascript:void(0)';
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_viewcms', 'cmsadmin');
                $iconList .= $icon_publish->getCleanTextIcon('btn_preview_section', $url, 'preview', $linkText, 'png', 'icons/cms/', $extra);			 		

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'trash':

                // Restore
                $alertText = $this->objLanguage->languageText('mod_cmsadmin_selectrestorelist', 'cmsadmin');
                $url = "javascript: if(checkSelect('select','input_arrayList[]')==false){alert('{$alertText}');}else{submitbutton('select','restore');}";
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_restore', 'cmsadmin');
                $iconList = $icon_publish->getCleanTextIcon('', $url, 'restore', $linkText, 'png', 'icons/cms/');

                // Restore sections
                $alertText = $this->objLanguage->languageText('mod_cmsadmin_selectrestoresections', 'cmsadmin');
                $url = "javascript: if(checkSelect('selectsections','input_arrayList[]')==false){alert('{$alertText}');}else{submitbutton('selectsections','restore');}";
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_restoresections', 'cmsadmin');
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'restoresection', $linkText, 'png', 'icons/cms/');

                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                // Preview
                $url = 'javascript:void(0)';
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_viewcms', 'cmsadmin');
                $iconList .= $icon_publish->getCleanTextIcon('btn_preview_content', $url, 'preview', $linkText, 'png', 'icons/cms/', $extra);

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                case 'menu':

                                                                /* Switch menu style
                                                                   $url = $this->uri(array('action' => 'menustyle'), 'cmsadmin');
                                                                   $linkText = $this->objLanguage->languageText('phrase_menustyle');
                                                                   $iconList = $icon_publish->getCleanTextIcon('', $url, 'menu2', $linkText, 'png', 'icons/cms/');

                                                                // New menu
                                                                $url = $this->uri(array('action' => 'addnewmenu','pageid'=>'0','add'=>'TRUE'), 'cmsadmin');
                                                                $linkText = $this->objLanguage->languageText('word_new');
                                                                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'new', $linkText, 'gif', 'icons/cms/');
                                                                 */

                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList .= $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                // Preview
                $url = 'javascript:void(0)';
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_viewcms', 'cmsadmin');
                $iconList .= $icon_publish->getCleanTextIcon('btn_preview_content', $url, 'preview', $linkText, 'png', 'icons/cms/', $extra);

                return '<p style="align:right;">'.$iconList.'</p>';
                break;

                default:
                // Cancel	 		
                $url = "javascript:history.back();";
                $linkText = ucwords($this->objLanguage->languageText('word_back'));
                $iconList = $icon_publish->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');

                // Preview
                $url = 'javascript:void(0)';
                $linkText = $this->objLanguage->languageText('mod_cmsadmin_viewcms', 'cmsadmin');
                $iconList .= $icon_publish->getCleanTextIcon('btn_preview_content', $url, 'preview', $linkText, 'png', 'icons/cms/', $extra);

                return '<p style="align:right;">'.$iconList.'</p>';
                break;
            }

            return $tbl->show();



        }


       /**
        * Method provides tabboxes for page artifacts
        * such as metadata and basic cms page behaviour artifacts
        * @param array Content The content to be modified
        * @return str tabs
        *
        */
        public function getConfigTemplateTabs($arrContent=NULL){

	   /**
		* Defining Basic items to be displayed for 
		* First Tab
		*/
            //var_dump($arrContent);
            //$tabs =$this->newObject('tabcontent','htmlelements');

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

            $frontPage = new checkbox('frontpage');
            $frontPage->value = 1;
            $frontPage->extra = 'onclick="doToggle(\'toggle_layer_param\'); doToggle(\'introdiv\')"';

            if ($this->_objUserPerm->canAddToFrontPage()) {
                $script = "
                    <script language='javascript'>
                        //jQuery standard toggle effect
                
                        function doToggle(id, chk) {
                            chkBox = document.getElementById('input_frontpage');

                            if (chkBox.checked){
                                jQuery('#' + id).addClass('toggleShow').show('slow');
                            } else {
                                jQuery('#' + id).addClass('toggleShow').hide('slow');
                            }
                        }

                        jQuery(document).ready(function(){
                            jQuery('#toggle_layer_param').addClass('toggleShow').hide();
                            jQuery('#introdiv').addClass('toggleShow').hide();

                            //Adding click event for checkbox in simple parameters tab
                            /*
                            jQuery('#input_frontpage').click(function() {
                                alert('Hello world!');
                            });
                            */
                        });
                        </script>";

                
                $this->appendArrayVar('headerParams', $script);
            }

            $show_content = '0';
            $is_front = FALSE;
            //date controls
            if (is_array($arrContent)) {

                //Initializing options

                $frontData = $this->_objFrontPage->getFrontPage($arrContent['id']);
                if($frontData === FALSE){
                    $is_front = FALSE;
                }else{
                    $show_content = $frontData['show_content'];
                    $is_front = TRUE;
                }

                $frontPage->setChecked($is_front);
                $published->setChecked($arrContent['published']);
                $visible = $arrContent['published'];

                //Set licence option
                if(isset($arrContent['post_lic'])){
                    $objCCLicence->defaultValue = $arrContent['post_lic'];
                }

                //convert to strings to datetime
                $override = date("Y-m-d H:i:s",strtotime($arrContent['created']));
                $start_pub = '';
                $end_pub = '';

            }else{
                //Initializing options

                $published->setChecked(TRUE);
                $visible = 1;
                $hide_title = '0';
                $hide_user = '1';
                $hide_date = '1';
                $contentId = '';
                $arrContent = null;

                if ( $this->getParam('frontpage') == 'true') {
                    $frontPage->setChecked(TRUE);
                    $is_front = TRUE;
                }

            }

            $tbl_basic->startRow();
            $tbl_basic->addCell($this->objLanguage->languageText('word_publish').': &nbsp; ', '100');
            $tbl_basic->addCell($this->getYesNoRadion('published', $visible, false));
            //$tbl_basic->addCell($published->show());
            $tbl_basic->endRow();

            $tabs->addTab($this->objLanguage->languageText('mod_cmsadmin_basic','cmsadmin'),$tbl_basic->show(),'',False,'');
            $tabs->cssClass = 'addContentTabs';

            return $tabs->show();
        }








       /**
        * Method provides tabboxes for page artifacts
        * such as metadata and basic cms page behaviour artifacts
        * @param array Content The content to be modified
        * @return str tabs
        *
        */
        public function getConfigTabs($arrContent=NULL){

            /**
            * Defining Basic items to be displayed for 
            * First Tab
            */
            //var_dump($arrContent);
            //$tabs =$this->newObject('tabcontent','htmlelements');

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

            $frontPage = new checkbox('frontpage');
            $frontPage->value = 1;
            $frontPage->extra = 'onclick="doToggle(\'toggle_layer_param\'); doToggle(\'introdiv\')"';

            if ($this->_objUserPerm->canAddToFrontPage()) {
                $script = "
                    <script language='javascript'>
                        //jQuery standard toggle effect
                
                        function doToggle(id, chk) {
                            chkBox = document.getElementById('input_frontpage');

                            if (chkBox.checked){
                                jQuery('#' + id).addClass('toggleShow').show('slow');
                            } else {
                                jQuery('#' + id).addClass('toggleShow').hide('slow');
                            }
                        }

                        jQuery(document).ready(function(){
                            chkBox = document.getElementById('input_frontpage');

                            if (!chkBox.checked){
                                jQuery('#toggle_layer_param').addClass('toggleShow').hide();
                                jQuery('#introdiv').addClass('toggleShow').hide();
                            }

                            //Adding click event for checkbox in simple parameters tab
                            /*
                            jQuery('#input_frontpage').click(function() {
                                alert('Hello world!');
                            });
                            */
                        });
                        </script>";

                $this->appendArrayVar('headerParams', $script);
            }

            $show_content = '0';

            //date controls
            if (is_array($arrContent)) {

                //Initializing options

                $introInputValue = stripslashes($arrContent['introtext']);

                $frontData = $this->_objFrontPage->getFrontPage($arrContent['id']);
                if($frontData === FALSE){
                    $is_front = FALSE;
                }else{
                    $show_content = $frontData['show_content'];
                    $is_front = TRUE;
                }

                $frontPage->setChecked($is_front);
                $published->setChecked($arrContent['published']);
                $visible = $arrContent['published'];

                //Set licence option
                if(isset($arrContent['post_lic'])){
                    $objCCLicence->defaultValue = $arrContent['post_lic'];
                }

                //convert to strings to datetime
                $override = date("Y-m-d H:i:s",strtotime($arrContent['created']));
                $start_pub = '';
                $end_pub = '';

                if (!is_null($arrContent['start_publish'])) {
                    $start_pub = date("Y-m-d H:i:s",strtotime($arrContent['start_publish']));

                }elseif (!is_null($arrContent['end_publish'])){
                    $end_pub = date("Y-m-d H:i:s",strtotime($arrContent['end_publish']));

                }

                $dateField = $objdate->show('overide_date', 'yes', 'no', $override);
                $pub = $publishing->show('publish_date', 'yes', 'no',$start_pub);
                $end_pub = $publishing_end->show('end_date', 'yes', 'no', $end_pub);
                //Author Alias
                $author = new textinput('author_alias',$arrContent['created_by_alias'],null,20);
                $lbl_author_alias = new label( $this->objLanguage->languageText('mod_cmsadmin_author_alias','cmsadmin'),'author_alias');
                //Pre-populated dropdown

                /*
                $creator = new dropdown('creator');
                $users = $this->_objUserModel->getUsers('surname', 'listall');

                if(!empty($users)){
                    foreach($users as $item){
                        $creator->addOption($item['userid'], $item['surname'].', '.$item['firstname']);
                    }
                    $creator->setSelected($arrContent['created_by']);
                }
                $lbl_creator = new label($this->objLanguage->languageText('mod_cmsadmin_change_author','cmsadmin'),'creator');
                */

                //Change Created Date
                $lbl_date_created = new label($this->objLanguage->languageText('mod_cmsadmin_override_creation_date','cmsadmin'),'overide_date');
                $lbl_pub = new label($this->objLanguage->languageText('mod_cmsadmin_start_publishing','cmsadmin'),'publish_date');
                $lbl_pub_end = new label($this->objLanguage->languageText('mod_cmsadmin_end_publishing','cmsadmin'),'end_date');

            }else{
                //Initializing options

                $published->setChecked(TRUE);
                $visible = 1;
                $hide_title = '0';
                $hide_user = '1';
                $hide_date = '1';
                $contentId = '';
                $arrContent = null;

                if (!isset($is_front)) {
                    $is_front = false;
                }

                if ( $this->getParam('frontpage') == 'true') {
                    $frontPage->setChecked(TRUE);
                    $is_front = TRUE;
                }

                $frontPage->setChecked($is_front);


                $dateField = $objdate->show('overide_date', 'yes', 'no', '');
                $pub = $publishing->show('publish_date', 'yes', 'no', '');
                $end_pub = $publishing_end->show('end_date', 'yes', 'no', '');
                //Author Alias
                $author = new textinput('author_alias',null,null,20);
                $lbl_author_alias = new label( $this->objLanguage->languageText('mod_cmsadmin_author_alias','cmsadmin'),'author_alias');


                //Pre-populated dropdown
                /*
                $creator = new dropdown('creator');
                $users = $this->_objUserModel->getUsers('surname', 'listall');

                if(!empty($users)){
                    foreach($users as $item){
                        $creator->addOption($item['userid'], $item['surname'].', '.$item['firstname']);
                    }
                    $creator->setSelected($this->_objUser->userId());
                }
                $lbl_creator = new label($this->objLanguage->languageText('mod_cmsadmin_change_author','cmsadmin'),'creator');
                */

                //Change Created Date
                $lbl_date_created = new label($this->objLanguage->languageText('mod_cmsadmin_override_creation_date','cmsadmin'),'overide_date');
                $lbl_pub = new label($this->objLanguage->languageText('mod_cmsadmin_start_publishing','cmsadmin'),'publish_date');
                $lbl_pub_end = new label($this->objLanguage->languageText('mod_cmsadmin_end_publishing','cmsadmin'),'end_date');
            }

            $tbl_basic->startRow();
            $tbl_basic->addCell($this->objLanguage->languageText('word_publish').': &nbsp; ', '200');
            $tbl_basic->addCell($this->getYesNoRadion('published', $visible, false));
            //$tbl_basic->addCell($published->show());
            $tbl_basic->endRow();


            if (is_array($arrContent)) {
			
                $show_title = 'g';
                if (isset($arrContent['show_title'])) {
                    $show_title = $arrContent['show_title'];
                }
    
                $show_author = 'g';
                if (isset($arrContent['show_author'])) {
                    $show_author = $arrContent['show_author'];
                }
    
                $show_date = 'g';
                if (isset($arrContent['show_date'])) {
                    $show_date = $arrContent['show_date'];
                }

                //title option
                $opt_title = new dropdown('show_title');
                $opt_title->addOption('g',"-Global-Option-");
                $opt_title->addOption("y",'yes');
                $opt_title->addOption("n",'no');
                $opt_title->setSelected($show_title);
                $lbl_title = new label($this->objLanguage->languageText('mod_cmsadmin_show_title','cmsadmin'),'show_title');
                //author option
                $opt_author = new dropdown('show_author');
                $opt_author->addOption('g',"-Global-Option-");
                $opt_author->addOption('y','yes');
                $opt_author->addOption('n','no');
                $opt_author->setSelected($show_author);
                $lbl_author = new label($this->objLanguage->languageText('mod_cmsadmin_show_author','cmsadmin'),'show_author');
                //date option
                $opt_date = new dropdown('show_date');
                $opt_date->addOption('g',"-Global-Option-");
                $opt_date->addOption('y','yes');
                $opt_date->addOption('n','no');
                $opt_date->setSelected($show_date);
                $lbl_date = new label($this->objLanguage->languageText('mod_cmsadmin_show_date','cmsadmin'),'show_date');

            }else{
                //title option
                $opt_title = new dropdown('show_title');
                $opt_title->addOption('g',"-Global-Option-");
                $opt_title->addOption("y",'yes');
                $opt_title->addOption("n",'no');
                $opt_title->setSelected('g');
                $lbl_title = new label($this->objLanguage->languageText('mod_cmsadmin_show_title','cmsadmin'),'show_title');
                //author option
                $opt_author = new dropdown('show_author');
                $opt_author->addOption('g',"-Global-Option-");
                $opt_author->addOption('y','yes');
                $opt_author->addOption('n','no');
                $opt_author->setSelected('g');
                $lbl_author = new label($this->objLanguage->languageText('mod_cmsadmin_show_author','cmsadmin'),'show_author');
                //date option
                $opt_date = new dropdown('show_date');
                $opt_date->addOption('g',"-Global-Option-");
                $opt_date->addOption('y','yes');
                $opt_date->addOption('n','no');
                $opt_date->setSelected('g');
                $lbl_date = new label($this->objLanguage->languageText('mod_cmsadmin_show_date','cmsadmin'),'show_date');
            }
            //add items to tables for good layout

            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_title->show(), null, 'top', null, 'cmstinyvspacer');
            $tbl_basic->addCell($opt_title->show());
            $tbl_basic->endRow();

            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_author->show(), null, 'top', null, 'cmstinyvspacer');
            $tbl_basic->addCell($opt_author->show());
            $tbl_basic->endRow();
			
            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_date->show(), null, 'top', null, 'cmstinyvspacer');
            $tbl_basic->addCell($opt_date->show());
            $tbl_basic->endRow();

            //Hide Title
            /*
            $lbNo = $this->objLanguage->languageText('word_no');
            $lbYes = $this->objLanguage->languageText('word_yes');

            $objRadio = new radio('hide_title');
            $objRadio->addOption('1', '&nbsp;'.$lbYes);
            $objRadio->addOption('0', '&nbsp;'.$lbNo);
            $objRadio->setSelected($hide_title);
            $objRadio->setBreakSpace('&nbsp;&nbsp;');

            $tbl_basic->startRow();
            $tbl_basic->addCell($this->objLanguage->languageText('phrase_hidetitle').': &nbsp; ');
            $tbl_basic->addCell($objRadio->show());
            $tbl_basic->endRow();

            //Hide User
            $objRadio = new radio('hide_user');
            $objRadio->addOption('1', '&nbsp;'.$lbYes);
            $objRadio->addOption('0', '&nbsp;'.$lbNo);
            $objRadio->setSelected($hide_user);
            $objRadio->setBreakSpace('&nbsp;&nbsp;');

            $tbl_basic->startRow();
            $tbl_basic->addCell($this->objLanguage->languageText('phrase_hideuser').': &nbsp; ');
            $tbl_basic->addCell($objRadio->show());
            $tbl_basic->endRow();

            //Hide Date
            $objRadio = new radio('hide_date');
            $objRadio->addOption('1', '&nbsp;'.$lbYes);
            $objRadio->addOption('0', '&nbsp;'.$lbNo);
            $objRadio->setSelected($hide_date);
            $objRadio->setBreakSpace('&nbsp;&nbsp;');

            $tbl_basic->startRow();
            $tbl_basic->addCell($this->objLanguage->languageText('phrase_hidedate').': &nbsp; ');
            $tbl_basic->addCell($objRadio->show());
            $tbl_basic->endRow();
            */

            // Radio button to display the full content or only the summary on the front page
            //$lbDisplay = $this->objLanguage->languageText('mod_cmsadmin_displaysummaryorcontent', 'cmsadmin');
            $lbIntroOnly = $this->objLanguage->languageText('mod_cmsadmin_introonly', 'cmsadmin');
            $lbFullContent = $this->objLanguage->languageText('mod_cmsadmin_fullcontent', 'cmsadmin');

            $objRadio = new radio('show_content');
            $objRadio->addOption('0', '&nbsp;'.$lbIntroOnly);
            $objRadio->addOption('1', '&nbsp;'.$lbFullContent);
            $objRadio->setBreakSpace('<br/>');
            $objRadio->setSelected($show_content);

            //Frontpage Security
            if ($this->_objUserPerm->canAddToFrontPage()) {
                $tbl_basic->startRow();
                $tbl_basic->addCell($this->objLanguage->languageText('mod_cmsadmin_showonfrontpage', 'cmsadmin').': ');
                $tbl_basic->addCell($frontPage->show());
                $tbl_basic->endRow();

                //Toggle Intro Only vs Full Content
                $toggleLayer = new layer();
                $toggleLayer->id = 'toggle_layer_param';
                $toggleLayer->str = $this->objLanguage->languageText('mod_cmsadmin_toggleintrocontent', 'cmsadmin') . ":<br/>" . $objRadio->show();            

                $tbl_basic->startRow();
                $tbl_basic->addCell($toggleLayer->show(), '', '', '', '', 'colspan="2"');
                $tbl_basic->endRow();
            }

            $tbl_basic->startRow();
            if (isset($lbl_author_alias)) {
                $tbl_basic->addCell($lbl_author_alias->show());
            } else {
                $tbl_basic->addCell("");
            }
            $tbl_basic->addCell($author->show());
            $tbl_basic->endRow();

        /*
            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_creator->show());
            $tbl_basic->addCell($creator->show());
            $tbl_basic->endRow();
        */





            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_date_created->show());
            $tbl_basic->addCell($dateField);
            $tbl_basic->endRow();
            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_pub->show());
            $tbl_basic->addCell($pub);
            $tbl_basic->endRow();
            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_pub_end->show());
            $tbl_basic->addCell($end_pub);
            $tbl_basic->endRow();



           /**
            * Defining Items to be added to Advanced Tab
            */
            $show_pdf = 'g';
            $show_email = 'g';
            $show_print = 'g';
            if (is_array($arrContent)) {

                if (isset($arrContent['show_pdf'])) {
                    $show_pdf = $arrContent['show_pdf'];
                }

                if (isset($arrContent['show_email'])) {
                    $show_email = $arrContent['show_email'];
                }

                if (isset($arrContent['show_print'])) {
                    $show_print = $arrContent['show_print'];
                }
				
				$show_flag = 'g';
                if (isset($arrContent['show_flag'])) {
                    $show_flag = $arrContent['show_flag'];
                }

				//flag option
                $opt_flag = new dropdown('show_flag');
                $opt_flag->addOption('g',"-Global-Option-");
                $opt_flag->addOption("y",'yes');
                $opt_flag->addOption("n",'no');
                $opt_flag->setSelected($show_flag);
                $lbl_flag = new label($this->objLanguage->languageText('mod_cmsadmin_show_flag','cmsadmin'),'show_flag');

                //pdf option
                $opt_pdf = new dropdown('show_pdf');
                $opt_pdf->addOption('g',"-Global-Option-");
                $opt_pdf->addOption("y",'yes');
                $opt_pdf->addOption("n",'no');
                $opt_pdf->setSelected($show_pdf);
                $lbl_pdf = new label($this->objLanguage->languageText('mod_cmsadmin_show_pdf','cmsadmin'),'show_pdf');
                //email option
                $opt_email = new dropdown('show_email');
                $opt_email->addOption('g',"-Global-Option-");
                $opt_email->addOption('y','yes');
                $opt_email->addOption('n','no');
                $opt_email->setSelected($show_email);
                $lbl_email = new label($this->objLanguage->languageText('mod_cmsadmin_show_email','cmsadmin'),'show_email');
                //Print
                $opt_print = new dropdown('show_print');
                $opt_print->addOption('g',"-Global-Option-");
                $opt_print->addOption('y','yes');
                $opt_print->addOption('n','no');
                $opt_print->setSelected($show_print);
                $lbl_print = new label($this->objLanguage->languageText('mod_cmsadmin_show_print','cmsadmin'),'show_print');

            }else{
				//flag option
                $opt_flag = new dropdown('show_flag');
                $opt_flag->addOption('g',"-Global-Option-");
                $opt_flag->addOption("y",'yes');
                $opt_flag->addOption("n",'no');
                $opt_flag->setSelected('g');
                $lbl_flag = new label($this->objLanguage->languageText('mod_cmsadmin_show_flag','cmsadmin'),'show_flag');
			
                //pdf option
                $opt_pdf = new dropdown('show_pdf');
                $opt_pdf->addOption('g',"-Global-Option-");
                $opt_pdf->addOption("y",'yes');
                $opt_pdf->addOption("n",'no');
                $opt_pdf->setSelected('g');
                $lbl_pdf = new label($this->objLanguage->languageText('mod_cmsadmin_show_pdf','cmsadmin'),'show_pdf');
                //email option
                $opt_email = new dropdown('show_email');
                $opt_email->addOption('g',"-Global-Option-");
                $opt_email->addOption('y','yes');
                $opt_email->addOption('n','no');
                $opt_email->setSelected('g');
                $lbl_email = new label($this->objLanguage->languageText('mod_cmsadmin_show_email','cmsadmin'),'show_email');
                //Print
                $opt_print = new dropdown('show_print');
                $opt_print->addOption('g',"-Global-Option-");
                $opt_print->addOption('y','yes');
                $opt_print->addOption('n','no');
                $opt_print->setSelected('g');
                $lbl_print = new label($this->objLanguage->languageText('mod_cmsadmin_show_print','cmsadmin'),'show_print');
            }
            //add items to tables for good layout

            $tbl_advanced->startRow();
            $tbl_advanced->addCell($lbl_pdf->show(), '120px', 'top', null, 'cmstinyvspacer');
            $tbl_advanced->addCell($opt_pdf->show());
            $tbl_advanced->endRow();
            $tbl_advanced->startRow();
            $tbl_advanced->addCell($lbl_email->show(), '120px', 'top', null, 'cmstinyvspacer');
            $tbl_advanced->addCell($opt_email->show());
            $tbl_advanced->endRow();
            $tbl_advanced->startRow();
            $tbl_advanced->addCell($lbl_print->show(), '120px', 'top', null, 'cmstinyvspacer');
            $tbl_advanced->addCell($opt_print->show());
            $tbl_advanced->endRow();
            $tbl_advanced->startRow();
            $tbl_advanced->addCell($lbl_flag->show(), '120px', 'top', null, 'cmstinyvspacer');
            $tbl_advanced->addCell($opt_flag->show());
            $tbl_advanced->endRow();

           /**
            * Defining Items for Metadata
            */
            if (is_array($arrContent)) {
                $keyword = new textarea('keyword',$arrContent['metakey'],6);
                $lbl_keyword = new label($this->objLanguage->languageText('mod_cmsadmin_keyword','cmsadmin'),'keyword');
                $descr = new textarea('description',$arrContent['metadesc'],6);
                $lbl_descr = new label($this->objLanguage->languageText('mod_cmsadmin_description','cmsadmin'),'description');
            }else{
                $keyword = new textarea('keyword',null,6);
                $lbl_keyword = new label($this->objLanguage->languageText('mod_cmsadmin_keyword','cmsadmin'),'keyword');
                $descr = new textarea('description',null,6);
                $lbl_descr = new label($this->objLanguage->languageText('mod_cmsadmin_description','cmsadmin'),'description');
            }
            $tbl_meta->startRow();
            $tbl_meta->addCell($lbl_keyword->show());
            $tbl_meta->endRow();
            $tbl_meta->startRow();
            $tbl_meta->addCell($keyword->show());
            $tbl_meta->endRow();
            $tbl_meta->startRow();
            $tbl_meta->addCell($lbl_descr->show());
            $tbl_meta->endRow();
            $tbl_meta->startRow();
            $tbl_meta->addCell($descr->show());
            $tbl_meta->endRow();
            $tbl_meta->startRow();
            if (is_array($arrContent)) {
                $tbl_meta->addCell("<input type=\"button\" class=\"button\" value=\"{$this->objLanguage->languageText('mod_cmsadmin_add_section_button','cmsadmin')}\" onclick=\"f=document.getElementById('form_addfrm');f.keyword.value=document.getElementById('form_addfrm').parent.value+', '+f.title.value+f.keyword.value;\" />");
            }else{
                $tbl_meta->addCell("<input type=\"button\" class=\"button\" value=\"{$this->objLanguage->languageText('mod_cmsadmin_add_section_button','cmsadmin')}\" onclick=\"f=document.getElementById('form_addfrm');f.keyword.value=document.getElementById('form_addfrm').parent.options[document.getElementById('form_addfrm').parent.selectedIndex].text+', '+f.title.value+f.keyword.value;\" />");
            }

            //Moved outside of the tabs due to slow load issue
            /*
            if (isset($arrContent['body'])) {
            $bodyInputValue = stripslashes($arrContent['body']);
            }else{
            $bodyInputValue = null;
            }
            $bodyInput = $this->newObject('htmlarea', 'htmlelements');
            $bodyInput->init('body', $bodyInputValue);
            $bodyInput->setContent($bodyInputValue);
            $bodyInput->setDefaultToolBarSet();
            $bodyInput->height = '400px';
            */

            //$bodyInput->width = '50%';
            $tbl_meta->endRow();
            //Add items to tabs

            //Lets do the CC Licence
            $objModulesInfo = $this->getObject('modules', 'modulecatalogue');

            //cc licence input
            if ($objModulesInfo->checkIfRegistered('creativecommons')) {
                $objCCLicence = $this->newObject('licensechooser', 'creativecommons');
                $objCCLicence->setIconSize('small');

                $h3->str = $this->objLanguage->languageText('word_licence');
                $h3->type = 3;
                //if (!$editmode) {

                $tbl_lic->startRow();
                $tbl_lic->addCell('<br/>');
                $tbl_lic->endRow();

                $tbl_lic->startRow();
                $tbl_lic->addCell($h3->show(),null,'center','left');
                $tbl_lic->endRow();

                $tbl_lic->startRow();
                $tbl_lic->addCell('', null, 'top', null, 'cmsvspacer');
                $tbl_lic->endRow();

                $tbl_lic->startRow();
                $tbl_lic->addCell($objCCLicence->show(),null,'top','left', null, 'colspan="2"'); 
                $tbl_lic->endRow();

                $tbl_lic->startRow();
                $tbl_lic->addCell('<input type="hidden" value="0" name="must_preview" id="must_preview"><input type="hidden" value="0" name="must_apply" id="must_apply">', null, 'top', null, 'cmsvspacer');
                $tbl_lic->endRow();

            } 



            //$tabs->addTab($this->objLanguage->languageText('mod_cmsadmin_maintext', 'cmsadmin'),$bodyInput->show(),'',TRUE,'');

            $tabs->addTab($this->objLanguage->languageText('mod_cmsadmin_basic','cmsadmin'),$tbl_basic->show(),'',False,'');
            $tabs->addTab($this->objLanguage->languageText('mod_cmsadmin_advanced','cmsadmin'),$tbl_advanced->show(),'',False,'');
            $tabs->addTab($this->objLanguage->languageText('mod_cmsadmin_meta','cmsadmin'), $tbl_meta->show(),'',False,'');

            //cc licence input
            if ($objModulesInfo->checkIfRegistered('creativecommons')) {
                $tabs->addTab($this->objLanguage->languageText('mod_cmsadmin_lic','cmsadmin'), $tbl_lic->show(),'',False,'');
            }

            //$tabs->width = '70%';
            $tabs->cssClass = 'addContentTabs';

            return $tabs->show();
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
        * Method to get the public vs logged in toggle button
        *
        * @param  $isCheck Booleans value with either TRUE|FALSE
        * @return string icon
        * @access public
        */
        public function getPublicAccessIcon($isCheck, $returnFalse = TRUE)
        {
            $objIcon = $this->newObject('geticon', 'htmlelements');

            if ($isCheck) {
                $objIcon->setIcon('permission_unlock', 'png');
                $objIcon->title = 'Click to Deny Public Access';//$this->objLanguage->languageText('word_published');
            } else {
                if ($returnFalse) {
                    $objIcon->setIcon('permission_lock', 'png');
                    $objIcon->title = 'Click to Allow Public Access';//$this->objLanguage->languageText('phrase_notpublished');
                }
            }

            return $objIcon->show();
        }


       /**
        * Method to generate the navigation
        *
        * @access public
        * @return string The html for the side bar link / navigation
        */
        public function getNav($hideCmsMenu = TRUE)
        {
            $lbSection = $this->objLanguage->languageText('mod_cmsadmin_sectionnotvisible', 'cmsadmin');
            $lbOrange = $this->objLanguage->languageText('mod_cmsadmin_sectionsetnotvisible', 'cmsadmin');
            $lbWhite = $this->objLanguage->languageText('mod_cmsadmin_sectionnocontent', 'cmsadmin');
            $lbGreen = $this->objLanguage->languageText('mod_cmsadmin_sectionparentnotvisible', 'cmsadmin');

            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $objIcon = $this->getObject('geticon', 'htmlelements');

            //Create cms admin link
            $link = $this->objLanguage->languageText('phrase_controlpanel');
            $url = $this->uri('', 'cmsadmin');
            $cmsAdminLink = $objIcon->getTextIcon($url, 'control_panel', $link, 'png', 'icons/cms/');

            // Create RSS link
            $link = $this->objLanguage->languageText('phrase_rssfeeds');
            $url = $this->uri(array('action' => 'createfeed'), 'cmsadmin');
            $createRss = $objIcon->getTextIcon($url, 'rss', $link, 'png', 'icons/cms/');

            //Create menu management
            $link = $this->objLanguage->languageText('mod_cmsadmin_menu','cmsadmin');
            //$url = $this->uri(array('action' => 'managemenus'), 'cmsadmin');
            $url = $this->uri(array('action' => 'menustyle'), 'cmsadmin');
            $menuMangement = $objIcon->getTextIcon($url, 'menu2', $link, 'png', 'icons/cms/');

            //Create filemanager menu
            $link = $this->objLanguage->languageText('phrase_uploadfiles');
            $url = $this->uri(array('action' => ''), 'filemanager');
            $filemanager = $objIcon->getTextIcon($url, 'media', $link, 'png', 'icons/cms/');
            
            $nav  = '<div id="cmsnavcontainer">';
            if (!$hideCmsMenu) { // Is this really needed?
                $currentNode = $this->getParam('sectionid');
                $objAdminCmsTree = $this->getObject('simpletreemenu', 'cmsadmin');
                $cmsAdminMenu = $objAdminCmsTree->getCMSAdminTree($currentNode);
                $nav .="<div id='cmsnavigation'>".$cmsAdminMenu."</div>\n"; 
            }

			$script = "
            <script type='text/javascript'>
                jQuery(function(){
					jQuery('#cmscontrolpanelmenu').menu({
						content: jQuery('#cmscontrolpanelmenu').next().html(),
                        flyOut: true,
						backLink: true,
                        showSpeed: 100
					});
				});
				
                jQuery(document).ready(function(){
                    jQuery('#btnaddcontent').livequery('click',function(){
                        alert(jQuery('#menuSelection').html());
                        //document.href.location = '?module=cmsadmin&amp;action=addcontent';
                    });
                });
			</script>";
			
			$this->appendArrayVar('headerParams', $script);

            $lblQuickMenu = $this->objLanguage->languageText('mod_cmsadmin_quick_heading', 'cmsadmin');
            $lblFrontpage = $this->objLanguage->languageText('mod_cmsadmin_quick_frontpage', 'cmsadmin');
            $lblContent = $this->objLanguage->languageText('mod_cmsadmin_quick_content', 'cmsadmin');
            $lblSections = $this->objLanguage->languageText('mod_cmsadmin_quick_sections', 'cmsadmin');
            $lblTemplates = $this->objLanguage->languageText('mod_cmsadmin_quick_templates', 'cmsadmin');
            $lblFeeds = $this->objLanguage->languageText('mod_cmsadmin_quick_feeds', 'cmsadmin');
            $lblPermissions = $this->objLanguage->languageText('mod_cmsadmin_quick_permissions', 'cmsadmin');
            $lblMenu = $this->objLanguage->languageText('mod_cmsadmin_quick_menu', 'cmsadmin');
            $lblUploadfiles = $this->objLanguage->languageText('mod_cmsadmin_quick_uploadfiles', 'cmsadmin');
            $lblShorturls = $this->objLanguage->languageText('mod_cmsadmin_quick_shorturls', 'cmsadmin');
            $lblFlag = $this->objLanguage->languageText('mod_cmsadmin_quick_flag', 'cmsadmin');
            $lblConfig = $this->objLanguage->languageText('mod_cmsadmin_quick_config', 'cmsadmin');
            $lblTrash = $this->objLanguage->languageText('mod_cmsadmin_quick_trash', 'cmsadmin');

            $objIcon->extra = '';

            //Frontpage Icon
            $objIcon->setIcon('frontpage_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblFrontpage;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['frontpage'] = $objIcon->show().'&nbsp;';

            //Content Icon
            $objIcon->setIcon('add_article_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblContent;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['content'] = $objIcon->show().'&nbsp;';
            $qIcon['content_src'] = $objIcon->getSrc();

            //Sections Icon
            $objIcon->setIcon('section_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblSections;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['section'] = $objIcon->show().'&nbsp;';
            $qIcon['section_src'] = $objIcon->getSrc();

            //Templates Icon
            $objIcon->setIcon('templates_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblTemplates;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['template'] = $objIcon->show().'&nbsp;';

            //Feeds Icon
            $objIcon->setIcon('rss_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblFeeds;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['feed'] = $objIcon->show().'&nbsp;';

            //Permissions Icon
            $objIcon->setIcon('permissions_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblPermissions;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['permission'] = $objIcon->show().'&nbsp;';

            //Menu Icon
            $objIcon->setIcon('menu2_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblMenu;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['menu'] = $objIcon->show().'&nbsp;';

            //Upload Files Icon
            $objIcon->setIcon('media_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblUploadfiles;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['filemanager'] = $objIcon->show().'&nbsp;';

            //Short URLs Icon
            $objIcon->setIcon('shorturl_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblShorturls;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['shorturl'] = $objIcon->show().'&nbsp;';

            //Flag Icon
            $objIcon->setIcon('flag_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblFlag;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['flag'] = $objIcon->show().'&nbsp;';

            //Config Icon
            $objIcon->setIcon('config_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblConfig;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['config'] = $objIcon->show().'&nbsp;';

            //Trash Icon
            $objIcon->setIcon('trash_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblConfig;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['trash'] = $objIcon->show().'&nbsp;';

            //Add Icon
            $objIcon->setIcon('new_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblConfig;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['add'] = $objIcon->show().'&nbsp;';

            //Add Icon
            $objIcon->setIcon('eyes_smaller', 'png', 'icons/cms/');
            $objIcon->title = $lblConfig;
            $objIcon->cssClass = 'control_icon_images';
            $qIcon['view'] = $objIcon->show().'&nbsp;';

			$cmsControlPanel = '
			<a tabindex="0" href="#cmscontrolpanel" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all" id="cmscontrolpanelmenu">'.$lblQuickMenu.'</a>
			<div id="cmscontrolpanelitems" class="hidden">
			<ul>';

            //Frontpage Security
            if ($this->_objUserPerm->canAddToFrontPage()) {
				$cmsControlPanel .= '<li><a href="?module=cmsadmin&amp;action=frontpages">'.$qIcon['frontpage'].'Edit Front Page</a></li>';
            }

            $cmsControlPanel .= 
			'	<li> <a href="#">'.$qIcon['content'].'Content</a>
					<ul>
						<li><a id="btnaddcontent" href="?module=cmsadmin&amp;action=addcontent">'.$qIcon['add'].'Add a Content Item</a></li>
					</ul>
				</li>
				<li><a href="?module=cmsadmin&amp;action=sections">'.$qIcon['section'].'Sections</a>
					<ul>
						<li><a href="?module=cmsadmin&amp;action=sections">'.$qIcon['view'].'View Sections</a></li>
						<li><a href="?module=cmsadmin&amp;action=addsection">'.$qIcon['add'].'Add a Section</a></li>
					</ul>
				</li>
				<li><a href="?module=cmsadmin&amp;action=templates">'.$qIcon['template'].'Templates</a>
				</li>
				<li><a href="?module=cmsadmin&amp;action=createfeed">'.$qIcon['feed'].'RSS Feeds</a>
				</li>
				<li><a href="?module=cmsadmin&amp;action=permissions">'.$qIcon['permission'].'Permissions</a></li>';
			if ($this->_objSysConfig->getValue('admin_only_menu', 'cmsadmin') == 'TRUE') {
                if ($this->_objUser->inAdminGroup($this->_objUser->userId())) {
                    $cmsControlPanel .= '<li><a href="?module=cmsadmin&amp;action=menustyle">'.$qIcon['menu'].'Menu</a></li>';
                }
            } else {
                $cmsControlPanel .= '<li><a href="?module=cmsadmin&amp;action=menustyle">'.$qIcon['menu'].'Menu</a></li>';
            }
			$cmsControlPanel .= '<li><a href="?module=cmsadmin&amp;action=filemanager">'.$qIcon['filemanager'].'Upload Files</a></li>
				<li><a href="?module=shorturl&ref=cmsadmin">'.$qIcon['shorturl'].'Short URLs</a></li>
                <li><a href="?module=cmsadmin&amp;action=flag">'.$qIcon['flag'].'Flag</a></li>
				<li><a href="?module=sysconfig&action=step2&pmodule_id=cmsadmin">'.$qIcon['config'].'Configuration</a></li>
				
			</ul>
			</div>
			';

            $table = new htmltable();
			$table->width = '100%';
			
            $table->startRow();
            $table->addCell($cmsControlPanel);
            $table->endRow();
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->endRow();
            $table->startRow();
            $table->addCell($cmsAdminLink);
            $table->endRow();
            $table->startRow();
            $table->addCell($createRss);
            $table->endRow();
            if ($this->_objSysConfig->getValue('admin_only_menu', 'cmsadmin') == 'TRUE') {
                if ($this->_objUser->inAdminGroup($this->_objUser->userId())) {
                    $table->startRow();
                    $table->addCell($menuMangement);
                    $table->endRow();
                }
            } else {
                $table->startRow();
                $table->addCell($menuMangement);
                $table->endRow();
            }
            $table->startRow();
            $table->addCell($filemanager);
            $table->endRow();
            
            $nav .= $objFeatureBox->showContent(NULL, $table->show())."</div>";

            return $nav;
        }

                /**
                 * Method to show the content info for the group list popup
                 *
                 * @access public
                 * @return string The page content to be displayed
                 */
        public function showGroupContent($groupId, $groupFieldName = 'input_publisherid')
        {
            $this->loadClass('textinput','htmlelements');
            $this->loadClass('hiddeninput','htmlelements');
            $objGroups = & $this->newObject('dbgroups', 'cmsadmin');
            $group = $objGroups->getNode($groupId);

            //initiate objects
            $objForm = new form('editfrm', $this->uri(array()));
            $objForm->setDisplayType(3);

            $selectedGroupInput = new textinput('selectedgroup');

            if (count($group) > 0) {
                $selectedGroupInput->value = $groupId;
                $table = & $this->newObject('htmltable', 'htmlelements');
                $table->startRow();
                $table->addCell('Name');
                $table->addCell($group[0]['name']);
                $table->endRow();
                $table->startRow();
                $table->addCell('Description');
                $table->addCell($group[0]['description']);
                $table->endRow();

                $objForm->addToForm($table->show());
            }
            $objForm->addToForm($selectedGroupInput->show());

            $strHTML = $objForm->show();
            $strHTML .= '<br /><button onclick="javascript:opener.document.getElementById(\''.$groupFieldName.'\').value=document.getElementById(\'input_selectedgroup\').value;window.close()">Link</button>';

            return $strHTML;
        }


                /**
                 * Method to get a list of groups with write access to this section
                 *
                 * The sections tables groupid field will store a comma separated list of id's
                 *
                 * @param string $sectionid
                 * @return array of groups with write access to this section
                 * @access public
                 */

        public function getSectionGroupNames($sectionid){
            //TODO: groupid?? suppose to be sectionid??
            //Removing notices for now.
            if (!isset($groupid)) {
                $groupid = '';
            }

            $objGroups = & $this->newObject('dbgroups', 'cmsadmin');
            $group = $objGroups->getNode($groupid);
            $names = array();
            foreach ($group as $grp){
                array_push($names, $grp['name']);
            }

            $hasValue = false;
            foreach ($names as $name){
                if ($name != '' && $name != null){
                    $hasValue = true;
                }
            }

            if ($hasValue){
                return $names;
            } else {
                return array('');
            }
        } 

                /**
                 * Method to get a list of users with access to this section
                 *
                 * The sections tables groupid field will store a comma separated list of id's
                 *
                 * @param string $sectionid
                 * @return array of groups with write access to this section
                 * @access public
                 */

        public function getSectionUserNames($sectionid){

            //TODO: groupid?? suppose to be sectionid??
            //Removing notices for now.
            if (!isset($groupid)) {
                $groupid = '';
            }

            $objGroups = & $this->newObject('dbgroups', 'cmsadmin');
            $group = $objGroups->getNode($groupid);
            $names = array();
            foreach ($group as $grp){
                array_push($names, $grp['name']);
            }

            $hasValue = false;
            foreach ($names as $name){
                if ($name != '' && $name != null){
                    $hasValue = true;
                }
            }

            if ($hasValue){
                return $names;
            } else {
                return array('');
            }
        } 


                /**
                 * Method to get a list of groups with write access to this CONTENT
                 *
                 * The sections tables groupid field will store a comma separated list of id's
                 *
                 * @param string $contentid
                 * @return array of groups with write access to this section
                 * @access public
                 */

        public function getContentGroupNames($contentid){
            //TODO: groupid?? suppose to be sectionid??
            //Removing notices for now.
            if (!isset($groupid)) {
                $groupid = '';
            }

            $objGroups = & $this->newObject('dbgroups', 'cmsadmin');
            $group = $objGroups->getNode($groupid);
            $names = array();
            foreach ($group as $grp){
                array_push($names, $grp['name']);
            }

            $hasValue = false;
            foreach ($names as $name){
                if ($name != '' && $name != null){
                    $hasValue = true;
                }
            }

            if ($hasValue){
                return $names;
            } else {
                return array('');
            }
        } 

                /**
                 * Method to get a list of users with access to this CONTENT
                 *
                 * The sections tables groupid field will store a comma separated list of id's
                 *
                 * @param string $contentid
                 * @return array of groups with write access to this section
                 * @access public
                 */

        public function getContentUserNames($contentid){
            //TODO: groupid?? suppose to be sectionid??
            //Removing notices for now.
            if (!isset($groupid)) {
                $groupid = '';
            }

            $objGroups = & $this->newObject('dbgroups', 'cmsadmin');
            $group = $objGroups->getNode($groupid);
            $names = array();
            foreach ($group as $grp){
                array_push($names, $grp['name']);
            }

            $hasValue = false;
            foreach ($names as $name){
                if ($name != '' && $name != null){
                    $hasValue = true;
                }
            }

            if ($hasValue){
                return $names;
            } else {
                return array('');
            }
        } 



                /**
                 * Method to check if a user is in a group
                 *
                 * @param string $userId User ID of logd in user
                 * @param string $group Group to check for
                 * @return TRUE|FALSE TRUE if user is in group
                 * @access public
                 */
        public function inGroup($group)
        {
            $userId = $this->_objUser->userId();
            $objGroupModel = $this->getObject('groupadminmodel','groupadmin');
            $id = $this->_objUser->PKid($userId);
            $groupId = $objGroupModel->getId($group);
            return $objGroupModel->isGroupMember($id, $groupId);
        }

                /**
                 * Method to check if a user is in a group using group Id
                 *
                 * @param string $userId User ID of logd in user
                 * @param string $group Group to check for
                 * @return TRUE|FALSE TRUE if user is in group
                 * @access public
                 */
        public function inGroupById($groupId)
        {
            $userId = $this->_objUser->userId();
            $objGroupModel = $this->getObject('groupadminmodel','groupadmin');
            $id = $this->_objUser->PKid($userId);
            return $objGroupModel->isGroupMember($id, $groupId);
        }



                /**
                 * Method to check if a user is in a group using group Id
                 *
                 * @param string $userId User ID of logd in user
                 * @param string $group Group to check for
                 * @return TRUE|FALSE TRUE if user is in group
                 * @access public
                 */
        public function userGroups()
        {
            $userId = $this->_objUser->userId();
            $objGroupModel = $this->getObject('groupadminmodel','groupadmin');
            $id = $this->_objUser->PKid($userId);
            return $objGroupModel->getUserGroups($id);
        }


                /**
                 * Method to show  the edit page screen in Menu management
                 *
                 * @access public
                 * @return string The page content to be displayed
                 */
        public function showEditNode($menuNodeId)
        {
            $this->loadClass('textinput','htmlelements');
            $this->loadClass('hiddeninput','htmlelements');
            $this->loadClass('dropdown','htmlelements');
            $this->loadClass('checkbox','htmlelements');
            $this->loadClass('windowPop','htmlelements');
            $objHtmlCleaner = $this->newObject('htmlcleaner', 'utilities');
            $this->objTreeMenu = $this->newObject('buildtree', 'cmsadmin');
            $menuNode = $this->objTreeMenu->getNode($menuNodeId);
            //initiate objects
            $table =  $this->newObject('htmltable', 'htmlelements');
            $objForm = new form('editfrm', $this->uri(array('action' => 'savemenu', 'id' => $menuNodeId)));
            $objForm->setDisplayType(3);

            $nodeTypeInput = new dropdown ('nodetype');
            if ($menuNode[0]['parent_id'] == '0') {
                $nodeTypeInput->addOption('0','Menu Root'.'&nbsp;&nbsp;');
                $nodeTypeInput->selected = '0';
                $linkReferenceInput = new hiddeninput ('linkreference');
                $bannerInput = new hiddeninput ('banner');
                $cssInput = new hiddeninput ('css');
                $layoutInput = new hiddeninput ('layout');
            } else {
                $nodeTypeInput->addOption('1','CMS Content'.'&nbsp;&nbsp;');
                $nodeTypeInput->addOption('2','External Link'.'&nbsp;&nbsp;');
                $nodeTypeInput->addOption('3','News Item'.'&nbsp;&nbsp;');
                $nodeTypeInput->selected = $menuNode[0]['node_type'];
                $linkReferenceInput = new textinput ('linkreference');
                $bannerInput = new textinput ('banner');
                $cssInput = new textinput ('css');
                $layoutInput = new textinput ('layout');
            }
            $titleInput = new textinput ('title', null, null, 30);
            $publishedInput = new checkbox ('published', 'Published');

            $publisherIdInput = new textinput('publisherid', $menuNode[0]['publisher_id']);
            $parentIdInput = new hiddeninput('parentid', $menuNode[0]['parent_id']);
            $orderingInput = new hiddeninput ('ordering', $menuNode[0]['ordering']);

            // Submit Button
            $button = new button('submitform', $this->objLanguage->languageText('word_save'));
            $button->setToSubmit();

            $titleInput->value = html_entity_decode($menuNode[0]['title']);
            $linkReferenceInput->value = html_entity_decode($menuNode[0]['link_reference']);
            $bannerInput->value = $menuNode[0]['banner'];
            $cssInput->value = $menuNode[0]['css'];
            $layoutInput->value = $menuNode[0]['layout'];
            if ($menuNode[0]['published'] == 1) {
                $publishedInput->setChecked(TRUE);
            } else {
                $publishedInput->setChecked(FALSE);
            }

            $table->startRow();
            $table->addCell('Node Id');
            $table->addCell($menuNode[0]['id']);
            $table->endRow();
            $table->startRow();
            $table->addCell('Node Type');
            $table->addCell($nodeTypeInput->show());
            $table->endRow();
            $table->startRow();
            $table->addCell('Title');
            $table->addCell($titleInput->show());
            $table->endRow();

            if ($menuNode[0]['parent_id'] == '0') {
                $objForm->addToForm($linkReferenceInput->show());
                $objForm->addToForm($bannerInput->show());
                $objForm->addToForm($layoutInput->show());
                $objForm->addToForm($cssInput->show());
            } else {
                $table->startRow();
                $table->addCell('Link Reference');
                $table->addCell($linkReferenceInput->show());
                if ($menuNode[0]['node_type'] == 1) {
                    $objPop= new windowPop;
                    $objPop->set('location',$this->uri(array('action' => 'showcmspages', 'id' => $menuNode[0]['link_reference'], 'pageid' => $menuNodeId), 'cmsadmin'));
                    $objPop->set('linktext','CMS content');
                    $objPop->set('width','600');
                    $objPop->set('height','600');
                    $objPop->set('left','300');
                    $objPop->set('top','400');
                    $objPop->putJs(); // you only need to do this once per page

                    $table->addCell($objPop->show());
                }
                $table->endRow();
                $table->startRow();
                $table->addCell('Banner');
                $table->addCell($bannerInput->show());
                $table->endRow();
                $table->startRow();
                $table->addCell('Layout');
                $table->addCell($layoutInput->show());
                $table->endRow();
                $table->startRow();
                $table->addCell('CSS');
                $table->addCell($cssInput->show());
                $table->endRow();
            }
            $table->startRow();
            $table->addCell('Published');
            $table->addCell($publishedInput->show());
            $table->endRow();
            $table->startRow();
            $table->addCell('Group Id');
            $table->addCell($publisherIdInput->show());

            $objPop= new windowPop;
            $objPop->set('location',$this->uri(array('action' => 'showgrouplist', 'groupid' => $menuNode[0]['publisher_id']), 'cmsadmin'));
            $objPop->set('linktext','Group list');
            $objPop->set('width','600');
            $objPop->set('height','600');
            $objPop->set('left','300');
            $objPop->set('top','400');
            $objPop->putJs(); // you only need to do this once per page

            $table->addCell($objPop->show());

            $table->endRow();
            $table->startRow();
            $table->addCell($button->show());
            $table->endRow();

            $objForm->addToForm($table->show());

            $objForm->addToForm($parentIdInput->show());
            $objForm->addToForm($orderingInput->show());
            $objH = null;
            $objH = $this->newObject('htmlheading', 'htmlelements');
            $objH->type = '3';
            $objH->str = 'Editing: '. $menuNode[0]['title'];
            $strHTML = $objH->show();

            $strHTML.= $objForm->show();


            return $strHTML;
        }

                /**
                 * Method to show  the add node screen when managing menus
                 *
                 * @access public
                 * @return string The page content to be displayed
                 */
        public function showAddNode($parentId)
        {
            $this->loadClass('textinput','htmlelements');
            $this->loadClass('hiddeninput','htmlelements');
            $this->loadClass('dropdown','htmlelements');
            $this->loadClass('checkbox','htmlelements');
            $this->loadClass('windowPop','htmlelements');
            $this->objTreeMenu =  $this->newObject('buildtree', 'cmsadmin');
            $menuNode = $this->objTreeMenu->getNode($parentId);
            if (count($menuNode) > 0) {
                $parentTitle = $menuNode[0]['title'];
            } else {
                $parentTitle = 'Root';
            }
            //initiate objects
            $table = $this->newObject('htmltable', 'htmlelements');
            $objForm = new form('editfrm', $this->uri(array('action' => 'addmenu')));
            $objForm->setDisplayType(3);


            $nodeTypeInput = new dropdown ('nodetype');

            if ($parentId == '0') {
                $nodeTypeInput->addOption('0','Menu Root'.'&nbsp;&nbsp;');
                $nodeTypeInput->selected = '0';
                $linkReferenceInput = new hiddeninput ('linkreference', '');
                $bannerInput = new hiddeninput ('banner', '');
                $cssInput = new hiddeninput ('css', '');
                $layoutInput = new hiddeninput ('layout', '');
            } else {
                $nodeTypeInput->addOption('1','CMS Content'.'&nbsp;&nbsp;');
                $nodeTypeInput->addOption('2','External Link'.'&nbsp;&nbsp;');
                $nodeTypeInput->addOption('3','News Item'.'&nbsp;&nbsp;');
                $nodeTypeInput->selected = '1';
                $linkReferenceInput = new textinput ('linkreference');
                $bannerInput = new textinput ('banner');
                $cssInput = new textinput ('css');
                $layoutInput = new textinput ('layout');
            }

            $titleInput = new textinput ('title');
            $publishedInput = new checkbox ('published', 'Published', TRUE);

            $publisherIdInput = new textinput('publisherid');

            $parentIdInput = new hiddeninput('parentid', $parentId);
            // Submit Button
            $button = new button('submitform', $this->objLanguage->languageText('word_save'));
            $button->setToSubmit();


            $table->startRow();
            $table->addCell('Node Type');
            $table->addCell($nodeTypeInput->show());
            $table->endRow();
            $table->startRow();
            $table->addCell('Title');
            $table->addCell($titleInput->show());
            $table->endRow();
            if ($parentId == '0') {
                $objForm->addToForm($linkReferenceInput->show());
                $objForm->addToForm($bannerInput->show());
                $objForm->addToForm($layoutInput->show());
                $objForm->addToForm($cssInput->show());
            } else {
                $table->startRow();
                $table->addCell('Link Reference');
                $table->addCell($linkReferenceInput->show());
                $table->endRow();
                $table->startRow();
                $table->addCell('Banner');
                $table->addCell($bannerInput->show());
                $table->endRow();
                $table->startRow();
                $table->addCell('Layout');
                $table->addCell($layoutInput->show());
                $table->endRow();
                $table->startRow();
                $table->addCell('CSS');
                $table->addCell($cssInput->show());
                $table->endRow();
            }
            $table->startRow();
            $table->addCell('Published');
            $table->addCell($publishedInput->show());
            $table->endRow();
            $table->startRow();
            $table->addCell('Group Id');
            $table->addCell($publisherIdInput->show());

            $objPop= new windowPop;
            $objPop->set('location',$this->uri(array('action' => 'showgrouplist'), 'cmsadmin'));
            $objPop->set('linktext','Group list');
            $objPop->set('width','600');
            $objPop->set('height','600');
            $objPop->set('left','300');
            $objPop->set('top','400');
            $objPop->putJs(); // you only need to do this once per page

            $table->addCell($objPop->show());
            $table->endRow();
            $table->startRow();
            $table->addCell($button->show());
            $table->endRow();

            $objForm->addToForm($table->show());

            $objForm->addToForm($parentIdInput->show());
            $objH = null;	
            $objH = $this->newObject('htmlheading', 'htmlelements');
            $objH->type = '3';
            $objH->str = 'Add node to: '. $parentTitle;
            $strHTML = $objH->show().'<p/>';

            $strHTML .= $objForm->show();


            return $strHTML;
        }

                /**
                 * Method to generate the bread crumbs
                 *
                 * @param void
                 * @return string Html for breadcrumbs
                 * @access public
                 */
        public function getBreadCrumbs($module = 'cms')
        {
            $str = '';
            $objTools =$this->newObject('tools', 'toolbar');
            if ($this->getParam('action') == '') {
                return '';
            }

            $home =$this->newObject('link', 'htmlelements');
            $link =$this->newObject('link', 'htmlelements');

            if (!is_null($this->getParam('sectionid', NULL))) {

                $section = $this->_objSections->getSection($this->getParam('sectionid'));
                $link->href = $this->uri(array('action' => 'showsection', 'id' => $this->getParam('sectionid'), 'sectionid' => $this->getParam('sectionid')) , $module);
                $link->link = $this->_objSections->getMenuText($this->getParam('sectionid'));
                $str = $link->show() .' / ';

                while (($section['parentid'] != '0') && (!is_null($section['parentid']))) {
                    $section = $this->_objSections->getSection($section['parentid']);
                    if (is_null($section['parentid'])) {
                        break;
                    }
                    $link->href = $this->uri(array('action' => 'showsection', 'id' => $section['id'], 'sectionid' => $section['id']) , $module);
                    $link->link = $this->_objSections->getMenuText($section['id']);
                    $str = $link->show() .' / ' .$str;

                }
            }
            if (!is_null($this->getParam('id', NULL))) {
                $page = $this->_objContent->getContentPage($this->getParam('id'));
                $str .= $page['title'];
            }
            $home->href = $this->uri(null , $module);
            $home->link = $this->objLanguage->languageText('word_home');
            $str = $home->show() .' / ' . $str;

            $objTools->replaceBreadCrumbs(split(' / ', $str));
        }


        /**
         * Method to generate the dropdown with tree indentations for selecting parent category
         *
         * @param string $setSelected The dropdown option to select
         * @param bool $noRoot True Root Level option will not be displayed
         * @return string Generated HTML for the dropdown
         * @access public
         * @author Warren Windvogel
         */
        public function getTreeDropdown($setSelected = NULL, $noRoot = TRUE)
        {
            $objCMSTree = $this->getObject('cmstree');
            $tree = $objCMSTree->getCMSAdminDropdownTree($setSelected, $noRoot);
            return $tree;

        }

        /**
         * Method to generate the dropdown with tree indentations for selecting parent category
         *
         * @param string $setSelected The dropdown option to select
         * @param bool $noRoot True Root Level option will not be displayed
         * @return string Generated HTML for the dropdown
         * @access public
         * @author Warren Windvogel
         */
        public function getSectionTreeDropdown($setSelected = NULL, $noRoot = TRUE)
        {
            $objCMSTree = $this->getObject('cmstree');
            $tree = $objCMSTree->getCMSAdminSectionDropdownTree($setSelected, $noRoot);

            return $tree;

        }

        /**
         * Method to generate the dropdown with tree indentations for selecting parent category
         *
         * @param string $setSelected The dropdown option to select
         * @param bool $noRoot True Root Level option will not be displayed
         * @return string Generated HTML for the dropdown
         * @access public
         * @author Warren Windvogel
         */
        public function getSectionFlatDropdown($setSelected = NULL, $noRoot = TRUE)
        {
            $objCMSTree = $this->getObject('cmstree');
            $sections = $objCMSTree->getFlatTree($setSelected, $noRoot);

            //var_dump($sections);

            $dropdown = new dropdown('parent');
            $dropdown->addOption(0, '[Root]');

            foreach ($sections as $section){
                $dropdown->addOption($section['id'], $section['title']);
            }

            $dropdown->setSelected($setSelected);
            return $dropdown;
        }

        /**
         * Method to generate the dropdown with tree indentations for selecting parent category
         *
         * @param string $setSelected The dropdown option to select
         * @param bool $showRoot True Root Level option will not be displayed
         * @return string Generated HTML for the dropdown
         * @access public
         * @author Charl Mert, Warren Windvogel
         */
        public function getSectionList($setSelected = NULL, $showRoot = TRUE)
        {

            $arrSections = $this->getSectionLinks(TRUE,$this->contextCode);

            $dropdown = new dropdown('parent');
            
            if ($showRoot){
                $dropdown->addOption(0, '[Root]');
            }

            if (!empty($arrSections)) {
                foreach($arrSections as $section) {
                    $pref = "";
                    $matches = split('<', $section['title']);
                    $img = split('>', $matches[1]);
                    $image = '<'.$img[0].'>';
                    $linkText = $img[1];
                    $noSpaces = strlen($matches[0]);
                    for ($i = 1; $i < $noSpaces; $i++) {
                        $pref .= '&nbsp;&nbsp;';
                    }
                    
                    $section = $this->_objSections->getSection($section['id']);
                    //View section link
                    $sectionItem = $pref.$linkText;
                $dropdown->addOption($section['id'], $sectionItem);
                }
            }

            $dropdown->setSelected($setSelected);
            $dropdown->setId('input_parent_section');
            return $dropdown;
        }


        /**
         * Method to generate the dropdown with tree indentations for selecting parent category
         *
         * @param string $setSelected The dropdown option to select
         * @param bool $noRoot True Root Level option will not be displayed
         * @return string Generated HTML for the dropdown
         * @access public
         * @author Warren Windvogel
         */
        public function getContentTreeDropdown($setSelected = NULL, $noRoot = TRUE)
        {
            $objCMSTree = $this->getObject('cmstree');
            $sections = $objCMSTree->getFlatTree($setSelected, $noRoot);

            //var_dump($sections);

            $dropdown = new dropdown('parent');

            foreach ($sections as $section){
                $dropdown->addOption($section['id'], $section['title']);
            }

            $dropdown->setSelected($setSelected);
            return $dropdown;
        }

      /**
        * Method to return the number of node levels attached to a root section
        *
        * @param string $rootId The id(pk) of the section root
        * @return int $numLevels The number of node levels in the root section
        * @access public
        * @author Warren Windvogel
        */
        public function getNumNodeLevels($rootId)
        {
            //get all sub secs in section
            $subSecs = $this->_objSections->getSubSectionsInRoot($rootId);
            //se number of levels
            $numLevels = '0';

            if (!empty($subSecs)) {
                foreach($subSecs as $sec) {
                    if ($sec['nodelevel'] > $numLevels) {
                        $numLevels = $sec['nodelevel'];
                    }
                }
            }

            return $numLevels;
        }

                /**
                 * Method to insert data into an array at a specific entry pushing entries below
                 * this down
                 *
                 * @param array $dataArray The array to add the data to
                 * @param int $entryNumber The place to add the data
                 * @param mixed $newEntry The new data to be added
                 * @return array $newArray The array with the new entry
                 * @access public
                 * @author Warren Windvogel
                 */
        public function addToTreeArray($dataArray, $entryNumber, $newEntry)
        {
            //create new array
            $newArray = array();
            $counter = '0';
            //loop thru array adding entries before $entryNumber entry as usual
            foreach($dataArray as $ar) {
                if ($counter < $entryNumber) {
                    $newArray[$counter] = $ar;
                } else if ($counter == $entryNumber) {
                    $newArray[$counter] = $ar;
                    $num = $counter + '1';
                    $newArray[$num] = $newEntry;
                } else {
                    $num = $counter + '1';
                    $newArray[$num] = $ar;
                }

                $counter++;
            }

            return $newArray;
        }

       /**
        * Method to generate the indented section links for the side menu
        *
        * @param bool $forTable If false returns links for side menu if true returns array of text with indentation
        * @return string Generated section links or array $treeArray section names indented and ids
        * @param string contextcode The context the user is in: defaults to null
        * @access public
        * @author Warren Windvogel
        */
        public function getSectionLinks($forTable = FALSE,$contextcode=null)
        {
            //Create instance of geticon object
            $objIcon =$this->newObject('geticon', 'htmlelements');
            //Get all root sections
            $availsections = $this->_objSections->getRootNodes(FALSE,$contextcode);
            if (!empty($availsections)) {
                //initiate sequential tree structured array to be inserted into dropdown
                $treeArray = array();
                //add nodes for each section
                foreach($availsections as $section) {
                    //Get icon for root nodes
                    $objIcon->setIcon('tree/treebase');
                    //initiate prefix for nodes
                    $prefix = '';
                    //add root(secion) to dropdown
                    $treeArray[] = array('title' => $objIcon->show().$section['menutext'], 'id' => $section['id']);
                    //get number of node levels
                    $numLevels = $this->getNumNodeLevels($section['id']);
                    //check if section has sub sections

                    if ($numLevels > '0') {
                        //loop through each level and add all sub sections in level

                        for ($i = '2'; $i <= $numLevels; $i++) {
                            $prefix .= '- ';
                            //get all sub secs in section on level
                            $subSecs = $this->_objSections->getSubSectionsForLevel($section['id'], $i, 'DESC');
                            foreach($subSecs as $sec) {
                                //Get icon for parent child nodes
                                $objIcon->setIcon('tree/folder');
                                //if its the 1st node just add it under the section

                                if ($i == '2') {
                                    $treeArray[] = array('title' => $prefix.$objIcon->show().$sec['menutext'], 'id' => $sec['id']);
                                    //else find the parent node and include it after this node
                                } else {
                                    $parentId = $sec['parentid'];
                                    $subSecTitle = $this->_objSections->getMenuText($parentId);
                                    $count = $this->_objSections->getLevel($parentId);
                                    $searchPrefix = "";

                                    for ($num = '2'; $num <= $count; $num++) {
                                        $searchPrefix .= '- ';
                                    }

                                    $needle = array('title' => $searchPrefix.$objIcon->show().$subSecTitle, 'id' => $parentId);
                                    $entNum = array_search($needle, $treeArray);
                                    $newEnt = array('title' => $prefix.$objIcon->show().$sec['menutext'], 'id' => $sec['id']);
                                    $treeArray = $this->addToTreeArray($treeArray, $entNum, $newEnt);
                                }
                            }
                        }
                    }
                }
                if($forTable) {
                    return $treeArray;
                } else {
                    $links = "";
                    $objLink =$this->newObject('link', 'htmlelements');
                    //Add array to dropdown
                    foreach($treeArray as $node) {
                        $matches = split('<', $node['title']);
                        $img = split('>', $matches[1]);
                        $image = '<'.$img[0].'>';
                        $linkText = $img[1];
                        $noSpaces = strlen($matches[0]);
                        //Add space for indentation of node levels
                        for ($i = 1; $i < $noSpaces; $i++) {
                            $links .= '&nbsp;&nbsp;';
                        }
                        //Add folder image
                        $links .= $image;
                        //Create link to section
                        $objLink->link($this->uri(array('action' => 'viewsection', 'id' => $node['id'])));
                        $objLink->link = $linkText;
                        //Add link to section
                        $links .= $objLink->show();
                        $links .= '<br/>';
                    }

                    return $links;
                }
            }
        }


       /**
        * Method to check if a section will be displayed on the tree menu
        *
        * @param string $sectionId The id of the section
        * @return bool $isVisible True if it will be displayed, else False
        * @access public
        * @author Warren Windvogel
        */
        public function sectionIsVisibleOnMenu($sectionId)
        {
            //Set $isVisible to true
            $isVisible = true;
            //Get count value of section to use in for loop
            $sectionLevel = $this->_objSections->getLevel($sectionId);

            for ($i = 1; $i <= $sectionLevel; $i++) {
                //If section has no content set $isVisible to false

                if ($this->_objContent->getNumberOfPagesInSection($sectionId) == 0) {
                    $isVisible = false;
                } else {
                    $section = $this->_objSections->getSection($sectionId);
                    if ($section['published'] == 0) {
                        $isVisible = false;
                    }
                    $sectionId = $section['parentid'];
                }
            }

            return $isVisible;
        }

        /**
         * Method to return the add edit section form
         *
         * @param string $sectionId The id of the section to be edited. Default NULL for adding new section
         * @param string $parentid The id of the section it is found in. Default NULL for adding root node
         * @return string $middleColumnContent The form used to create and edit a section
         * @access public
         * @author Warren Windvogel, Charl Mert
         */
        public function getAddEditSectionForm($sectionId = NULL, $parentid = NULL)
        {

            //initiate objects
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');

            $table =$this->newObject('htmltable', 'htmlelements');
            $objIcon =  $this->newObject('geticon', 'htmlelements');
            $tbl = $this->newObject('htmltable', 'htmlelements');
            $h3 =$this->newObject('htmlheading', 'htmlelements');
            $button =$this->newObject('button', 'htmlelements');
            $objLayer =$this->newObject('layer', 'htmlelements');
            $this->loadClass('image','htmlelements');
            $objFiles =$this->getObject('dbfile','filemanager');
            $objconfig = $this->getObject('altconfig','config');


            $tableContainer = new htmlTable();
            $tableContainer->width = "100%";
            $tableContainer->cellspacing = "0";
            $tableContainer->cellpadding = "0";
            $tableContainer->border = "0";
            $tableContainer->attributes = "align ='center'";

            $table2 = new htmltable();
            $table2->width = "100%";

            if ($sectionId == NULL) {
                $action = 'createsection';
                $editmode = FALSE;
                $sectionId = '';
            } else {
                $action = 'editsection';
                $sectionId = $sectionId;
                $editmode = TRUE;
                $section = $this->_objSections->getSection($sectionId);
            }

            if ($parentid == ''){
                $parentid = $this->getParam('parentid');
            }

            $objForm =& $this->newObject('form', 'htmlelements');
            //setup form
            $objForm->name = 'addsection';
            $objForm->id = 'addsection';

            if (isset($parentid) && !empty($parentid)) {
                $objForm->setAction($this->uri(array('action' => $action, 'id' => $sectionId, 'parentid' => $parentid), 'cmsadmin'));
            } else {
                $objForm->setAction($this->uri(array('action' => $action, 'id' => $sectionId), 'cmsadmin'));
            }


            $objForm->setDisplayType(3);
            $table->cellpadding = '5';  
            $table->cellspacing = '2';  
            //the title
            $titleInput = new textinput('title',null,null,30);
            $objForm->addRule('title', $this->objLanguage->languageText('mod_cmsadmin_pleaseaddtitle', 'cmsadmin'), 'required');
            $button = new button('save', $this->objLanguage->languageText('word_save'));
            $button->id = 'save';
            $button->setToSubmit();
            if ($editmode) {
                $titleInput->value = $section['title'];
                $layout = $section['layout'];
                $isPublished = $section['published'];
                $showTitle = isset($section['show_title']) ? $section['show_title'] : '0';
                //Set rootid as hidden field
                $objRootId = new textinput();
                $objRootId->name = 'rootid';
                $objRootId->id = 'rootid';
                $objRootId->fldType = 'hidden';
                $objRootId->value = $section['rootid'];
                //Set parentid as hidden field
                $objParentId = new textinput();
                $objParentId->name = 'parent';
                $objParentId->id = 'parent';
                $objParentId->fldType = 'hidden';
                $objParentId->value = $section['parentid'];
                //Set parentid as hidden field
                $objCount = new textinput();
                $objCount->name = 'count';
                $objCount->fldType = 'hidden';
                $objCount->value = $section['nodelevel'];
                //Set parentid as hidden field
                $objOrdering = new textinput();
                $objOrdering->name = 'ordering';
                $objOrdering->fldType = 'hidden';
                $objOrdering->value = $section['ordering'];
            } else {
                $titleInput->value = '';
                $bodyInput->value = '';
                $layout = 0;
                $isPublished = '1';
                $showTitle = '0';
            }

            //Add form elements to the table

            $h3->str = $this->objLanguage->languageText('mod_cmsadmin_section_maintext', 'cmsadmin');
            $h3->type = 3;

            $table->startRow();
            $table->addCell($h3->show(), null, 'top', null, null, 'colspan="2"');
            $table->endRow();

            $table->startRow();
            $table->addCell('', '100px', 'top', null, 'cmsvspacer');
            $table->endRow();

            if (!$editmode) {
                $table->startRow();
                $table->addCell($this->objLanguage->languageText('mod_cmsadmin_parentfolder', 'cmsadmin'));

                if (isset($parentid)) {
                    $table->addCell($this->getSectionList($parentid)->show());
                } else {
                    $table->addCell($this->getSectionList()->show());
                }

                $table->endRow();
            } else {
                $table->startRow();
                $table->addCell($objParentId->show().$objRootId->show().$objCount->show().$objOrdering->show(),'','','','',"colspan='2'");
                $table->endRow();
            }

            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;','','','','',"colspan='2'");
            $table->endRow();

            //title name
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('word_title').': ');
            $table->addCell($titleInput->show(),'','','','',"colspan='2'");
            $table->endRow();

            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;','','','','',"colspan='2'");
            $table->endRow();

            //Intro text
            $introText = $this->newObject('htmlarea', 'htmlelements');
            $introText->height = '200px';
            $introText->width = '100%';

            $introText->name = 'introtext';
            $introText->height = '500px';
            if ($editmode) {
                $introText->value = nl2br($section['description']);
            }


            $table->startRow();
            $table->addCell($introText->show(),'','','','',"colspan='3'");
            $table->endRow();
            
            //button
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell($button->show(),'','','','',"colspan='2'");
            $table->endRow();

            $h3->str = $this->objLanguage->languageText('mod_cmsadmin_sectionparams', 'cmsadmin');
            $h3->type = 3;

            $table2->startRow();
            $table2->addCell($h3->show(), null, 'top', null, null, 'colspan="2"');
            $table2->endRow();

            $table2->startRow();
            $table2->addCell('', null, 'top', null, 'cmsvspacer');
            $table2->endRow();

            if (!$editmode) {
                $table2->addCell($this->getSectionConfigTabs());
            }else{
                $table2->addCell($this->getSectionConfigTabs($section));
            }

            $pageParams = new layer();
            $pageParams->id = 'AddSectionPageParams';
            $pageParams->str = $table2->show();

            $tableContainer->startRow();
            $tableContainer->addCell($table->show(),'', 'top', '', 'AddSectionLeft');
            $tableContainer->addCell($pageParams->show(),'', 'top', '', 'AddSectionRight');
            $tableContainer->endRow();

            $objForm->addToForm($tableContainer->show());


            //create heading

            //Get blocks icon
            $objIcon->setIcon('modules/blocks');
            $objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_addremoveblocks', 'cmsadmin');
            $blockIcon = $objIcon->show();
            
            //Check if blocks module is registered
            $this->objModule = &$this->newObject('modules', 'modulecatalogue');
            $isRegistered = $this->objModule->checkIfRegistered('blocks');

            if (!isset($pageId)) {
                $pageId = '';
            }

            // set up link to view block form
            $objBlocksLink = new link('#');
            $objBlocksLink->link = $blockIcon;
            $objBlocksLink->extra = "onclick = \"javascript:window.open('" . $this->uri(array('action' => 'positionblock', 'sectionid' => $sectionId, 'pageid' => $pageId, 'blockcat' => 'section')) . "', 'branch', 'width=500, height=350, top=50, left=50, scrollbars')\"";
    
            if ($this->_objSecurity->canUserWriteContent($pageId)){
                $objBlocksLinkDisplay = '&nbsp;&nbsp;'.$objBlocksLink->show();
            } else {
                $objBlocksLinkDisplay = '';
            }

            if (!$isRegistered) {
                $objBlocksLinkDisplay = '';
            }

            $tbl->cellpadding = 3;
            $tbl->align = "left";
            if ($editmode) {
                $objIcon->setIcon('section_small', 'png', 'icons/cms/');
                $objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_addnewsection', 'cmsadmin');
                $h3->str = $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_editsection', 'cmsadmin').$objBlocksLinkDisplay;
            } else {
                $objIcon->setIcon('section_small', 'png', 'icons/cms/');
                $objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_addnewsection', 'cmsadmin');
                $h3->str = $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_addnewsection', 'cmsadmin').$objBlocksLinkDisplay;
            }
            //Heading box
            $topNav = $this->topNav('addsection');

            $objLayer->str = $h3->show();
            //$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_left';
            $header = $objLayer->show();

            $objLayer->str = $topNav;
            //$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_right';
            $header .= $objLayer->show();

            $objLayer->str = '';
            //$objLayer->border = '; clear:both; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_clear';
            $objLayer->cssClass = 'clearboth';
            $headShow = $objLayer->show();

            //Add content to the output layer
            $middleColumnContent = "";
            $middleColumnContent .= $header.$headShow;//$tbl->show());
            $middleColumnContent .= "<br><br><br><br>".$objForm->show();

            return $middleColumnContent;
        }



       /**
        * Method provides tabboxes for section page artifacts
        * such as metadata and basic cms page behaviour artifacts
        * @param array Content The content to be modified
        * @return str tabs
        *
        */
        public function getSectionConfigTabs($arrSection=NULL){

        /**
        * Defining Basic items to be displayed for 
        * First Tab
        */
            $section = $arrSection;

            //var_dump($arrSection);
            //$tabs =$this->newObject('tabcontent','htmlelements');

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
            $objFiles =$this->getObject('dbfile','filemanager');
            
            $table = new htmlTable();
            //$table->width = "470px";
            $table->width = "100%";
            $table->cellspacing = "0";
            $table->cellpadding = "0";
            $table->border = "0";
            $table->attributes = "align ='center'";
            $this->loadClass('textinput', 'htmlelements');

            if ($arrSection != NULL){
                $editmode = true;
            } else {
                $editmode = false;
            }


            // layout
            // Dropdown list of page layouts - now running on jQuery (^.^)
            $script = '
                <script language="javascript">
                    /*
                    Function to intitialize scriptaculous for 
                    */
                    
                    function jq_processSection(sectionType)
                    {
                        if(sectionType == "page"){
                            jQuery("#pagenumlabel").hide();
                            jQuery("#pagenumcol").hide();
                            jQuery("#dateshowlabel").hide();
                            jQuery("#dateshowcol").hide();
                            jQuery("showintrolabel").show();
                            jQuery("showintrocol").show();
                        } else {
                            jQuery("#pagenumlabel").show();
                            jQuery("#pagenumcol").show();
                            jQuery("#dateshowlabel").show();
                            jQuery("#dateshowcol").show();
                            jQuery("#showintrolabel").show();
                            jQuery("#showintrocol").show();
                        }   
                    
                        if (sectionType == "summaries" || sectionType == "list") {
                            jQuery("#showintrolabel").show();
                            jQuery("#showintrocol").show();
                        }  
                    }

                    jQuery(document).ready(function(){
                        jQuery("#display").change(function(){
                            jq_processSection(this.value); 
                            var path = "'.$this->getResourceUri('', 'cmsadmin').'"; 
                            var image = path+"section_"+this.value+".gif";
                            jQuery("#img").attr("src", image);
                        })
                    });
                </script>';            

            $this->appendArrayVar('headerParams', $script);

            $objDrop = new dropdown('display');
            $objDrop->cssId = 'display';

            $objDrop->addOption('page', $this->objLanguage->languageText('mod_cmsadmin_layout_pagebypage', 'cmsadmin'));
            $objDrop->addOption('previous', $this->objLanguage->languageText('mod_cmsadmin_layout_previouspagebelow', 'cmsadmin'));
            $objDrop->addOption('list', $this->objLanguage->languageText('mod_cmsadmin_layout_listofpages', 'cmsadmin'));
            $objDrop->addOption('summaries', $this->objLanguage->languageText('mod_cmsadmin_layout_summaries', 'cmsadmin'));

            if ($editmode) {
                $objDrop->setSelected($section['layout']);
                $imgPath = $this->getResourceUri('section_'.$section['layout'].'.gif', 'cmsadmin');
                $this->appendArrayVar('bodyOnLoad', "jq_processSection('{$section['layout']}');");
            } else {
                $objDrop->setSelected('page');
                $imgPath = $this->getResourceUri('section_page.gif', 'cmsadmin');
                $this->appendArrayVar('bodyOnLoad', "jq_processSection('page');");
            }

            $objDrop->extra = "onchange=\"javascript:
                                                jq_processSection(this.value); 
                                var path = '{$this->getResourceUri('', 'cmsadmin')}'; 
                                var image = path+'section_'+this.value+'.gif';
                                jQuery('#img').attr('src').value = image;\"";

            $imgStr = "<img id='img' src='{$imgPath}' />";

            $layoutStr = $objDrop->show();

            //layout preview image place holder
            $imagesrc = $this->_objConfig->getSiteRootPath().'/skins/_common/blank.png';
            $image ="<img src='{$imagesrc}' name='imagelib' id='imagelib' border='2' height='80' width='80' />" ;
            $imageThumb = new textinput('imagesrc',$imagesrc,'hidden');
            
            //read for images
            $listFiles = $objFiles->getUserFiles($this->_objUser->userId(), null);  

            $drp_image = new dropdown('image');
            $drp_image->id= 'image';
            $drp_image->extra = 'onchange="javascript:if(this.value!=\'\'){$(\'imagelib\').src = \'usrfiles/\'+this.value;$(\'input_imagesrc\').value = \'usrfiles/\'+this.value}else{$(\'imagelib\').src = \'../images/blank.png\'}"';
            $drp_image->addOption('','- Select Image -');
            $drp_image->addFromDB($listFiles,'filename','path');

            //Show Intro
            $label = new label ($this->objLanguage->languageText('mod_cmsadmin_showintro', 'cmsadmin').': ', 'input_showintro');
            $showdate = new radio ('showintro');
            $showdate->addOption('1', $this->objLanguage->languageText('word_yes'));
            $showdate->addOption('0', $this->objLanguage->languageText('word_no'));
            if ($editmode) {
                $showdate->setSelected($section['show_introduction']);
            } else {
                $showdate->setSelected('1');
            }
            $showdate->setBreakSpace(' &nbsp; ');

            //Show Date
            $label = new label ($this->objLanguage->languageText('mod_cmsadmin_showintro', 'cmsadmin').': ', 'input_showintro');
            $showdate = new radio ('showintro');
            $showdate->addOption('1', $this->objLanguage->languageText('word_yes'));
            $showdate->addOption('0', $this->objLanguage->languageText('word_no'));
            if ($editmode) {
                $showdate->setSelected($section['show_introduction']);
            } else {
                $showdate->setSelected('1');
            }
            $showdate->setBreakSpace(' &nbsp; ');

            //$table->startRow();
            //$table->addCell('<div id="showintrolabel">'.$label->show().'</div>');
            //$table->addCell('<div id="showintrocol">'.$this->objLanguage->languageText('mod_cmsadmin_showintrotext', 'cmsadmin').' '.$showdate->show().'<br /><br />'.$introText->show().'</div>','','','','',"colspan='2'");
            //$table->endRow();

            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;','','','','',"colspan='2'");
            $table->endRow();


            //No. pages to display
            $lbOther = $this->objLanguage->languageText('phrase_othernumber');
            $label = new label ($this->objLanguage->languageText('phrase_numberofpages').': ', 'input_pagenum');
            $pagenum = new dropdown('pagenum');
            $pagenum->addOption('0', $this->objLanguage->languageText('phrase_showall'));
            $pagenum->addOption('3', '3');
            $pagenum->addOption('5', '5');
            $pagenum->addOption('10', '10');
            $pagenum->addOption('20', '20');
            $pagenum->addOption('30', '30');
            $pagenum->addOption('50', '50');
            $pagenum->addOption('100', '100');

            $pagenum->addOption('custom', $lbOther);

            if ($editmode) {
                $num = $section['numpagedisplay'];

                if ($num == '0' || $num == '3' || $num == '5' || $num == '10' || $num == '20' || $num == '30' || $num == '50' || $num == '100') {
                    $pagenum->setSelected($section['numpagedisplay']);
                } else {
                    $pagenum->setSelected('custom');
                }
            } else {
                $pagenum->setSelected('0');
            }
            $pagenum->extra = "onclick=\"javascript: if(this.value == 'custom'){document.getElementById('input_customnumber').disabled=false;}
                                else{document.getElementById('input_customnumber').value='';
                                                document.getElementById('input_customnumber').disabled=true;}\"";

            //Input custom no.
            $customInput = new textinput('customnumber');
            if ($editmode && $section['numpagedisplay'] != '0') {
                $customInput->value = $section['numpagedisplay'];
            }else{
                $customInput->extra = "disabled='true'";
            }

            $numStr = $this->objLanguage->languageText('mod_cmsadmin_numpagesdisplaypersection', 'cmsadmin');
            $numStr .= '<p>'.$pagenum->show().'&nbsp;&nbsp;'.$lbOther.': '.$customInput->show().'</p>';
            $numStr .= '<p class="warning">* '.$this->objLanguage->languageText('mod_cmsadmin_numpagesonlyrequiredwhen', 'cmsadmin').'</p>';

            $table->startRow();
            $table->addCell('<div id="pagenumlabel">'.$label->show().'</div>');
            $table->addCell('<div id="pagenumcol">'.$numStr.'</div>','','','','',"colspan='2'");
            $table->endRow();

            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;','','','','',"colspan='2'");
            $table->endRow();

            //Show date or not
            $label = new label ($this->objLanguage->languageText('phrase_showdate').': ', 'input_showdate');
            $showdate = new radio ('showdate');
            $showdate->addOption('1', $this->objLanguage->languageText('word_yes'));
            $showdate->addOption('0', $this->objLanguage->languageText('word_no'));
            if ($editmode) {
                $showdate->setSelected($section['show_date']);
            } else {
                $showdate->setSelected('1');
            }
            $showdate->setBreakSpace(' &nbsp; ');

            $table->startRow();
            $table->addCell('<div id="dateshowlabel">'.$label->show().'</div>');
            $table->addCell('<div id="dateshowcol">'.$this->objLanguage->languageText('mod_cmsadmin_shoulddatebedisplayed', 'cmsadmin').' '.$showdate->show().'</div>','','','','',"colspan='2'");
            $table->endRow();
            //add context
            if ($this->inContextMode) {
                $ContextInput=new textinput();
                $ContextInput->name = 'Contextcode';
                $ContextInput->id = 'Contextcode';
                $ContextInput->fldType = 'hidden';
                $ContextInput->value = $this->contextCode;
                $table->startRow();
                $table->addCell('&nbsp;');
                $table->addCell($ContextInput->show(),'','','','',"colspan='2'");
                $table->endRow();
            }































            $frontPage = new checkbox('showintro');
            $frontPage->value = 1;

            $show_content = '0';

            $show_introduction = 'g';
            $show_title = 'g';
            $show_author = 'g';
            $show_date = 'g';

            $lbl_introduction = $this->objLanguage->languageText('mod_cmsadmin_show_introduction','cmsadmin');
            $lbl_title = $this->objLanguage->languageText('mod_cmsadmin_show_title','cmsadmin');
            $lbl_date = $this->objLanguage->languageText('mod_cmsadmin_show_date','cmsadmin');
            $lbl_author = $this->objLanguage->languageText('mod_cmsadmin_show_author','cmsadmin');

            //date controls
            if (is_array($arrSection)) {

                //Initializing options

                $published = $arrSection['published'];
                $visible = $published;

                //show intro
                if (isset($arrContent['show_introduction'])) {
                    $show_introduction = $arrContent['show_introduction'];
                }

                //show title
                if (isset($arrContent['show_title'])) {
                    $show_title = $arrContent['show_title'];
                }

                //show author
                if (isset($arrContent['show_author'])) {
                    $show_author = $arrContent['show_author'];
                }

                //show date
                if (isset($arrContent['show_date'])) {
                    $show_date = $arrContent['show_date'];
                }


                //Set licence option
                if(isset($arrSection['post_lic'])){
                    $objCCLicence->defaultValue = $arrSection['post_lic'];
                }

                //convert to strings to datetime
                if(isset($arrSection['created'])){
                    $override = date("Y-m-d H:i:s",strtotime($arrSection['created']));
                }
                $start_pub = '';
                $end_pub = '';
                
                // ---------------------------- Uncrapification if statements added by Derek Keats
                if( isset($arrSection['created']) && isset($arrSection['end_publish']) ){
                    if (!is_null($arrSection['start_publish'])) {
                        $start_pub = date("Y-m-d H:i:s",strtotime($arrSection['start_publish']));
                    } elseif (!is_null($arrSection['end_publish'])) {
                        $end_pub = date("Y-m-d H:i:s",strtotime($arrSection['end_publish']));
                    }
                }
                if( isset($override) ) {
                    $dateField = $objdate->show('overide_date', 'yes', 'no', $override);
                }
                if ( isset($start_pub) ) {
                    $pub = $publishing->show('publish_date', 'yes', 'no',$start_pub);
                }
                if ( isset($end_pub) ) {
                    $end_pub = $publishing_end->show('end_date', 'yes', 'no', $end_pub);
                }
                //Author Alias
                if ( isset ($arrSection['created_by_alias']) ) {
                    $author = new textinput('author_alias',$arrSection['created_by_alias'],null,20);
                }
                // ---------------------------- End of uncrapification if statements added by Derek Keats
                //Pre-populated dropdown

                //Change Created Date
                $lbl_date_created = new label($this->objLanguage->languageText('mod_cmsadmin_override_creation_date','cmsadmin'),'overide_date');
                $lbl_pub = new label($this->objLanguage->languageText('mod_cmsadmin_start_publishing','cmsadmin'),'publish_date');
                $lbl_pub_end = new label($this->objLanguage->languageText('mod_cmsadmin_end_publishing','cmsadmin'),'end_date');

            } else {
                //Initializing options

                $published->setChecked(TRUE);
                $visible = TRUE;
                $show_title = 'g';
                $show_user = 'g';
                $show_date = 'g';
                $show_introduction = 'g';
                $contentId = '';
                $arrSection = null;

                $dateField = $objdate->show('overide_date', 'yes', 'no', '');
                $pub = $publishing->show('publish_date', 'yes', 'no', '');
                $end_pub = $publishing_end->show('end_date', 'yes', 'no', '');

                //Change Created Date
                $lbl_date_created = new label($this->objLanguage->languageText('mod_cmsadmin_override_creation_date','cmsadmin'),'overide_date');
                $lbl_pub = new label($this->objLanguage->languageText('mod_cmsadmin_start_publishing','cmsadmin'),'publish_date');
                $lbl_pub_end = new label($this->objLanguage->languageText('mod_cmsadmin_end_publishing','cmsadmin'),'end_date');
            }

            $tbl_basic->startRow();
            $tbl_basic->addCell($this->objLanguage->languageText('word_publish').': &nbsp; ', '200');
            $tbl_basic->addCell($this->getYesNoRadion('published', $visible, false));
            //$tbl_basic->addCell($published->show());
            $tbl_basic->endRow();

            $lbNo = $this->objLanguage->languageText('word_no');
            $lbYes = $this->objLanguage->languageText('word_yes');

            //intro option
            $opt_introduction = new dropdown('show_introduction');
            $opt_introduction->addOption('g',"-Global-Option-");
            $opt_introduction->addOption("y",'yes');
            $opt_introduction->addOption("n",'no');
            $opt_introduction->setSelected($show_introduction);

            //title option
            $opt_title = new dropdown('show_title');
            $opt_title->addOption('g',"-Global-Option-");
            $opt_title->addOption("y",'yes');
            $opt_title->addOption("n",'no');
            $opt_title->setSelected($show_title);

            //author option
            $opt_author = new dropdown('show_author');
            $opt_author->addOption('g',"-Global-Option-");
            $opt_author->addOption('y','yes');
            $opt_author->addOption('n','no');
            $opt_author->setSelected($show_author);

            //date option
            $opt_date = new dropdown('show_date');
            $opt_date->addOption('g',"-Global-Option-");
            $opt_date->addOption('y','yes');
            $opt_date->addOption('n','no');
            $opt_date->setSelected($show_date);

            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_introduction, null, 'top', null, 'cmstinyvspacer');
            $tbl_basic->addCell($opt_introduction->show());
            $tbl_basic->endRow();

            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_title, null, 'top', null, 'cmstinyvspacer');
            $tbl_basic->addCell($opt_title->show());
            $tbl_basic->endRow();

            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_author, null, 'top', null, 'cmstinyvspacer');
            $tbl_basic->addCell($opt_author->show());
            $tbl_basic->endRow();

            $tbl_basic->startRow();
            $tbl_basic->addCell($lbl_date, null, 'top', null, 'cmstinyvspacer');
            $tbl_basic->addCell($opt_date->show());
            $tbl_basic->endRow();

            //Hide User
            /*
            $objRadio = new radio('hide_user');
            $objRadio->addOption('1', '&nbsp;'.$lbYes);
            $objRadio->addOption('0', '&nbsp;'.$lbNo);
            $objRadio->setSelected($hide_user);
            $objRadio->setBreakSpace('&nbsp;&nbsp;');

            $tbl_basic->startRow();
            $tbl_basic->addCell($this->objLanguage->languageText('phrase_hideuser').': &nbsp; ');
            $tbl_basic->addCell($objRadio->show());
            $tbl_basic->endRow();
            */

            //Hide Date
            /*
            $objRadio = new radio('hide_date');
            $objRadio->addOption('1', '&nbsp;'.$lbYes);
            $objRadio->addOption('0', '&nbsp;'.$lbNo);
            $objRadio->setSelected($hide_date);
            $objRadio->setBreakSpace('&nbsp;&nbsp;');

            $tbl_basic->startRow();
            $tbl_basic->addCell($this->objLanguage->languageText('phrase_hidedate').': &nbsp; ');
            $tbl_basic->addCell($objRadio->show());
            $tbl_basic->endRow();
            */


            // Radio button to display the full content or only the summary on the front page
            //$lbDisplay = $this->objLanguage->languageText('mod_cmsadmin_displaysummaryorcontent', 'cmsadmin');
            $lbIntroOnly = $this->objLanguage->languageText('mod_cmsadmin_introonly', 'cmsadmin');
            $lbFullContent = $this->objLanguage->languageText('mod_cmsadmin_fullcontent', 'cmsadmin');

            //Toggle Intro Only vs Full Content
            //$toggleLayer = new layer();
            //$toggleLayer->id = 'toggle_layer_param';
            //$toggleLayer->str = $this->objLanguage->languageText('mod_cmsadmin_toggleintrocontent', 'cmsadmin') . ":<br/>" . $objRadio->show();            

            //$tbl_basic->startRow();
            //$tbl_basic->addCell($toggleLayer->show(), '', '', '', '', 'colspan="2"');
            //$tbl_basic->endRow();

            //Author Alias
            //$tbl_basic->startRow();
            //$tbl_basic->addCell($lbl_author->show());
            //$tbl_basic->addCell($author->show());
            //$tbl_basic->endRow();

            //Order type
            $label = new label ($this->objLanguage->languageText('mod_cmsadmin_orderpagesby', 'cmsadmin').': ', 'input_pageorder');
            $pageOrder = new dropdown('pageorder');
            $pageOrder->addOption('pageorder', $this->objLanguage->languageText('mod_cmsadmin_order_pageorder', 'cmsadmin'));
            $pageOrder->addOption('pagedate_asc', $this->objLanguage->languageText('mod_cmsadmin_order_pagedate_asc', 'cmsadmin'));
            $pageOrder->addOption('pagedate_desc', $this->objLanguage->languageText('mod_cmsadmin_order_pagedate_desc', 'cmsadmin'));
            $pageOrder->addOption('pagetitle_asc', $this->objLanguage->languageText('mod_cmsadmin_order_pagetitle_asc', 'cmsadmin'));
            $pageOrder->addOption('pagetitle_desc', $this->objLanguage->languageText('mod_cmsadmin_order_pagetitle_desc', 'cmsadmin'));
            if ($editmode) {
                $pageOrder->setSelected($section['ordertype']);
            } else {
                $pageOrder->setSelected('pageorder');
            }


            $tbl_advanced->startRow();
            $tbl_advanced->addCell($label->show());
            $tbl_advanced->endRow();
            $tbl_advanced->startRow();
            $tbl_advanced->addCell($pageOrder->show(),'','','','',"colspan='2'");
            $tbl_advanced->endRow();

            $tbl_advanced->startRow();
            $tbl_advanced->addCell('&nbsp;','','','','cmsvspacer',"");
            $tbl_advanced->endRow();


            $tbl_advanced->startRow();
            $tbl_advanced->addCell($this->objLanguage->languageText('mod_cmsadmin_layoutofpages', 'cmsadmin').': ','','center');
            $tbl_advanced->endRow();
            $tbl_advanced->startRow();
            $tbl_advanced->addCell($layoutStr, '20%', 'center');
            $tbl_advanced->endRow();
            $tbl_advanced->startRow();
            $tbl_advanced->addCell($imgStr, '', '', '', '', 'colspan="2"');
            $tbl_advanced->endRow();
    
            $tabs->addTab($this->objLanguage->languageText('mod_cmsadmin_basic','cmsadmin'),$tbl_basic->show(),'',False,'');
            $tabs->addTab($this->objLanguage->languageText('mod_cmsadmin_layout','cmsadmin'),$tbl_advanced->show(),'',False,'');

            //$tabs->width = '70%';
            $tabs->cssClass = 'addSectionTabs';

            return $tabs->show();
        }




      /**
        * Method to return the add edit section PERMISSIONS form
        *
        * @param string $sectionId The id of the section to be edited. Default NULL for adding new section
        * @return string $middleColumnContent The form used to create and edit a section
        * @access public
        * @author Charl Mert <charl.mert@gmail.com>
        */
        public function getAddEditPermissionsSectionForm($sectionid = NULL, $returnSubView=0)
        {

            if ($returnSubView == 0){
                $returnSubView = $this->getParam('subview');
            }

            $subSecId = $this->getParam('parent');

            $sectionArr = $this->_objSections->getSection($sectionid);
            $sectionName = $sectionArr['title'];

            $this->loadClass('form', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');
            $this->loadClass('button', 'htmlelements');

            if ($returnSubView == 1){
                //Setting for to return to the SubView Page
                $objForm = new form('add_permissions_frm', $this->uri(array('action' => 'view_permissions_section', 'id' => $sectionid, 'parent' => $subSecId), 'cmsadmin'));
            } else {
                //Setting form to return to the Sections List Page (Default)
                $objForm = new form('add_permissions_frm', $this->uri(array('action' => 'addpermissions', 'id' => $sectionid), 'cmsadmin'));

            }
            $objForm->setDisplayType(3);

            //Start Header
            //initiate objects for header
            $table =  $this->newObject('htmltable', 'htmlelements');
            $objH = $this->newObject('htmlheading', 'htmlelements');
            $link =  $this->newObject('link', 'htmlelements');
            $objIcon =  $this->newObject('geticon', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
            $objRound =$this->newObject('roundcorners','htmlelements');
            $objLayer =$this->newObject('layer','htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');

            $topNav = $this->topNav('addpermissions');

            $tbl = $this->newObject('htmltable', 'htmlelements');
            $tbl->cellpadding = 3;
            $tbl->align = "left";

            //create a heading
            $objH->type = '1';

            //Heading box
            $objIcon->setIcon('section', 'png', 'icons/cms/');
            //$objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin');
            $objIcon->title = 'Edit Section Permissions';
            //$objH->str =  $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin')."<br/><h3>Sections List</h3>";
            $objH->str =  $objIcon->show().'&nbsp;'.$objIcon->title."<br/><h3>Sections Title: $sectionName</h3>";
            $tbl->startRow();
            $tbl->addCell($objH->show(), '', 'center');
            $tbl->addCell($topNav, '','center','right');
            $tbl->endRow();

            $objLayer->str = $objH->show();
            //$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_left';
            $header = $objLayer->show();
            $objLayer->str = $topNav;
            //$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_right';
            $header .= $objLayer->show();

            $objLayer->str = '';
            //$objLayer->border = '; clear:both; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_clear';
            $objLayer->cssClass = 'clearboth';
            $headShow = $objLayer->show();
            // end header

            //Setting up the table
            $table = new htmlTable();
            $table->width = "100%";
            $table->cellspacing = "0";
            $table->cellpadding = "0";
            $table->border = "0";
            $table->attributes = "align ='center'";

            $table->startHeaderRow();
            $table->addHeaderCell('Users / Groups');
            $table->addHeaderCell('Read');
            $table->addHeaderCell('Write');
            $table->endHeaderRow();

            //Looping through Users and Groups Assigned to Current Section
            //for (){...


            //TODO: Put the data query below into a freakin function charl ;-)
            //Getting origonal members from DB
            //Preparing a list of USER ID's
            $usersList = $this->_objSecurity->getAssignedSectionUsers($sectionid);
            $usersCount = count($usersList);

            //Preparing a list of GROUP_ID's
            $groupsList = $this->_objSecurity->getAssignedSectionGroups($sectionid);
            $groupsCount = count($groupsList);
            $globalChkCounter = 0;

            //Displaying Groups
            for ($x = 0; $x < $groupsCount; $x++){
                $memberName = $groupsList[$x]['name'];
                $memberReadAccess = $groupsList[$x]['read_access'];			
                $memberWriteAccess = $groupsList[$x]['write_access'];			

                $canRead = (($memberReadAccess == 1) ? true : false);
                $canWrite = (($memberWriteAccess == 1) ? true : false);

                $chkRead = new checkbox('chk_read-'.$globalChkCounter, 'Read', $canRead);
                $chkWrite = new checkbox('chk_write-'.$globalChkCounter, 'Write', $canWrite);

                $globalChkCounter += 1;

                //Adding Users / Groups
                $table->startRow();
                $table->addCell($memberName);
                $table->addCell($chkRead->show());
                $table->addCell($chkWrite->show());
                $table->endRow();

            }	//End Loop

            //Displaying Users
            for ($x = 0; $x < $usersCount; $x++){
                $memberName = $usersList[$x]['username'];
                $memberReadAccess = $usersList[$x]['read_access'];
                $memberWriteAccess = $usersList[$x]['write_access'];
                
                $canRead = (($memberReadAccess == 1) ? true : false);
                $canWrite = (($memberWriteAccess == 1) ? true : false);
                
                $chkRead = new checkbox('chk_read-'.$globalChkCounter, 'Read', $canRead);
                $chkWrite = new checkbox('chk_write-'.$globalChkCounter, 'Write', $canWrite);

                $globalChkCounter += 1;

                //Adding Users / Groups
                $table->startRow();
                $table->addCell($memberName);
                $table->addCell($chkRead->show());
                $table->addCell($chkWrite->show());
                $table->endRow();

            }       //End Loop

            //Add User / Group Link
            $lnkAddUserGroup = new link();
            $lnkAddUserGroup->link = "Add User/Group";

            if ($returnSubView == 1){
                //Setting for to return to the SubView Page
                $lnkAddUserGroup->href = $this->uri(array('action' => 'addpermissions_user_group', 'id' => $sectionid, 'parent' => $subSecId, 'subview' => '1'));
            } else {
                //Setting form to return to the Sections List Page (Default)
                $lnkAddUserGroup->href = $this->uri(array('action' => 'addpermissions_user_group', 'id' => $sectionid, 'parent' => $subSecId));
            }


            $btnSubmit = new button('save_btn', 'Save');
            $btnSubmit->setToSubmit(); 


            //Setting up the Owner table
            $tblOwner = new htmlTable();
            $tblOwner->width = "100%";
            $tblOwner->cellspacing = "0";
            $tblOwner->cellpadding = "0";
            $tblOwner->border = "0";
            $tblOwner->attributes = "align ='center'";

            //Setting up the Owner table

            $drpOwner = new dropdown('drp_owner');
            $allUsers = $this->_objSecurity->getAllUsers();
            $allUsersCount = count($allUsers);
            //var_dump($allUsers);	
            for ($i = 0; $i < $allUsersCount; $i++){
                //$drpOwner->addOption($allUsers[$i]['userid'], $allUsers[$i]['firstname'].' '.$allUsers[$i]['surname']);
                //echo 'Owner ID : '.$allUsers[$i]['userid'].'<br/>';
                $drpOwner->addOption($allUsers[$i]['userid'], $allUsers[$i]['username']);
            }

            //Getting the current owner of the section
            $section = $this->_objSections->getSection($sectionid);
            $ownerName = $this->_objUser->userName($section['userid']);

            $drpOwner->setSelected($section['userid']);	

            //must set the default owner
            //$drpOwner->setSelected();

            $chkPropagate = new checkbox('chk_propagate', 'Propagate', false);
            $chkPropagate->setLabel("Allow permissions to propagate to child items");

            $chkPropagateOwner = new checkbox('chk_propagate_owner', 'Propagate Owner', false);
            $chkPropagateOwner->setLabel("Make this the owner of all child items in this section");

            //Owner Select Box
            $tblOwner->startRow();
            $tblOwner->addCell($drpOwner->show());
            $tblOwner->endRow();

            //Check box to force inheritence on child sections and content

            //$tblOwner->startRow();
            //$tblOwner->addCell($chkInherit->show()." Force Ownership on Child Sections &amp; Content Items");
            //$tblOwner->endRow();

            $canPublic = (($sectionArr['public_access'] == 0)? true : false);
            
            $chkPublic = new checkbox('chk_public', 'Public', $canPublic);
            $topNav = $this->topNav('addpermissions');
            
            //$objForm->addToForm("<h1>Edit Section Permissions</h1><h3>Section Title : $sectionName</h3><br/>");
            $objForm->addToForm($header.$headShow);
            $objForm->addToForm("<br/><h4>Authorised Members:</h4><br/>");
            $objForm->addToForm($table->show());
            $objForm->addToForm('<br/>');
            $objForm->addToForm($lnkAddUserGroup->show());
            $objForm->addToForm('<br/>');
            $objForm->addToForm($chkPublic->show() . ' Only allow logged in users to access this section.');
            $objForm->addToForm('<br/><br/>' .$chkPropagate->show()." Allow permissions to propagate to child items.");
            $objForm->addToForm('<br/><input type="hidden" name="id" value="'.$this->getParam('parent').'"/>');
            $objForm->addToForm('<br/><input type="hidden" name="cid" value="'.$sectionid.'"/>');
            $objForm->addToForm( "<input type='hidden' name='subview' value='$returnSubView'>" );
            $objForm->addToForm('<br/><input type="hidden" name="chkCount" value="'.$globalChkCounter.'"/>');
            if ($returnSubView == 1){
                //Setting for to return to the SubView Page
                $objForm->addToForm('<br/><input type="hidden" name="action" value="view_permissions_section"/>');
            } else {
                //Setting form to return to the Sections List Page (Default)
                $objForm->addToForm('<br/><input type="hidden" name="action" value="addpermissions"/>');
            }
            $objForm->addToForm( "<input type='hidden' name='button' value='saved'>" ); 
            $objForm->addToForm("<h4>Owner:</h4>");
            $objForm->addToForm($tblOwner->show());
            $objForm->addToForm('<br/>');
            $objForm->addToForm($chkPropagateOwner->show()." Make this the owner of all child items in this section.");
            $objForm->addToForm('<br/>');
            $objForm->addToForm($btnSubmit->show());

            $display = $objForm->show();
            return $display;
        }


                /**
                 * Method to return the add edit section permissions USER GROUP form
                 *
                 * @param string $sectionId The id of the section to be edited. Default NULL for adding new section
                 * @return string $middleColumnContent The form used to create and edit a section
                 * @access public
                 * @author Charl Mert <charl.mert@gmail.com>
                 */
        public function getAddEditPermissionsSectionUserGroupForm($sectionid = NULL)
        {
            $parentId = $this->getParam('parent');
            $returnSubView = $this->getParam('subview');

            $objLanguage = $this->objLanguage;

            $memberList = $this->sectionMemberList($sectionid);
            //$memberList = array();

            $usersList = $this->sectionUsersList($sectionid);

            if ( $sectionid == NULL) {
                $errMsg = 'unknown section';
                $this->setVar( 'errorMsg', $errMsg );
            } 

            // Members list dropdown
            $this->loadClass('dropdown', 'htmlelements');
            $lstMembers = new dropdown('list2[]');
            $lstMembers->extra = ' style="width:200pt" MULTIPLE SIZE=10 onDblClick="moveSelectedOptions(this.form[\'list2[]\'],this.form[\'list1[]\'],true)"';
            foreach ( $memberList as $user ) {
                if (array_key_exists('firstname', $user) && ($user['firstname'] != '') && ($user['surname'] != '')){
                    $fullName = $user['firstname'] . " " . $user['surname'];
                    $userPKId = $user['id'].'|user';
                } else {
                    $fullName = $user['username'];
                    $userPKId = $user['id'].'|group';
                }
                $lstMembers->addOption( $userPKId, $fullName );
            }
            // Users list dropdown
            $lstUsers = new dropdown('list1[]');
            $lstUsers->extra = ' style="width:200pt" MULTIPLE SIZE=10 onDblClick="moveSelectedOptions(this.form[\'list1[]\'],this.form[\'list2[]\'],true)"';


            foreach ( $usersList as $user ) {

                if (array_key_exists('firstname', $user) && ($user['firstname'] != '') && ($user['surname'] != '')){
                    $fullName = $user['firstname'] . " " . $user['surname'];
                    $userPKId = $user['id'].'|user';
                } else {
                    $fullName = $user['username'];
                    $userPKId = $user['id'].'|group';
                }
                $lstUsers->addOption( $userPKId, $fullName );
            }

            // Build the nonMember table.
            //$hdrUsers = $objLanguage->languageText('mod_cmsadmin_word_user','cmsadmin');
            $hdrUsers = "User / Group";

            $tblUsers = '<table><tr><th>' . $hdrUsers . '</th></tr><tr><td>' . $lstUsers->show() . '</td></tr></table>'; 

            // Build the Member table.
            //$hdrMemberList = $objLanguage->languageText( 'mod_groupadmin_hdrMemberList','groupadmin' );
            $hdrMemberList = "Members";

            $tblMembers = '<table><tr><th>' . $hdrMemberList . '</th></tr><tr><td>' . $lstMembers->show() . '</td></tr></table>'; 

            // The save button
            $btnSave = $this->newObject( 'button', 'htmlelements' );
            $btnSave->name = 'btnSave';
            $btnSave->cssClass = null;
            $btnSave->value = $objLanguage->languageText( 'word_save' );
            $btnSave->onclick = "selectAllOptions(this.form['list2[]'])";
            $btnSave->setToSubmit(); 

            // The save link button
            $btnSave = $this->newObject( 'button', 'htmlelements' );
            $btnSave->name = 'btnSave';
            $btnSave->cssClass = null;
            $btnSave->value = $objLanguage->languageText( 'word_save' );
            $btnSave->onclick = "selectAllOptions(this.form['list2[]'])";
            $btnSave->setToSubmit(); 

            // Link method
            $lnkSave = "<a href=\"#\" onclick=\"javascript:selectAllOptions(document.frmEdit['list2[]']); document.frmEdit['button'].value='save'; document.frmEdit.submit()\">";
            $lnkSave .= $objLanguage->languageText( 'word_save' ) . "</a>"; 

            // The cancel button
            $btnCancel = $this->newObject( 'button', 'htmlelements' );
            $btnCancel->name = 'btnCancel';
            $btnCancel->value = $objLanguage->languageText( 'word_Cancel' );
            $btnCancel->setToSubmit(); 

            // Link method
            $lnkCancel = "<a href=\"#\" onclick=\"javascript:document.frmEdit['button'].value='cancel'; document.frmEdit.submit()\">";
            $lnkCancel .= $objLanguage->languageText( 'word_cancel' ) . "</a>"; 

            // Form control buttons
            $buttons = array( $lnkSave, $lnkCancel ); 

            // The move selected items right button
            $btnRight = $this->newObject( 'button', 'htmlelements' );
            $btnRight->name = 'right';
            $btnRight->value = htmlspecialchars( '>>' );
            $btnRight->onclick = "moveSelectedOptions(this.form['list1[]'],this.form['list2[]'],true)"; 

            // Link method
            $lnkRight = "<a href=\"#\" onclick=\"javascript:moveSelectedOptions(document.frmEdit['list1[]'],document.frmEdit['list2[]'],true)\">";
            $lnkRight .= htmlspecialchars( '>>' ) . "</a>"; 

            // The move all items right button
            $btnRightAll = $this->newObject( 'button', 'htmlelements' );
            $btnRightAll->name = 'right';
            $btnRightAll->value = htmlspecialchars( 'All >>' );
            $btnRightAll->onclick = "moveAllOptions(this.form['list1[]'],this.form['list2[]'],true)"; 

            // Link method
            $lnkRightAll = "<a href=\"#\" onclick=\"javascript:moveAllOptions(document.frmEdit['list1[]'],document.frmEdit['list2[]'],true)\">";
            $lnkRightAll .= htmlspecialchars( 'All >>' ) . "</a>"; 

            // The move selected items left button
            $btnLeft = $this->newObject( 'button', 'htmlelements' );
            $btnLeft->name = 'left';
            $btnLeft->value = htmlspecialchars( '<<' );
            $btnLeft->onclick = "moveSelectedOptions(this.form['list2[]'],this.form['list1[]'],true)"; 

            // Link method
            $lnkLeft = "<a href=\"#\" onclick=\"javascript:moveSelectedOptions(document.frmEdit['list2[]'],document.frmEdit['list1[]'],true)\">";
            $lnkLeft .= htmlspecialchars( '<<' ) . "</a>"; 

            // The move all items left button
            $btnLeftAll = $this->newObject( 'button', 'htmlelements' );
            $btnLeftAll->name = 'left';
            $btnLeftAll->value = htmlspecialchars( 'All <<' );
            $btnLeftAll->onclick = "moveAllOptions(this.form['list2[]'],this.form['list1[]'],true)"; 

            // Link method
            $lnkLeftAll = "<a href=\"#\" onclick=\"javascript:moveAllOptions(document.frmEdit['list2[]'],document.frmEdit['list1[]'],true)\">";
            $lnkLeftAll .= htmlspecialchars( 'All <<' ) . "</a>"; 

            // The move items (Insert and Remove) buttons
            $btns = array( $lnkRight, $lnkRightAll, $lnkLeft, $lnkLeftAll );
            $tblInsertRemove = '<div>' . implode( '<br /><br />', $btns ) . '</div>'; 

            // Form Layout Elements
            $tblLayout = $this->newObject( 'htmltable', 'htmlelements' );
            $tblLayout->row_attributes = 'align=center';
            $tblLayout->width = '99%';
            $tblLayout->startRow();
            $tblLayout->addCell( $tblUsers, null, null );
            $tblLayout->addCell( $tblInsertRemove, null, null );
            $tblLayout->addCell( $tblMembers, null, null );
            $tblLayout->endRow();

            // Title and Header
            $ttlEditGroup = $objLanguage->languageText( 'mod_groupadmin_ttlEditGroup','groupadmin' );
            $hlpEditGroup = $objLanguage->languageText( 'mod_groupadmin_hlpEditGroup','groupadmin' );
            $hdrEditGroup = $objLanguage->languageText( 'mod_groupadmin_hdrEditGroup','groupadmin' ); 

            // Context Home Icon
            $lblContextHome = $this->objLanguage->languageText( "word_course" ) . ' ' . $this->objLanguage->languageText( "word_home" );
            $icnContextHome = $this->newObject( 'geticon', 'htmlelements' );
            $icnContextHome->setIcon( 'home' );
            $icnContextHome->alt = $lblContextHome;

            $lnkContextHome = $this->newObject( 'link', 'htmlelements' );
            $lnkContextHome->href = $this->uri( array(), 'context' );
            $lnkContextHome->link = $icnContextHome->show() . $lblContextHome;

            $return = $this->getParam( 'return' ) == 'context' ? 'context' : 'main';
            $confirm = $this->getParam( 'confirm' ) ? TRUE : FALSE; 

            // Form Elements
            $frmEdit = $this->newObject( 'form', 'htmlelements' );
            $frmEdit->name = 'frmEdit';
            $frmEdit->displayType = '3';
            $frmEdit->action = $this->uri ( array( 'action' => 'add_section_members' ) );
            $frmEdit->addToForm( "<div id='blog-content'>" . $tblLayout->show() . "</div>" );
            $frmEdit->addToForm( "<div id='blog-footer'>" . implode( '&#160;', $buttons ) . "</div>" );
            $frmEdit->addToForm( "<input type='hidden' name='id' value='$sectionid'>" );
            $frmEdit->addToForm( "<input type='hidden' name='parent' value='$parentId'>" );
            $frmEdit->addToForm( "<input type='hidden' name='subview' value='$returnSubView'>" );
            $frmEdit->addToForm( "<input type='hidden' name='return' value='$return'>" );
            $frmEdit->addToForm( "<input type='hidden' name='confirm' value='TRUE'>" );
            $frmEdit->addToForm( "<input type='hidden' name='button' value='saved'>" ); 

            // Back link button
            $lnkBack = $this->newObject( 'link', 'htmlelements' );
            $lnkBack->href = $this->uri ( array() );
            $lnkBack->link = $objLanguage->languageText( 'mod_groupadmin_back' ,'groupadmin'); 

            // $lnkBack->cssClass = 'pseudobutton';
            $this->setVar( 'frmEdit', $frmEdit );
            $this->setVar( 'return', $return );
            $this->setVar( 'lnkContextHome', $lnkContextHome );
            $this->setVar( 'lnkBack', $lnkBack );
            $this->setVar( 'ttlEditGroup', $ttlEditGroup );
            //$this->setVar( 'fullPath', $fullPath );
            $this->setVar( 'confirm', $confirm );


            $sectionName = $this->_objSections->getSection($sectionid);
            $sectionName = $sectionName['title'];

            //Start Header
            //initiate objects for header
            $table =  $this->newObject('htmltable', 'htmlelements');
            $objH = $this->newObject('htmlheading', 'htmlelements');
            $link =  $this->newObject('link', 'htmlelements');
            $objIcon =  $this->newObject('geticon', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
            $objRound =$this->newObject('roundcorners','htmlelements');
            $objLayer =$this->newObject('layer','htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');

            $topNav = $this->topNav('addpermissions');

            $tbl = $this->newObject('htmltable', 'htmlelements');
            $tbl->cellpadding = 3;
            $tbl->align = "left";

            //create a heading
            $objH->type = '1';

            //Heading box
            $objIcon->setIcon('permissions_med', 'png', 'icons/cms/');
            //$objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin');
            $objIcon->title = 'Edit Section Permissions';
            //$objH->str =  $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin')."<br/><h3>Sections List</h3>";
            $objH->str =  $objIcon->show().'&nbsp;'.$objIcon->title."<br/><h3>Section Title: $sectionName</h3><br/><h2>Add Authorized Members</h2>";
            $tbl->startRow();
            $tbl->addCell($objH->show(), '', 'center');
            $tbl->addCell($topNav, '','center','right');
            $tbl->endRow();

            $objLayer->str = $objH->show();
            //$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_left';
            $header = $objLayer->show();
            $objLayer->str = $topNav;
            //$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_right';
            $header .= $objLayer->show();

            $objLayer->str = '';
            //$objLayer->border = '; clear:both; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_clear';
            $objLayer-> cssClass = 'clearboth';
            $headShow = $objLayer->show();
            // end header

            $this->setVar('header', $header);
            $this->setVar('headShow', $headShow);

            return 'cms_permissions_add_user_group_tpl.php';
        }








                /**
                 * Method to return the add edit content PERMISSIONS form for CONTENT
                 *
                 * @param string $contentId The id of the content to be edited. Default NULL for adding new content
                 * @return string $middleColumnContent The form used to create and edit a content
                 * @access public
                 * @author Charl Mert <charl.mert@gmail.com>
                 */
        public function getAddEditPermissionsContentForm($contentid = NULL)
        {

            $sectionid = $this->getParam('parent');

            $contentArr = $this->_objContent->getContentPage($contentid);
            $contentName = $contentArr['title'];

            $this->loadClass('form', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');
            $this->loadClass('button', 'htmlelements');

            $objForm = new form('add_content_permissions_frm', 

                $this->uri(array('action' => 'view_permissions_section', 'cid' => $contentid, 'id' => $this->getParam('parent')), 'cmsadmin'));

            $objForm->setDisplayType(3);

            //Start Header
            //initiate objects for header
            $table =  $this->newObject('htmltable', 'htmlelements');
            $objH = $this->newObject('htmlheading', 'htmlelements');
            $link =  $this->newObject('link', 'htmlelements');
            $objIcon =  $this->newObject('geticon', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
            $objRound =$this->newObject('roundcorners','htmlelements');
            $objLayer =$this->newObject('layer','htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');

            $topNav = $this->topNav('addpermissions');

            $tbl = $this->newObject('htmltable', 'htmlelements');
            $tbl->cellpadding = 3;
            $tbl->align = "left";

            //create a heading
            $objH->type = '1';

            //Heading box
            $objIcon->setIcon('permissions_med', 'png', 'icons/cms/');
            //$objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin');
            $objIcon->title = 'Edit Content Permissions';
            //$objH->str =  $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin')."<br/><h3>Sections List</h3>";
            $objH->str =  $objIcon->show().'&nbsp;'.$objIcon->title."<br/><h3>Content Title: $contentName</h3>";
            $tbl->startRow();
            $tbl->addCell($objH->show(), '', 'center');
            $tbl->addCell($topNav, '','center','right');
            $tbl->endRow();

            $objLayer->str = $objH->show();
            //$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
  	    $objLayer->id = 'cms_header_left';
            $header = $objLayer->show();
            $objLayer->str = $topNav;
            //$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
	    $objLayer->id = 'cms_header_left;';
            $header .= $objLayer->show();

            $objLayer->str = '';
            //$objLayer->border = '; clear:both; margin:0px; padding:0px;';
	    $objLayer->cssClass = 'clearboth';
            $headShow = $objLayer->show();
            // end header


            //Setting up the table
            $table = new htmlTable();
            $table->width = "100%";
            $table->cellspacing = "0";
            $table->cellpadding = "0";
            $table->border = "0";
            $table->attributes = "align ='center'";

            $table->startHeaderRow();
            $table->addHeaderCell('Users / Groups');
            $table->addHeaderCell('Read');
            $table->addHeaderCell('Write');
            $table->endHeaderRow();

            //Looping through Users and Groups Assigned to Current content
            //for (){...


            //TODO: Put the data query below into a freakin function charl ;-)
            //Getting origonal members from DB
            //Preparing a list of USER ID's
            $usersList = $this->_objSecurity->getAssignedContentUsers($contentid);
            $usersCount = count($usersList);

            //Preparing a list of GROUP_ID's
            $groupsList = $this->_objSecurity->getAssignedContentGroups($contentid);
            $groupsCount = count($groupsList);
            $globalChkCounter = 0;

            //Displaying Groups
            for ($x = 0; $x < $groupsCount; $x++){
                $memberName = $groupsList[$x]['name'];
                $memberReadAccess = $groupsList[$x]['read_access'];			
                $memberWriteAccess = $groupsList[$x]['write_access'];			

                $oddOrEven = ($rowcount == 0) ? "even" : "odd";

                $canRead = (($memberReadAccess == 1) ? true : false);
                $canWrite = (($memberWriteAccess == 1) ? true : false);


                $chkRead = new checkbox('chk_read-'.$globalChkCounter, 'Read', $canRead);
                $chkWrite = new checkbox('chk_write-'.$globalChkCounter, 'Write', $canWrite);

                $globalChkCounter += 1;

                //Adding Users / Groups
                $table->startRow();
                $table->addCell($memberName);
                $table->addCell($chkRead->show());
                $table->addCell($chkWrite->show());
                $table->endRow();

            }	//End Loop

            //Displaying Users
            for ($x = 0; $x < $usersCount; $x++){
                $memberName = $usersList[$x]['username'];
                $memberReadAccess = $usersList[$x]['read_access'];
                $memberWriteAccess = $usersList[$x]['write_access'];

                $oddOrEven = ($rowcount == 0) ? "even" : "odd";

                $canRead = (($memberReadAccess == 1) ? true : false);
                $canWrite = (($memberWriteAccess == 1) ? true : false);

                $chkRead = new checkbox('chk_read-'.$globalChkCounter, 'Read', $canRead);
                $chkWrite = new checkbox('chk_write-'.$globalChkCounter, 'Write', $canWrite);

                $globalChkCounter += 1;


                //Adding Users / Groups
                $table->startRow();
                $table->addCell($memberName);
                $table->addCell($chkRead->show());
                $table->addCell($chkWrite->show());
                $table->endRow();

            }       //End Loop

            //Add User / Group Link
            $lnkAddUserGroup = new link();
            $lnkAddUserGroup->link = "Add User/Group";
            $lnkAddUserGroup->href = $this->uri(array('action' => 'addpermissions_content_user_group', 'id' => $contentid, 'parent' => $sectionid));

            
            $btnSubmit = new button('save_btn', 'Save');
            $btnSubmit->setToSubmit(); 

            //Setting up the Owner table
            $tblOwner = new htmlTable();
            $tblOwner->width = "100%";
            $tblOwner->cellspacing = "0";
            $tblOwner->cellpadding = "0";
            $tblOwner->border = "0";
            $tblOwner->attributes = "align ='center'";

            //Setting up the Owner table

            $drpOwner = new dropdown('drp_owner');
            $allUsers = $this->_objSecurity->getAllUsers();
            $allUsersCount = count($allUsers);
            //var_dump($allUsers);	
            for ($i = 0; $i < $allUsersCount; $i++){
                //$drpOwner->addOption($allUsers[$i]['userid'], $allUsers[$i]['firstname'].' '.$allUsers[$i]['surname']);
                //echo 'Owner ID : '.$allUsers[$i]['userid'].'<br/>';
                $drpOwner->addOption($allUsers[$i]['userid'], $allUsers[$i]['username']);
            }

            //Getting the current owner of the content
            $content = $this->_objContent->getContentPage($contentid);
            $ownerName = $this->_objUser->userName($content['created_by']);

            $drpOwner->setSelected($content['created_by']);	

            //must set the default owner
            //$drpOwner->setSelected();

            $chkInherit = new checkbox('chk_read', 'Read', false);
            //$chkInherit->setLabel("Force owner on child objects");

            //Owner Select Box
            $tblOwner->startRow();
            $tblOwner->addCell($drpOwner->show());
            $tblOwner->endRow();

            //Check box to force inheritence on child contents and content

            //$tblOwner->startRow();
            //$tblOwner->addCell($chkInherit->show()." Force Ownership on Child contents &amp; Content Items");
            //$tblOwner->endRow();

            $canPublic = (($contentArr['public_access'] == 0)? true : false);
            $chkPublic = new checkbox('chk_public', 'Public', $canPublic);

            $topNav = $this->topNav('addpermissions');

            //$objForm->addToForm("<h1>Edit Content Permissions</h1><h3>Content Title : $contentName</h3><br/>");
            $objForm->addToForm($header.$headShow);
            $objForm->addToForm("<br/><h4>Authorised Members:</h4><br/>");
            $objForm->addToForm($table->show());
            $objForm->addToForm('<br/>');
            $objForm->addToForm($lnkAddUserGroup->show());
            $objForm->addToForm('<br/>');
            $objForm->addToForm($chkPublic->show() . ' Only allow logged in users to access this section.');
            $objForm->addToForm('<br/><input type="hidden" name="chkCount" value="'.$globalChkCounter.'"/>');
            $objForm->addToForm('<br/><input type="hidden" name="id" value="'.$this->getParam('parent').'"/>');
            $objForm->addToForm('<br/><input type="hidden" name="cid" value="'.$contentid.'"/>');
            $objForm->addToForm('<br/><input type="hidden" name="action" value="view_permissions_section"/>');
            $objForm->addToForm("<h4>Owner:</h4>");
            $objForm->addToForm($tblOwner->show());
            $objForm->addToForm('<br/>');
            $objForm->addToForm($btnSubmit->show());

            $display = $objForm->show();
            return $display;
        }


                /**
                 * Method to return the add edit CONTENT permissions USER GROUP form
                 *
                 * @param string $contentId The id of the content to be edited. Default NULL for adding new content
                 * @return string $middleColumnContent The form used to create and edit a content
                 * @access public
                 * @author Charl Mert <charl.mert@gmail.com>
                 */
        public function getAddEditPermissionsContentUserGroupForm($contentid = NULL)
        {
            //echo "$contentid"; exit;

            $sectionid = $this->getParam('parent');	

            $objLanguage = $this->objLanguage;

            $memberList = $this->contentMemberList($contentid);
            //$memberList = array();

            $usersList = $this->contentUsersList($contentid);

            if ( $contentid == NULL) {
                $errMsg = 'unknown content';
                $this->setVar( 'errorMsg', $errMsg );
            } 

            // Members list dropdown
            $this->loadClass('dropdown', 'htmlelements');
            $lstMembers = new dropdown('list2[]');
            //$lstMembers->name = 'list2[]';
            $lstMembers->extra = ' style="width:100pt" MULTIPLE SIZE=10 onDblClick="moveSelectedOptions(this.form[\'list2[]\'],this.form[\'list1[]\'],true)"';
            foreach ( $memberList as $user ) {
                if (($user['firstname'] != '') && ($user['surname'] != '')){
                    $fullName = $user['firstname'] . " " . $user['surname'];
                    $userPKId = $user['id'].'|user';
                } else {
                    $fullName = $user['username'];
                    $userPKId = $user['id'].'|group';
                }
                $lstMembers->addOption( $userPKId, $fullName );
            } 
            // Users list dropdown
            $lstUsers = new dropdown('list1[]');
            $lstUsers->extra = ' style="width:100pt" MULTIPLE SIZE=10 onDblClick="moveSelectedOptions(this.form[\'list1[]\'],this.form[\'list2[]\'],true)"';


            foreach ( $usersList as $user ) {

                if (($user['firstname'] != '') && ($user['surname'] != '')){
                    $fullName = $user['firstname'] . " " . $user['surname'];
                    $userPKId = $user['id'].'|user';
                } else {
                    $fullName = $user['username'];
                    $userPKId = $user['id'].'|group';
                }
                $lstUsers->addOption( $userPKId, $fullName );
            } 

            // Build the nonMember table.
            //$hdrUsers = $objLanguage->languageText('mod_cmsadmin_word_user','cmsadmin');
            $hdrUsers = "User / Group";

            $tblUsers = '<table><tr><th>' . $hdrUsers . '</th></tr><tr><td>' . $lstUsers->show() . '</td></tr></table>'; 

            // Build the Member table.
            //$hdrMemberList = $objLanguage->languageText( 'mod_groupadmin_hdrMemberList','groupadmin' );
            $hdrMemberList = "Members";

            $tblMembers = '<table><tr><th>' . $hdrMemberList . '</th></tr><tr><td>' . $lstMembers->show() . '</td></tr></table>'; 

            // The save button
            $btnSave = $this->newObject( 'button', 'htmlelements' );
            $btnSave->name = 'btnSave';
            $btnSave->cssClass = null;
            $btnSave->value = $objLanguage->languageText( 'word_save' );
            $btnSave->onclick = "selectAllOptions(this.form['list2[]'])";
            $btnSave->setToSubmit(); 

            // The save link button
            $btnSave = $this->newObject( 'button', 'htmlelements' );
            $btnSave->name = 'btnSave';
            $btnSave->cssClass = null;
            $btnSave->value = $objLanguage->languageText( 'word_save' );
            $btnSave->onclick = "selectAllOptions(this.form['list2[]'])";
            $btnSave->setToSubmit(); 

            // Link method
            $lnkSave = "<a href=\"#\" onclick=\"javascript:selectAllOptions(document.frmEdit['list2[]']); document.frmEdit['button'].value='save'; document.frmEdit.submit()\">";
            $lnkSave .= $objLanguage->languageText( 'word_save' ) . "</a>"; 

            // The cancel button
            $btnCancel = $this->newObject( 'button', 'htmlelements' );
            $btnCancel->name = 'btnCancel';
            $btnCancel->value = $objLanguage->languageText( 'word_Cancel' );
            $btnCancel->setToSubmit(); 

            // Link method
            $lnkCancel = "<a href=\"#\" onclick=\"javascript:document.frmEdit['button'].value='cancel'; document.frmEdit.submit()\">";
            $lnkCancel .= $objLanguage->languageText( 'word_cancel' ) . "</a>"; 

            // Form control buttons
            $buttons = array( $lnkSave, $lnkCancel ); 

            // The move selected items right button
            $btnRight = $this->newObject( 'button', 'htmlelements' );
            $btnRight->name = 'right';
            $btnRight->value = htmlspecialchars( '>>' );
            $btnRight->onclick = "moveSelectedOptions(this.form['list1[]'],this.form['list2[]'],true)"; 

            // Link method
            $lnkRight = "<a href=\"#\" onclick=\"javascript:moveSelectedOptions(document.frmEdit['list1[]'],document.frmEdit['list2[]'],true)\">";
            $lnkRight .= htmlspecialchars( '>>' ) . "</a>"; 

            // The move all items right button
            $btnRightAll = $this->newObject( 'button', 'htmlelements' );
            $btnRightAll->name = 'right';
            $btnRightAll->value = htmlspecialchars( 'All >>' );
            $btnRightAll->onclick = "moveAllOptions(this.form['list1[]'],this.form['list2[]'],true)"; 

            // Link method
            $lnkRightAll = "<a href=\"#\" onclick=\"javascript:moveAllOptions(document.frmEdit['list1[]'],document.frmEdit['list2[]'],true)\">";
            $lnkRightAll .= htmlspecialchars( 'All >>' ) . "</a>"; 

            // The move selected items left button
            $btnLeft = $this->newObject( 'button', 'htmlelements' );
            $btnLeft->name = 'left';
            $btnLeft->value = htmlspecialchars( '<<' );
            $btnLeft->onclick = "moveSelectedOptions(this.form['list2[]'],this.form['list1[]'],true)"; 

            // Link method
            $lnkLeft = "<a href=\"#\" onclick=\"javascript:moveSelectedOptions(document.frmEdit['list2[]'],document.frmEdit['list1[]'],true)\">";
            $lnkLeft .= htmlspecialchars( '<<' ) . "</a>"; 

            // The move all items left button
            $btnLeftAll = $this->newObject( 'button', 'htmlelements' );
            $btnLeftAll->name = 'left';
            $btnLeftAll->value = htmlspecialchars( 'All <<' );
            $btnLeftAll->onclick = "moveAllOptions(this.form['list2[]'],this.form['list1[]'],true)"; 

            // Link method
            $lnkLeftAll = "<a href=\"#\" onclick=\"javascript:moveAllOptions(document.frmEdit['list2[]'],document.frmEdit['list1[]'],true)\">";
            $lnkLeftAll .= htmlspecialchars( 'All <<' ) . "</a>"; 

            // The move items (Insert and Remove) buttons
            $btns = array( $lnkRight, $lnkRightAll, $lnkLeft, $lnkLeftAll );
            $tblInsertRemove = '<div>' . implode( '<br /><br />', $btns ) . '</div>'; 

            // Form Layout Elements
            $tblLayout = $this->newObject( 'htmltable', 'htmlelements' );
            $tblLayout->row_attributes = 'align=center';
            $tblLayout->width = '99%';
            $tblLayout->startRow();
            $tblLayout->addCell( $tblUsers, null, null );
            $tblLayout->addCell( $tblInsertRemove, null, null );
            $tblLayout->addCell( $tblMembers, null, null );
            $tblLayout->endRow();

            // Title and Header
            $ttlEditGroup = $objLanguage->languageText( 'mod_groupadmin_ttlEditGroup','groupadmin' );
            $hlpEditGroup = $objLanguage->languageText( 'mod_groupadmin_hlpEditGroup','groupadmin' );
            $hdrEditGroup = $objLanguage->languageText( 'mod_groupadmin_hdrEditGroup','groupadmin' ); 

            // Context Home Icon
            $lblContextHome = $this->objLanguage->languageText( "word_course" ) . ' ' . $this->objLanguage->languageText( "word_home" );
            $icnContextHome = $this->newObject( 'geticon', 'htmlelements' );
            $icnContextHome->setIcon( 'home' );
            $icnContextHome->alt = $lblContextHome;

            $lnkContextHome = $this->newObject( 'link', 'htmlelements' );
            $lnkContextHome->href = $this->uri( array(), 'context' );
            $lnkContextHome->link = $icnContextHome->show() . $lblContextHome;

            $return = $this->getParam( 'return' ) == 'context' ? 'context' : 'main';
            $confirm = $this->getParam( 'confirm' ) ? TRUE : FALSE; 

            // Form Elements
            $frmEdit = $this->newObject( 'form', 'htmlelements' );
            $frmEdit->name = 'frmEdit';
            $frmEdit->displayType = '3';
            $frmEdit->action = $this->uri ( array( 'action' => 'add_content_permissions', 'parent' => $sectionid ) );
            $frmEdit->addToForm( "<div id='blog-content'>" . $tblLayout->show() . "</div>" );
            $frmEdit->addToForm( "<div id='blog-footer'>" . implode( '&#160;', $buttons ) . "</div>" );
            $frmEdit->addToForm( "<input type='hidden' name='id' value='$contentid'>" );
            $frmEdit->addToForm( "<input type='hidden' name='parent' value='".$this->getParam('parent')."'>" );
            $frmEdit->addToForm( "<input type='hidden' name='return' value='$return'>" );
            $frmEdit->addToForm( "<input type='hidden' name='confirm' value='TRUE'>" );
            $frmEdit->addToForm( "<input type='hidden' name='button' value='saved'>" ); 

            // Back link button
            $lnkBack = $this->newObject( 'link', 'htmlelements' );
            $lnkBack->href = $this->uri ( array() );
            $lnkBack->link = $objLanguage->languageText( 'mod_groupadmin_back' ,'groupadmin'); 

            // $lnkBack->cssClass = 'pseudobutton';
            $this->setVar( 'frmEdit', $frmEdit );
            $this->setVar( 'return', $return );
            $this->setVar( 'lnkContextHome', $lnkContextHome );
            $this->setVar( 'lnkBack', $lnkBack );
            $this->setVar( 'ttlEditGroup', $ttlEditGroup );
            $this->setVar( 'fullPath', $fullPath );
            $this->setVar( 'confirm', $confirm );

            //Start Header
            //initiate objects for header
            $table =  $this->newObject('htmltable', 'htmlelements');
            $objH = $this->newObject('htmlheading', 'htmlelements');
            $link =  $this->newObject('link', 'htmlelements');
            $objIcon =  $this->newObject('geticon', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
            $objRound =$this->newObject('roundcorners','htmlelements');
            $objLayer =$this->newObject('layer','htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');

            $topNav = $this->topNav('addpermissions');

            $tbl = $this->newObject('htmltable', 'htmlelements');
            $tbl->cellpadding = 3;
            $tbl->align = "left";


            $contentName = $this->_objContent->getContentPage($contentid);
            $contentName = $contentName['title'];

            //create a heading
            $objH->type = '1';

            //Heading box
            $objIcon->setIcon('permissions_med', 'png', 'icons/cms/');
            //$objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin');
            $objIcon->title = 'Edit Content Permissions';
            //$objH->str =  $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin')."<br/><h3>Sections List</h3>";
            $objH->str =  $objIcon->show().'&nbsp;'.$objIcon->title."<br/><h3>Content Title: $contentName</h3><br/><h2>Add Authorized Members</h2>";
            $tbl->startRow();
            $tbl->addCell($objH->show(), '', 'center');
            $tbl->addCell($topNav, '','center','right');
            $tbl->endRow();

            $objLayer->str = $objH->show();
            //$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_left';
            $header = $objLayer->show();
            $objLayer->str = $topNav;
            //$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_right';
            $header .= $objLayer->show();

            $objLayer->str = '';
            //$objLayer->border = '; clear:both; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_clear';
            $objLayer->cssClass = 'clearboth';
            $headShow = $objLayer->show();
            // end header

            $this->setVar('header', $header);
            $this->setVar('headShow', $headShow);

            return 'cms_permissions_add_user_group_tpl.php';
        }





                /**
                 * Method to get all members of the current SECTION.
                 * 
                 * @access private
                 * @return array   containing the members.
                 */
        function sectionMemberList($sectionid = null)
        {

            $authorizedMembers = $this->_objSecurity->getAuthorizedSectionMembers($sectionid);

            return $authorizedMembers;

            return array( 
                    'user1' => array(
                                    'firstname' => 'Charl', 
                                    'surname' => 'Mert', 
                                    'id' => 'init_1'
                ),
                    'user2' => array(
                                    'firstname' => 'Ayia', 
                                    'surname' => 'Barry', 
                                    'id' => 'init_2'
                )
            );
        } 

                /**
                 * Method to get all users and groups that are NOT authorized for the current section
                 * 
                 * @access private
                 * @return array   of all non members.
                 */
        function sectionUsersList($sectionid = null)
        {

            $unAuthorizedMembers = $this->_objSecurity->getUnAuthorizedSectionMembers($sectionid);
            return $unAuthorizedMembers;

            return array( 
                                                                'user1' => array(
                                                                                'firstname' => 'Truman', 
                                                                                'surname' => 'Show', 
                                                                                'id' => 'init_3'
                ),
                                                                'user2' => array(
                                                                                'firstname' => 'Rick', 
                                                                                'surname' => 'Ross', 
                                                                                'id' => 'init_4'
                )
            );
        }


                /**
                 * Method to get all members of the current SECTION.
                 * 
                 * @access private
                 * @return array   containing the members.
                 */
        function contentMemberList($contentid = null)
        {

            $authorizedMembers = $this->_objSecurity->getAuthorizedContentMembers($contentid);

            return $authorizedMembers;

        } 

                /**
                 * Method to get all users and groups that are NOT authorized for the current content
                 * 
                 * @access private
                 * @return array   of all non members.
                 */
        function contentUsersList($contentid = null)
        {

            $unAuthorizedMembers = $this->_objSecurity->getUnAuthorizedContentMembers($contentid);
            return $unAuthorizedMembers;

        }


                /**
                 * Method to return the EDIT LINK
                 *
                 * @param string $menuId The id of the MENU ITEM to be edited. Default Null to EDIT THE MAIN MENU
                 * @access public
                 * @return string html icon
                 * @author Charl Mert
                 */
        public function getEditLink($params = array(), $tooltip = '')
        {
            $icon = $this->getObject('geticon','htmlelements');
            $icon->setIcon('edit');
            $icon->alt = $tooltip;
            $link = $this->getObject('link','htmlelements');
            $link->link($this->uri($params), 'cmsadmin');
            $link->link = $icon->show();
            $ret = " ".$link->show();
            return $ret;
        }




                /**
                 * Method to return the DELETE LINK
                 *
                 * @param string $menuId The id of the MENU ITEM to be edited. Default Null to EDIT THE MAIN MENU
                 * @access public
                 * @return string html icon
                 * @author Charl Mert
                 */
        public function getDeleteLink($menuId, $params = array(), $tooltip = '')
        {

            $this->objIcon = &$this->getObject('geticon', 'htmlelements');
            $delIcon = $this->objIcon->getDeleteIconWithConfirm($menuId, $params, 'cmsadmin');
        
            return $delIcon;
        }


       /**
        * Method to return the EDIT MENU form
        *
        * @param string $menuId The id of the MENU ITEM to be edited. Default Null to EDIT THE MAIN MENU
        * @access public
        * @return string html form used to create and edit a menu
        * @author Charl Mert
        */
        public function getEditMenuForm($headerMessage, $menuId = NULL, $menuType = NULL)
        {

            $isSub = $this->getParam('sub');

            //Getting Default Menu
            if ($isSub != '1'){
                $objForm = new form('addmenu', $this->uri(array('action' => 'addmenu', 'id' => $menuId), 'cmsadmin'));
            }else {
                $objForm = new form('addmenu', $this->uri(array('action' => 'editmenu', 'id' => $menuId, 'menutype' => $menuType), 'cmsadmin'));
            }
                
            $h3 = $this->newObject('htmlheading', 'htmlelements');

            //Edit Existing Menu Item
            if ($menuId != ''){
                $arrContent = $this->_objPageMenu->getMenuRow($menuId);
                if (isset($arrContent[0]['menukey'])){
                    $titleInputValue = $arrContent[0]['menukey'];
                }
            }

            //Getting Default Menu
            if ($isSub != '1'){
                $arrContent = $this->_objPageMenu->getMenuRowByKey('default');
            }
        
            $table = new htmlTable();
            $table->width = "100%";
            $table->cellspacing = "0";
            $table->cellpadding = "0";
            $table->border = "0";
            $table->attributes = "align ='center'";


            //Only showing the Sub Menu Link if the default menu exists
            if ($this->_objPageMenu->hasDefaultMenu()){

                if ($isSub != '1'){
                    $table->startRow();
                    $table->addCell('<br/>');
                    $table->endRow();

                    $h3->str = 'Edit Sub Menu\'s';
                    $h3->type = 3;

                    $table->startRow();
                    $table->addCell($h3->show(), null, 'top', null, null, 'colspan="2"');
                    $table->endRow();



                    $table_list = new htmlTable();
                    $table_list->width = "300px";
                    $table_list->cellspacing = "0";
                    $table_list->cellpadding = "0";
                    $table_list->border = "0";
                    $table_list->attributes = "align ='center'";

                    //Displaying The List Of Menus
                    $menuRows = $this->_objPageMenu->getAll();
                    if (count($menuRows) > 0){
                        foreach ($menuRows as $menu){
                            if ($menu['menukey'] != 'default'){
                                $editLink = $this->getEditLink(array('action' => 'editmenu', 'menutype' => 'page', 'sub' => '1', 'id' => $menu['id']), 'Edit Menu Item');	
                                $deleteLink = $this->getDeleteLink($menu['id'], array('action' => 'deletemenu', 'menutype' => 'page', 'id' => $menu['id']), 'Edit Menu Item');	
                                $table_list->startRow();
                                $table_list->addCell($menu['menukey'], 150);
                                $table_list->addCell($editLink.' '.$deleteLink, 150);
                                $table_list->endRow();
                            }
                        }

                        $table->startRow();
                        $table->addCell($table_list->show(), 150);
                        $table->endRow();

                    }


                    $subMenuLink = '<a href="?module=cmsadmin&action=editmenu&menutype=page&sub=1"><b>Add a Sub Menu</b></a>';

                    if ($isSub != '1'){
                        $table->startRow();
                        $table->addCell($subMenuLink, null, 'top', null, null, 'colspan="2"');
                        $table->endRow();


                    }
                }

            }

            if ($isSub == '1'){

                // Title Input
                $titleInput = new textinput ('menukey', $titleInputValue);
                $titleInput->cssId = 'input_title'; 
                $titleInput->extra = ' style="width: 25%"';

                $table->startRow();
                $table->addCell('Menu Key', 150);
                $table->addCell($titleInput->show());
                $table->endRow();
            }

                                /*
                                   $lbNo = $this->objLanguage->languageText('word_no');
                                   $lbYes = $this->objLanguage->languageText('word_yes');
                                   $objRadio = new radio('hide_title');
                                   $objRadio->addOption('1', '&nbsp;'.$lbYes);
                                   $objRadio->addOption('0', '&nbsp;'.$lbNo);
                                   $objRadio->setSelected($hide_title);
                                   $objRadio->setBreakSpace('&nbsp;&nbsp;');

                                   $table->startRow();
                                   $table->addCell($this->objLanguage->languageText('phrase_hidetitle').': &nbsp; ');
                                   $table->addCell($objRadio->show());
                                //$table->addCell($published->show());
                                $table->endRow();
                                 */

            //Adding the FCK_EDITOR
            if (isset($arrContent[0]['body'])) {
                $bodyInputValue = stripslashes($arrContent[0]['body']);
            }else{
                $bodyInputValue = null;
            }


            $bodyInput = $this->newObject('htmlarea', 'htmlelements');
            $bodyInput->init('body', $bodyInputValue);
            $bodyInput->setContent($bodyInputValue);
            $bodyInput->setDefaultToolBarSet();
            $bodyInput->height = '400px';

            //echo $bodyInput->show(); exit;

            $h3->str = $headerMessage;
            $h3->type = 3;

            $table->startRow();
            $table->addCell('<br/>');
            $table->endRow();

            $table->startRow();
            $table->addCell($h3->show(), null, 'top', null, null, 'colspan="2"');
            $table->endRow();

            $table->startRow();
            $table->addCell('', null, 'top', null, null, 'style="padding-bottom:10px"');
            $table->endRow();

            $table->startRow();
            $table->addCell($bodyInput->show(), null, 'top', null, null, 'colspan="2"');
            $table->endRow();

            $table->startRow();
            $table->addCell('', null, 'top', null, null, 'style="padding-bottom:10px"');
            $table->endRow();


            if ($isSub != '1'){
                $table->startRow();
                $table->addCell('<input type="hidden" name="menukey" value="default"/>', null, 'top', null, null, 'colspan="2"');
                $table->endRow();
            }

            $table->startRow();
            $table->addCell('<input type="submit" value=" Save " name="submitter" id="submitter">', null, 'top', null, 'cmsvspacer');
            $table->addCell('<input type="hidden" value="save" name="save">', null, 'top', null, 'cmsvspacer');
            $table->addCell('<input type="hidden" value="0" name="must_apply" id="must_apply"><input type="hidden" value="0" name="must_preview" id="must_preview">', null, 'top', null, 'cmsvspacer');
            $table->endRow();

        
            $objForm->addToForm($table->show());

            //Add validation for title            
                                /*
                                   $errTitle = $this->objLanguage->languageText('mod_cmsadmin_entertitle', 'cmsadmin');
                                   $objForm->addRule('title', $errTitle, 'required');
                                   $objForm->addToForm($table->show());
                                //add action
                                $objForm->addToForm($txt_action);
                                 */

            $display = $objForm->show(); 	
            return $display;

        } 










        /**
        * Method to return the add edit content form
        *
        * @param string $contentId The id of the content to be edited. Default NULL for adding new section
        * @access public
        * @return string $middleColumnContent The form used to create and edit a page
        * @author Warren Windvogel, Charl Mert
        */
        public function getAddEditContentForm($contentId = NULL, $section = NULL, $fromModule = NULL, $fromAction = NULL, $s_param = NULL)
        {
            $h3 = $this->newObject('htmlheading', 'htmlelements');

            // Determine whether to show the toggle or not
            if ($section == NULL) {
                $toggleShowHideIntro = TRUE;
            } else {
                $sectionInfo = $this->_objSections->getSection($section);

                if ($sectionInfo == FALSE) {
                    $toggleShowHideIntro = TRUE;
                } else {
                    if ($sectionInfo['layout'] == 'summaries') {
                        $toggleShowHideIntro = FALSE;
                    } else {
                        $toggleShowHideIntro = TRUE;
                    }
                }
            }

            $published = new checkbox('published');
            $frontPage = new checkbox('frontpage');
            $frontPage->value = 1;

            $objOrdering = new textinput();
            $is_front = FALSE;
            $show_content = '0';
            if ($contentId == NULL) {
                $action = 'createcontent';
                $editmode = FALSE;
                $titleInputValue = '';
                $bodyInputValue = '';
                $introInputValue = '';
                $published->setChecked(TRUE);
                $visible = TRUE;
                $hide_title = '0';
                $contentId = '';
                $arrContent = null;
                if ($this->getParam('frontpage') == 'true') {
                    $frontPage->setChecked(TRUE);
                    $is_front = TRUE;
                }
            } else {
                $action = 'editcontent';
                $editmode = TRUE;
                $arrContent = $this->_objContent->getContentPage($contentId);
                $titleInputValue = $arrContent['title'];

                if (!isset($is_front)) {
                    $is_front = false;
                }
                if ($this->getParam('frontpage') == 'true') {
                    $frontPage->setChecked(TRUE);
                    $is_front = TRUE;
                }
    
                $introInputValue = stripslashes($arrContent['introtext']);
                $bodyInputValue = stripslashes($arrContent['body']);

            }

            //setup form
            $frontMan = $this->getParam('frontmanage', FALSE);
            $objForm = new form('addfrm', $this->uri(array('action' => $action, 'id' => $contentId, 'frontman' => $frontMan), 'cmsadmin'));
            $objForm->setDisplayType(3);

            if ($editmode) {
                //Set ordering as hidden field
                if (trim($section) == '') {
                    $section = $arrContent['sectionid'];
                }

                $sections = $this->getSectionList($section, FALSE);
            } else {
                if (!isset($section)) {
                    $section = NULL;
                }
                $sections = $this->getSectionList($section, FALSE);
            }
		

            $tableContainer = new htmlTable();
            $tableContainer->width = "100%";
            $tableContainer->cellspacing = "0";
            $tableContainer->cellpadding = "0";
            $tableContainer->border = "0";
            $tableContainer->attributes = "align ='center'";

            $table = new htmlTable();
            $table->width = "100%";
            $table->cellspacing = "0";
            $table->cellpadding = "0";
            $table->border = "0";
            $table->attributes = "align ='center'";
            $this->loadClass('textinput', 'htmlelements');
            // Title Input
            $titleInput = new textinput ('title', $titleInputValue);
            $titleInput->cssId = 'input_title'; 

            $h3->str = $this->objLanguage->languageText('mod_cmsadmin_maintext', 'cmsadmin');
            $h3->type = 3;

            $table->startRow();
            $table->addCell($h3->show(), null, 'top', null, null, 'colspan="2"');
            $table->endRow();

            $table->startRow();
            $table->addCell('', null, 'top', null, 'cmsvspacer');
            $table->endRow();

            $tableInput = new htmltable();
            $tableInput->cellspacing = "0";
            $tableInput->cellpadding = "0";
            $tableInput->border = "0";

            $tableInput->startRow();
            $tableInput->addCell($this->objLanguage->languageText('word_title'), '30px', '', '', '', '');
            $tableInput->addCell($titleInput->show(), '', '', '', '', '');
            $tableInput->addCell($this->objLanguage->languageText('word_section'),'50px', '', '', '', '');
            $tableInput->addCell($sections->show(),'120px', '', '', '', '');
            $tableInput->endRow();


            $table->startRow();
            $table->addCell($tableInput->show());

            $table->endRow();

            //Frontpage Security
            if ($this->_objUserPerm->canAddToFrontPage()) {

                // Introduction Area
                $introInput = $this->newObject('htmlarea', 'htmlelements');
                $introInput->init('intro', $introInputValue);
                $introInput->setContent($introInputValue);
                $introInput->setBasicToolBar();
                $introInput->height = '200px';
                $introInput->width = '100%';

                $h3->str = $this->objLanguage->languageText('word_introduction')/*.' ('.$this->objLanguage->languageText('word_required').')'*/;
                $h3->type = 3;

                //add hidden text input
                $table->row_attributes = '';
                $table->startRow();
                $table->addCell('<div id="introdiv"><br />'.$h3->show().$introInput->show().'</div>','','left','left', null, 'colspan="2"');
                $table->endRow();
            }

            //Adding the RICH TEXT EDITOR
            if ($editmode) {

                if (isset($arrContent['body'])) {
                    $bodyInputValue = stripslashes($arrContent['body']);
                }else{
                    $bodyInputValue = null;
                }

            }

            $bodyInput = $this->newObject('htmlarea', 'htmlelements');
            $bodyInput->init('body', $bodyInputValue);
            $bodyInput->setContent($bodyInputValue);
            $bodyInput->setCMSToolBar();
            $bodyInput->loadCMSTemplates();
            $bodyInput->id = 'input_body';

            $bodyInput->height = '400px';

            $h3->str = $this->objLanguage->languageText('word_body').' ('.$this->objLanguage->languageText('word_required').')';
            $h3->type = 3;

            //add hidden text input
            $table->row_attributes = '';
            $table->startRow();
			
			//FCKEditor
            $table->addCell('<div id="bodydiv"><br />'.$h3->show().$bodyInput->show().'</div>','','left','left', null, 'colspan="2"');
			
            $table->endRow();

            //add the main body
            $table2 = new htmltable();
            $table2->width = "100%";

            $h3->str = $this->objLanguage->languageText('mod_cmsadmin_contentparams','cmsadmin');
            $h3->type = 3;

            $table2->startRow();
            $table2->addCell($h3->show(), null, 'top', null, null, 'colspan="2"');
            $table2->endRow();
            $table2->startRow();

            if (!$editmode) {
                $table2->addCell($this->getConfigTabs());
            }else{
                $table2->addCell($this->getConfigTabs($arrContent));
            }
            $table2->endRow();
            // Content Area

            //Header for main body
            //Pass action
            $txt_action = new textinput('action',$action,'hidden');
            $table->startRow();
            if ($fromModule) { 
                $mod = new textinput('frommodule',$fromModule,'hidden');
                $act = new textinput('fromaction',$fromAction,'hidden');
                $param = new textinput('s_param',$s_param,'hidden');
                $table->addCell($mod->show().$act->show().$param->show()); 
            }
            $table->endRow();         		

            //Adding the 250px hieght buffer to make sure the jQuery functions won't overlap existing skins
            if ($this->_objUserPerm->canAddToFrontPage() && !$is_front) {
                $layer = new layer();
                $layer->height = '250px';
                
                $table->startRow();
                $table->addCell($layer->show());
                $table->endRow();
            }
    
	        $pageParams = new layer();
	        $pageParams->id = 'AddContentPageParams';
	        $pageParams->str = $table2->show();

	        $pageContent = new layer();
	        $pageContent->id = 'AddContentPageContent';
	        $pageContent->str = $table->show();
    
            $wrapLayer = new layer();
            $wrapLayer->id = "wrapAddContent";

            $leftLayer = new layer();
            $leftLayer->id = "leftAddContent";
            $leftLayer->str = $table->show();

            $rightLayer = new layer();
            $rightLayer->id = "rightAddContent";
            $rightLayer->str = $pageParams->show();

            $wrapLayer->str = $leftLayer->show().$rightLayer->show();

	        $tableContainer->startRow();
	        $tableContainer->addCell($pageContent->show(),'', 'top', '', 'AddContentLeft');
	        $tableContainer->addCell($pageParams->show(),'', 'top', '', 'AddContentRight');
	        $tableContainer->endRow();
            
	        //Add validation for title            
            $errTitle = $this->objLanguage->languageText('mod_cmsadmin_entertitle', 'cmsadmin');
            $objForm->addRule('title', $errTitle, 'required');
			$objForm->addToForm($tableContainer->show() /*$wrapLayer->show()*/);
            $objForm->addToForm('<input type="hidden" name="must_apply" id="must_apply" value="0">');

            //add action
            $objForm->addToForm($txt_action);

            $display = $objForm->show(); 	
            return $display;
        }

        /**
        * Method to return the add edit template form
        *
        * @param string $templateId The id of the template item to be edited. Default NULL for adding new template
        * @access public
        * @return string $middleColumnTemplate The form used to create and edit a page
        * @author Charl Mert
        */
        public function getAddEditTemplateForm($templateId = NULL, $section = NULL, $fromModule = NULL, $fromAction = NULL, $s_param = NULL)
        {

            $h3 = $this->newObject('htmlheading', 'htmlelements');
            $objIcon = $this->getObject('geticon','htmlelements');

            $published = new checkbox('published');

            $objOrdering = new textinput();
            //$objCCLicence = $this->newObject('licensechooser', 'creativecommons');
            $is_front = FALSE;
            $show_Template = '0';
            if ($templateId == NULL) {
                $action = 'createtemplate';
                $editmode = FALSE;
                $titleInputValue = '';
                $imageInputValue = $this->_objConfig->getskinRoot().'_common/icons/cms/template_item.gif';
                $descInputValue = '';
                $bodyInputValue = '';
                $published->setChecked(TRUE);
                $templateId = '';
                $arrTemplate = null;
            } else {
                $action = 'edittemplate';
                $editmode = TRUE;
                $arrTemplate = $this->_objTemplate->getTemplatePage($templateId);
                $titleInputValue = $arrTemplate['title'];
                $imageInputValue = $arrTemplate['image'];
                $descInputValue = $arrTemplate['description'];
                $bodyInputValue = stripslashes($arrTemplate['body']);
            }

            //setup form
            $objForm = new form('addfrm', $this->uri(array('action' => $action, 'id' => $templateId), 'cmsadmin'));
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
            $titleInput->cssId = 'input_template_title';

            // Description Input
            $descInput = new textinput ('description', $descInputValue);
            $descInput->cssId = 'input_description'; 

            $h3->str = $this->objLanguage->languageText('mod_cmsadmin_templatetext', 'cmsadmin');
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

            $table_input->startRow();
            $table_input->addCell($this->objLanguage->languageText('word_description').': ');
            $table_input->addCell($descInput->show());
            $table_input->endRow();

$script = <<<OPENFILEMANAGER
        <script type="text/javascript">
            function openFileManager(){
                //alert('Good old quicky');
                //window.open('?module=filemanager&mode=fckimage&restriction=jpg_gif_png_jpeg&context=no','selectimage','width=600,height=600,scrollbars=1,resizable=1') //TEMPLATE_IMAGE
            }
        </script>
OPENFILEMANAGER;
$this->appendArrayVar('headerParams', $script);

            if ($imageInputValue == '') {
                $imageInputValue = $this->_objConfig->getskinRoot().'_common/icons/cms/template_item.gif';
            }

            //Disabling Image Choice for now: 
            $imageInputValue = 'template1.gif';//TEMPLATE_IMAGE_REMOVE

            $imageInput = new hiddeninput('imagepath', $imageInputValue);
            $imageInput->extra = ' id="template_thumb_input" ';

            $imageInputValue = $this->_objConfig->getskinRoot().'_common/icons/cms/template_item.gif';//TEMPLATE_IMAGE_REMOVE

            $templateThumb = '<img src="'.$imageInputValue.'" id="template_thumb" title="'.$this->objLanguage->languageText('mod_cmsadmin_template_change_image','cmsadmin').'" width="100px" height="70px"/>';
            //$objIcon->setIcon('template_item','gif', 'icons/cms/');
            //$objIcon->extra = ' id="template_thumb" width="100px" height="70px" ';
            //$objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_template_change_image','cmsadmin');
            $objLayer = new layer();
            $objLayer->id = 'change_thumb';
            //$objLayer->str = $objIcon->show().$imageInput->show();
            $objLayer->str = $templateThumb.$imageInput->show();
            $objLayer->onclick = "openFileManager();";

            $table->startRow();
            $table->addCell($table_input->show());
            $table->addCell($objLayer->show());
            $table->endRow();

            $h3->str = $this->objLanguage->languageText('word_introduction').' ('.$this->objLanguage->languageText('word_required').')';
            $h3->type = 3;

            //add hidden text input
            $table->row_attributes = '';

            //Adding the FCK_EDITOR

            if ($editmode) {

                if (isset($arrTemplate['body'])) {
                    $bodyInputValue = stripslashes($arrTemplate['body']);
                }else{
                    $bodyInputValue = null;
                }

            }

            $bodyInput = $this->newObject('htmlarea', 'htmlelements');
            $bodyInput->init('body', $bodyInputValue);
            $bodyInput->setContent($bodyInputValue);
            $bodyInput->setCMSToolBar();
            $bodyInput->loadCMSTemplates();
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

            $h3->str = $this->objLanguage->languageText('mod_cmsadmin_templateparams','cmsadmin');
            $h3->type = 3;

            $table2->startRow();
            $table2->addCell($h3->show(), null, 'top', null, null, 'colspan="2"');
            $table2->endRow();

            $table2->startRow();
            $table2->addCell('', null, 'top', null, null, 'style="padding-bottom:10px"');
            $table2->endRow();

            $table2->startRow();

            if (!$editmode) {
                $table2->addCell($this->getConfigTemplateTabs());
            }else{
                $table2->addCell($this->getConfigTemplateTabs($arrTemplate));
            }
            $table2->endRow();
            // Template Area

            //Header for main body
            //$h3->str = $this->objLanguage->languageText('mod_cmsadmin_maintext', 'cmsadmin');
            //Pass action
            $id = $this->getParam('id', '');
            $hiddenId = new hiddeninput('id', $id);

            $txt_action = new textinput('action',$action,'hidden');
            $table->startRow();
            //$table->addCell($h3->show(),null,'center','left');
            //$table->addCell($table2->show(),null,'top','left', null, 'colspan="2"');
            if ($fromModule) { 
                $mod = new textinput('frommodule',$fromModule,'hidden');
                $act = new textinput('fromaction',$fromAction,'hidden');
                $param = new textinput('s_param',$s_param,'hidden');
                $table->addCell($mod->show().$act->show().$param->show()); 
            }
            //$table->addCell(,null,'bottom','center');
            $table->endRow();               
    
            $pageParams = new layer();
            $pageParams->id = 'AddTemplatePageParams';
            $pageParams->str = $table2->show();
    
            $tableContainer->startRow();
            $tableContainer->addCell($table->show(),'', 'top', '', 'AddContentLeft');
            $tableContainer->addCell($pageParams->show(),'', 'top', '', 'AddContentRight');
            $tableContainer->endRow();

            //Add validation for title            
            $errTitle = $this->objLanguage->languageText('mod_cmsadmin_entertitle', 'cmsadmin');
            $objForm->addRule('template_title', $errTitle, 'required');
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
                 * Method to show  the body of a pages
                 *
                 * @access public
                 * @return string The page content to be displayed
                 */
        public function showBody($contentId, $menuNodeId, $edit = FALSE, $contentedit=TRUE)
        {
            if ($edit) {
                return $this->editPage($contentId, $menuNodeId);
            }
            $page = $this->_objContent->getContentPage($contentId);

            if (count($page) > 0) {
                //Create heading
                $objHeader = $this->newObject('htmlheading', 'htmlelements');
                $objHeader->type = '3';
                $objHeader->str = $page['title'];
                $strBody = $objHeader->show();
                $strBody .= stripslashes($page['body']).'<br />';
                $strBody .= '<span class="warning">'.$this->_objUser->fullname($page['created_by']).'</span><br />';
                $strBody .= '<span class="warning">'.$page['modified'].'</span>';

                if (($this->_objUser->isAdmin()) || ($this->_objUser->userId() == $page['created_by']))
                {
                    if($contentedit)
                    {
                        $link = &new Link($this->uri(array('pageid'=>$menuNodeId, 'edit' => 'true'),'cmsadmin'));
                        $link->link = 'Edit page';
                        //$strBody .= $link->show();
                    }
                }
            } else {
                $strBody = '';   //Will change this later to correct language element
            }
            return $strBody;
        }

       /**
        * Method to output a rss feeds box
        *
        * @param string $url
        * @param string $name
        * @return string
        */
        public function rssBox($url, $name)
        {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $objRss = $this->getObject('rssreader', 'feed');
            $objRss->parseRss($url);
            $head = $this->objLanguage->languageText("mod_cms_word_headlinesfrom", "cmsadmin");
            $head .= " " . $name;
            $content = "<ul>\n";
            foreach ($objRss->getRssItems() as $item)
            {
                if(!isset($item['link']))
                {
                    $item['link'] = NULL;
                }
                @$content .= "<li><a href=\"" . htmlentities($item['link']) . "\">" . htmlentities($item['title']) . "</a></li>\n";
            }
            $content .=  "</ul>\n";
            return $objFeatureBox->show($head, $content);
        }

        public function rssRefresh($rssurl, $name, $feedid)
        {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $objRss = $this->getObject('rssreader', 'feed');
            $this->objConfig = $this->getObject('altconfig', 'config');

            //get the proxy info if set
            $objProxy = $this->getObject('proxyparser', 'utilities');
            $proxyArr = $objProxy->getProxy();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $rssurl);
            //curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if(!empty($proxyArr) && $proxyArr['proxy_protocol'] != '')
            {
                curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'].":".$proxyArr['proxy_port']);
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'].":".$proxyArr['proxy_pass']);
            }
            $rsscache = curl_exec($ch);
            curl_close($ch);
            //var_dump($rsscache);
            //put in a timestamp
            $addtime = time();
            $addarr = array('url' => $rssurl, 'rsstime' => $addtime);

            //write the file down for caching
            $path = $this->objConfig->getContentBasePath() . "/cms/rsscache/";
            $rsstime = time();
            if(!file_exists($path))
            {

                mkdir($path);
                chmod($path, 0777);
                $filename = $path . $this->_objUser->userId() . "_" . $rsstime . ".xml";
                if(!file_exists($filename))
                {
                    touch($filename);

                }
                $handle = fopen($filename, 'wb');
                fwrite($handle, $rsscache);
            }
            else {
                $filename = $path . $this->_objUser->userId() . "_" . $rsstime . ".xml";
                $handle = fopen($filename, 'wb');
                fwrite($handle, $rsscache);
            }
            //update the db
            $addarr = array('url' => htmlentities($rssurl), 'rsscache' => $filename, 'rsstime' => $addtime);
            //print_r($addarr);
            $this->objDbBlog->updateRss($addarr, $feedid);

            $objRss->parseRss($rsscache);
            $head = $this->objLanguage->languageText("mod_cms_word_headlinesfrom", "cmsadmin");
            $head .= " " . $name;
            $content = "<ul>\n";
            foreach ($objRss->getRssItems() as $item)
            {
                if(!isset($item['link']))
                {
                    $item['link'] = NULL;
                }
                @$content .= "<li><a href=\"" . $item['link'] . "\">" . $item['title'] . "</a></li>\n";
            }
            $content .=  "</ul>\n";
            return $objFeatureBox->show($head, $content);

        }

        public function rssEditor($featurebox = FALSE, $rdata = NULL)
        {
            //initiate objects
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');

            $table =$this->newObject('htmltable', 'htmlelements');
            $objIcon =  $this->newObject('geticon', 'htmlelements');
            $tbl = $this->newObject('htmltable', 'htmlelements');
            $h3 =$this->newObject('htmlheading', 'htmlelements');
            $button =$this->newObject('button', 'htmlelements');
            $objLayer =$this->newObject('layer', 'htmlelements');

            $this->loadClass('href', 'htmlelements');
            $this->loadClass('label', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('textarea', 'htmlelements');

            if($rdata == NULL)
            {
                $rssform = new form('addrss', $this->uri(array(
                                                                                                                'action' => 'addrss'
                        )));
            }
            else {
                $rdata = $rdata[0];
                $rssform = new form('addrss', $this->uri(array(
                                                                                                                'action' => 'addrss', 'mode' => 'edit', 'id' => $rdata['id']
                        )));
            }
            //add rules
            //$rssform->addRule('rssurl', $this->objLanguage->languageText("mod_cms_phrase_rssurlreq", "cmsadmin") , 'required');
            //$rssform->addRule('name', $this->objLanguage->languageText("mod_cms_phrase_rssnamereq", "cmsadmin") , 'required');
            //start a fieldset
            $rssfieldset = $this->getObject('fieldset', 'htmlelements');
            $rssadd = $this->newObject('htmltable', 'htmlelements');
            $rssadd->cellpadding = 3;

            //url textfield
            $rssadd->startRow();
            $rssurllabel = new label($this->objLanguage->languageText('mod_cms_rssurl', 'cmsadmin') .':', 'input_rssuser');
            $rssurl = new textinput('rssurl');
            if(isset($rdata['url']))
            {
                $rssurl->setValue($rdata['url']);
                // $rssurl->setValue('url');

            }
            $rssadd->addCell($rssurllabel->show());
            $rssadd->addCell($rssurl->show());
            $rssadd->endRow();

            //name
            $rssadd->startRow();
            $rssnamelabel = new label($this->objLanguage->languageText('mod_cms_rssname', 'cmsadmin') .':', 'input_rssname');
            $rssname = new textinput('name');
            if(isset($rdata['name']))
            {
                $rssname->setValue($rdata['name']);
            }
            $rssadd->addCell($rssnamelabel->show());
            $rssadd->addCell($rssname->show());
            $rssadd->endRow();

            //description
            $rssadd->startRow();
            $rssdesclabel = new label($this->objLanguage->languageText('mod_cms_rssdesc', 'cmsadmin') .':', 'input_rssname');
            $rssdesc = new textarea('description');
            if(isset($rdata['description']))
            {
                //var_dump($rdata['description']);
                $rssdesc->setValue($rdata['description']);
            }
            $rssadd->addCell($rssdesclabel->show());
            $rssadd->addCell($rssdesc->show());
            $rssadd->endRow();

            //end off the form and add the buttons
            $this->objRssButton = new button($this->objLanguage->languageText('word_save', 'system'));
            $this->objRssButton->setValue($this->objLanguage->languageText('word_save', 'system'));
            $this->objRssButton->setToSubmit();
            $rssfieldset->addContent($rssadd->show());
            $rssform->addToForm($rssfieldset->show());
            $rssform->addToForm($this->objRssButton->show());
            $rssform = $rssform->show();

            //ok now the table with the edit/delete for each rss feed
            $efeeds = $this->objRss->getUserRss($this->_objUser->userId());
            $ftable = $this->newObject('htmltable', 'htmlelements');
            $ftable->cellpadding = 3;
            //$ftable->border = 1;
            //set up the header row
            $ftable->startHeaderRow();
            $ftable->addHeaderCell($this->objLanguage->languageText("mod_cms_fhead_name", "cmsadmin"));
            $ftable->addHeaderCell($this->objLanguage->languageText("mod_cms_fhead_description", "cmsadmin"));
            $ftable->addHeaderCell('');
            $ftable->endHeaderRow();

            //set up the rows and display
            if (!empty($efeeds)) {
                foreach($efeeds as $rows) {
                    $ftable->startRow();
                    $feedlink = new href($rows['url'], $rows['name']);
                    $ftable->addCell($feedlink->show());
                    //$ftable->addCell(htmlentities($rows['name']));
                    $ftable->addCell(($rows['description']));
                    $this->objIcon = &$this->getObject('geticon', 'htmlelements');
                    $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                                                                                                                                'action' => 'rssedit',
                                                                                                                                'mode' => 'edit',
                                                                                                                                'id' => $rows['id'],
                                //'url' => $rows['url'],
                                //'description' => $rows['description'],
                                                                                                                                'module' => 'cmsadmin'
                            )));
                    $delIcon = $this->objIcon->getDeleteIconWithConfirm($rows['id'], array(
                                                                                                                'module' => 'cmsadmin',
                                                                                                                'action' => 'deleterss',
                                                                                                                'id' => $rows['id']
                        ) , 'cmsadmin');
                    $ftable->addCell($edIcon.$delIcon);
                    $ftable->endRow();
                }
                //$ftable = $ftable->show();
            }

            if (!isset($objBlocksLinkDisplay)) {
                $objBlocksLinkDisplay = '';
            }   

            $tbl->cellpadding = 3;
            $tbl->align = "left";
            $objIcon->setIcon('rss_small', 'png', 'icons/cms/');
            $objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_addnewrss', 'cmsadmin');
            $h3->str = $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_addrss', 'cmsadmin').$objBlocksLinkDisplay;

            //Heading box
            $topNav = $this->topNav('createfeed');

            $objLayer->str = $h3->show();
            //$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_left';
            $header = $objLayer->show();

            $objLayer->str = $topNav;
            //$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_right';
            $header .= $objLayer->show();

            $objLayer->str = '';
            //$objLayer->border = '; clear:both; margin:0px; padding:0px;';
            $objLayer->id = 'cms_header_clear';
            $objLayer->cssClass = 'clearboth';
            $headShow = $objLayer->show();
            
            $objLayer->str = '&nbsp;';
            $objLayer->id = 'cmsvspacer';
            $vspacer = $objLayer->show();

            return $header . $headShow . $vspacer . $rssform . $ftable->show();

        }
                /**
                 * Method to show  the edit page screen
                 *
                 * @access public
                 * @return string The page content to be displayed
                 */
        public function editPage($contentId, $menuNodeId)
        {
            $this->loadClass('textinput','htmlelements');
            $this->loadClass('hiddeninput','htmlelements');

            $page = $this->objContent->getContentPage($contentId);

            //initiate objects
            $table = & $this->newObject('htmltable', 'htmlelements');
            $objForm = new form('editfrm', $this->uri(array('action' => 'save', 'id' => $contentId, 'pageid'=>$menuNodeId)));
            $objForm->setDisplayType(3);


            // Title Input
            $titleInput = new textinput ('title');
            $titleInput->extra = ' style="width: 100%"';

            // Content Area
            $bodyInput = $this->newObject('htmlarea', 'htmlelements');
            $bodyInput->name = 'body';
            $bodyInput->height = '400px';
            $bodyInput->width = '100%';

            // Submit Button
            $button = new button('submitform', $this->objLanguage->languageText('word_save'));
            $button->setToSubmit();

            //Extra fields cms needs which we do not want to edit or even care about for the portal
            $publishedInput = new hiddeninput('published', $page['published']);
            $accessInput = new hiddeninput('access', $page['access']);
            $orderingInput = new hiddeninput('ordering', $page['ordering']);
            $parentInput = new hiddeninput('parent', $page['sectionid']);
            $creativeCommonsInput = new hiddeninput('creativecommons', $page['post_lic']);
            $introInput = new hiddeninput('intro', $page['introtext']);

            $titleInput->value = $page['title'];

            $bodyInput->setContent((stripslashes($page['body'])));

            $table->startRow();
            $table->addCell($this->objLanguage->languageText('word_title'), 150);
            $table->addCell($titleInput->show(), NULL, NULL, NULL, NULL, ' colspan="3"');
            $table->endRow();
            $objForm->addToForm($table->show());
            //$objForm->addRule('title', $this->objLanguage->languageText('mod_cmsadmin_pleaseaddtitle', 'cmsadmin'), 'required');


            //body
            $objForm->addToForm('<br /><h3>'.$this->objLanguage->languageText('mod_cmsadmin_maintext', 'cmsadmin').'</h3>');
            $objForm->addToForm($bodyInput->show());
            $objForm->addToForm('<p><br />'.$button->show().'</p>');


            //hidden elements
            $objForm->addToForm($publishedInput->show());
            $objForm->addToForm($accessInput->show());
            $objForm->addToForm($orderingInput->show());
            $objForm->addToForm($parentInput->show());
            $objForm->addToForm($creativeCommonsInput->show());
            $objForm->addToForm($introInput->show());

            $objH = $this->newObject('htmlheading', 'htmlelements');
            $objH->type = '3';
            $objH->str = 'Edit '. $page['title'];
            $strBody = $objH->show().'<p/>';

            $strBody .= $objForm->show();


            return $strBody;
        }

                /**
                 * Method to return the form for adding/removing blocks from the front page
                 *
                 * @access public
                 * @return string html
                 */
        public function showFrontBlocksForm()
        {
            $objCMSBlocks = $this->_objBlocks;
            $thisPageBlocks = $objCMSBlocks->getBlocksForFrontPage();
            $leftBlocks = $objCMSBlocks->getBlocksForFrontPage(1);

            //$init = 'fm_init();';
            $init = "bl_init('adddynamicfrontpageblock', 'addleftfrontpageblock', 'removedynamicfrontpageblock', 'init_x', 'init_x');";
            $str = $this->showBlocksForm($thisPageBlocks, $leftBlocks, $init);

            return $str;
        }

                /**
                 * Method to return the form for adding/removing blocks from the content page
                 *
                 * @access public
                 * @param string $id The row id of the content page where the blocks will be added
                 * @param string $section The id of the section containing the content page
                 * @return string html
                 */
        public function showContentBlocksForm($id, $section)
        {
            $objCMSBlocks = $this->_objBlocks;
            $thisPageBlocks = $objCMSBlocks->getBlocksForPage($id);
            $leftBlocks = $objCMSBlocks->getBlocksForPage($id, '', 1);

            //        $init = "ca_init('{$id}', '{$section}');";
            $init = "bl_init('adddynamicpageblock', 'addleftpageblock', 'removedynamicpageblock', '{$id}', '{$section}');";
            $str = $this->showBlocksForm($thisPageBlocks, $leftBlocks, $init);

            return $str;
        }

       /**
        * Method to return the form for adding/removing blocks from the section
        *
        * @access public
        * @param string $id The row id of the section
        * @return string html
        */
        public function showSectionBlocksForm($id)
        {
            $objCMSBlocks = $this->_objBlocks;
            $thisPageBlocks = $objCMSBlocks->getBlocksForSection($id);
            $leftBlocks = $objCMSBlocks->getBlocksForSection($id, 1);

            //        $init = "sa_init('{$id}')";
            $init = "bl_init('adddynamicsectionblock', 'addleftsectionblock', 'removedynamicsectionblock', '', '{$id}');";
            $str = $this->showBlocksForm($thisPageBlocks, $leftBlocks, $init);

            return $str;
        }

       /**
        * Method to display the form for adding and removing blocks to/from the left and right hand columns
        *
        * @access public
        * @return string html
        */
        public function showBlocksForm($thisPageBlocks, $leftBlocks, $onload = '')
        {
            $objIcon = $this->newObject('geticon', 'htmlelements');
            $objModuleBlocks = $this->getObject('dbmoduleblocks', 'modulecatalogue');
            $objBlocks = $this->getObject('blocks', 'blocks');

            $blocks = $objModuleBlocks->getBlocks('normal');

            // add js script library
            $headerParams = $this->getJavascriptFile('scripts.js', 'cmsadmin');
            $this->appendArrayVar('headerParams', $headerParams);

            // initialize onload scripts
            $this->appendArrayVar('bodyOnLoad', $onload);

            // language elements
            $lbAddedBl = $this->objLanguage->languageText('mod_cmsadmin_rightsideblocks', 'cmsadmin');
            $lbLeftBl = $this->objLanguage->languageText('mod_cmsadmin_leftsideblocks', 'cmsadmin');
            $lbDragBl = $this->objLanguage->languageText('mod_cmsadmin_dragaddblocks', 'cmsadmin');
            $lbPageBl = $this->objLanguage->languageText('mod_cmsadmin_pageblocks', 'cmsadmin');
            $lbLinkDis = $this->objLanguage->languageText('mod_cmsadmin_warnlinkdisabled', 'cmsadmin');
            $lbLoading = $this->objLanguage->languageText('word_loading');
            $lbAvailBl = $this->objLanguage->languageText('mod_cmsadmin_availableblocks', 'cmsadmin');
            $lbDragRem = $this->objLanguage->languageText('mod_cmsadmin_dragremoveblocks', 'cmsadmin');

            $blStr = ''; $usedBlocks = array();

            // Display loding bar
            $objIcon->setIcon('loading_bar', 'gif', 'icons/');
            $objIcon->title = $lbLoading;

            $objLayer = new layer();
            $objLayer->str = $objIcon->show();
            $objLayer->id = 'loading';
            $objLayer->display = 'none';
            $blStr .= $objLayer->show();

                                /* Create right side drop zone */
            $objHead = new htmlheading();
            $objHead->str = $lbAddedBl;
            $objHead->type = 4;
            $dropStr = $objHead->show();
            $dropStr .= '<p>'.$lbDragBl.'</p>';

            if(!empty($thisPageBlocks)){
                foreach ($thisPageBlocks as $block){
                    $str = trim($objBlocks->showBlock($block['blockname'], $block['moduleid'], '', 20, TRUE, TRUE, 'none'));
                    $str = preg_replace('/type\\s??=\\s??"submit"/', 'type="button"', $str);
                    $str = preg_replace('/href=".+?"/', 'href="javascript:alert(\''.$lbLinkDis.'\');"', $str);
                    $str = preg_replace('/onchange =".+"/', 'onchange="javascript:alert(\''.$lbLinkDis.'\');"', $str);

                    $usedBlocks[] = $block['blockid'];

                    $objLayer = new layer();
                    $objLayer->str = $str;
                    $objLayer->id = $block['blockid'];
                    $objLayer->cssClass = 'usedblock';
                    $dropStr .= $objLayer->show();
                }
            }

            // Drop zone for adding blocks
            $objLayer = new layer();
            $objLayer->str = $dropStr;
            $objLayer->id = 'dropzone';
            $objLayer->cssClass = 'dropblock';
            $rightStr = $objLayer->show();

                                /* Create left side drop zone */
            $objHead = new htmlheading();
            $objHead->str = $lbLeftBl;
            $objHead->type = 4;
            $dropStr = $objHead->show();
            $dropStr .= '<p>'.$lbDragBl.'</p>';

            if(!empty($leftBlocks)){
                foreach ($leftBlocks as $block){
                    $str = trim($objBlocks->showBlock($block['blockname'], $block['moduleid'], '', 20, TRUE, TRUE, 'none'));
                    $str = preg_replace('/type\\s??=\\s??"submit"/', 'type="button"', $str);
                    $str = preg_replace('/href=".+?"/', 'href="javascript:alert(\''.$lbLinkDis.'\');"', $str);
                    $str = preg_replace('/onchange =".+"/', 'onchange="javascript:alert(\''.$lbLinkDis.'\');"', $str);

                    $usedBlocks[] = $block['blockid'];

                    $objLayer = new layer();
                    $objLayer->str = $str;
                    $objLayer->id = $block['blockid'];
                    $objLayer->cssClass = 'leftblocks';
                    $dropStr .= $objLayer->show();
                }
            }


            // Drop zone for adding blocks
            $objLayer = new layer();
            $objLayer->str = $dropStr;
            $objLayer->id = 'leftzone';
            $objLayer->cssClass = 'dropleft';
            $leftStr = $objLayer->show();

                                /* Create delete zone */
            $objHead = new htmlheading();
            $objHead->str = $lbAvailBl;
            $objHead->type = '4';
            $delStr = $objHead->show();
            $delStr .= '<p>'.$lbDragRem.'</p>';

            if(!empty($blocks)){
                foreach ($blocks as $block){
                    if (!in_array($block['id'], $usedBlocks)) {
                        $str = trim($objBlocks->showBlock($block['blockname'], $block['moduleid'], '', 20, TRUE, TRUE, 'none'));
                        $str = preg_replace('/type\\s??=\\s??"submit"/', 'type="button"', $str);
                        $str = preg_replace('/href=".+?"/', 'href="javascript:alert(\''.$lbLinkDis.'\');"', $str);
                        $str = preg_replace('/onchange =".+"/', 'onchange="javascript:alert(\''.$lbLinkDis.'\');"', $str);

                        $objLayer = new layer();
                        $objLayer->str = $str;
                        $objLayer->id = $block['id'];
                        $objLayer->cssClass = 'addblocks';
                        $delStr .= $objLayer->show();
                    }
                }
            }

            $objLayer = new layer();
            $objLayer->str = $delStr;
            $objLayer->id = 'deletezone';
            $objLayer->cssClass = 'deleteblock';
            $allStr = $objLayer->show();


            $blStr .= $leftStr.$allStr.$rightStr.'<br clear="left" />';

            $objLayer = new layer();
            $objLayer->str = $blStr;
            $objLayer->id = 'selectblocks';

            return $objLayer->show();
        }

                /**
                 * Method to check if the user is in the CMS Authors group
                 *
                 * @access public
                 */
        public function checkPermission()
        {
            $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
            $groupId = $objGroups->getLeafId(array('CMSAuthors'));
            if($objGroups->isGroupMember($this->_objUser->pkId(), $groupId)){
                return TRUE;
            }else{
                return FALSE;
            }   
        }









/**
        * Method to return the add edit content form
        *
        * @param string $contentId The id of the content to be edited. Default NULL for adding new section
        * @access public
        * @return string $middleColumnContent The form used to create and edit a page
        * @author Warren Windvogel
        */
        public function ContentTestForm($contentId = NULL, $section = NULL, $fromModule = NULL, $fromAction = NULL, $s_param = NULL)
        {

            $h3 = $this->newObject('htmlheading', 'htmlelements');


            // Determine whether to show the toggle or not
            if ($section == NULL) {
                $toggleShowHideIntro = TRUE;
            } else {
                $sectionInfo = $this->_objSections->getSection($section);

                if ($sectionInfo == FALSE) {
                    $toggleShowHideIntro = TRUE;
                } else {
                    if ($sectionInfo['layout'] == 'summaries') {
                        $toggleShowHideIntro = FALSE;
                    } else {
                        $toggleShowHideIntro = TRUE;
                    }
                }
            }


            $published = new checkbox('published');
            $frontPage = new checkbox('frontpage');
            $frontPage->value = 1;

            $objOrdering = new textinput();
            //$objCCLicence = $this->newObject('licensechooser', 'creativecommons');
            $is_front = FALSE;
            $show_content = '0';
            if ($contentId == NULL) {
                $action = 'createcontent';
                $editmode = FALSE;
                $titleInputValue = '';
                $bodyInputValue = '';
                $introInputValue = '';
                $published->setChecked(TRUE);
                $visible = TRUE;
                $hide_title = '0';
                $contentId = '';
                $arrContent = null;

                if ($this->getParam('frontpage') == 'true') {
                    $frontPage->setChecked(TRUE);
                    $is_front = TRUE;
                }
            } else {
                $action = 'editcontent';
                $editmode = TRUE;
                $arrContent = $this->_objContent->getContentPage($contentId);
                $titleInputValue = $arrContent['title'];

                $bodyInputValue = stripslashes($arrContent['body']);

            }

            //setup form
            $action = ''; //Making it submit back to cms_test_template.
            $frontMan = $this->getParam('frontmanage', FALSE);
            $objForm = new form('addfrm', $this->uri(array('action' => $action, 'id' => $contentId, 'frontman' => $frontMan), 'cmsadmin'));
            $objForm->setDisplayType(3);

            if ($editmode) {
                //Set ordering as hidden field
                $sections = new hiddeninput('parent', $arrContent['sectionid']);
                //$objOrdering = new hiddeninput('ordering', $arrContent['ordering']);

            } else {
                if (isset($section) && !empty($section)) {
                    //$sections = $this->getContentTreeDropdown($section, FALSE);
                    $sections = $this->getSectionList($section, FALSE);
                } else {
                    //$sections = $this->getContentTreeDropdown(NULL, FALSE);
                    $sections = $this->getSectionList(NULL, FALSE);
                }
            }

            $tableContainer = new htmlTable();
            $tableContainer->width = "100%";
            $tableContainer->cellspacing = "0";
            $tableContainer->cellpadding = "0";
            $tableContainer->border = "0";
            $tableContainer->attributes = "align ='center'";

            $table = new htmlTable();
            //$table->width = "470px";
            $table->width = "70%";
            $table->cellspacing = "0";
            $table->cellpadding = "0";
            $table->border = "0";
            $table->attributes = "align ='center'";
            $this->loadClass('textinput', 'htmlelements');
            // Title Input
            $titleInput = new textinput ('title', $titleInputValue);
            $titleInput->cssId = 'input_title'; 

            $h3->str = $this->objLanguage->languageText('mod_cmsadmin_maintext', 'cmsadmin');
            $h3->type = 3;

            $table->startRow();
            $table->addCell($h3->show(), null, 'top', null, null, 'colspan="2"');
            $table->endRow();

            $table->startRow();
            $table->addCell('', null, 'top', null, null, 'style="padding-bottom:6px"');
            $table->endRow();


            $table->startRow();
            $table->addCell($this->objLanguage->languageText('word_title').': ' . $titleInput->show());

            if (!$editmode) {
                $table->addCell($this->objLanguage->languageText('word_section').': ' . $sections->show());
            } else {
                $table->addCell($sections->show());
            }

            $table->endRow();

/*
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('word_publish').': &nbsp; ');
            $table->addCell($this->getYesNoRadion('published', $visible));
            //$table->addCell($published->show());
            $table->endRow();

            $lbNo = $this->objLanguage->languageText('word_no');
            $lbYes = $this->objLanguage->languageText('word_yes');
            $objRadio = new radio('hide_title');
            $objRadio->addOption('1', '&nbsp;'.$lbYes);
            $objRadio->addOption('0', '&nbsp;'.$lbNo);
            $objRadio->setSelected($hide_title);
            $objRadio->setBreakSpace('&nbsp;&nbsp;');

            $table->startRow();
            $table->addCell($this->objLanguage->languageText('phrase_hidetitle').': &nbsp; ');
            $table->addCell($objRadio->show());
            //$table->addCell($published->show());
            $table->endRow();

            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_cmsadmin_showonfrontpage', 'cmsadmin').': ');
            $table->addCell($frontPage->show().'<span id="introrequiredtext" class="warning">'.$this->objLanguage->languageText('mod_cmsadmin_pleaseenterintrotext', 'cmsadmin').'</span>');
            $table->endRow();
*/


            //$table->row_attributes = "id='row_id'";
            //$table->startRow();
            //$table->addCell($table_fr->show(), null, 'top', null, null, 'colspan="2"');
            //$table->endRow();


            // Introduction Area
            $introInput = $this->newObject('htmlarea', 'htmlelements');
            $introInput->init('intro', $introInputValue);
            $introInput->setContent($introInputValue);
            $introInput->setBasicToolBar();
            $introInput->height = '200px';
            $introInput->width = '100%';

            $h3->str = $this->objLanguage->languageText('word_introduction').' ('.$this->objLanguage->languageText('word_required').')';
            $h3->type = 3;

            //add hidden text input
            $table->row_attributes = '';
            $table->startRow();
            //$table->addCell(NULL);
            $table->addCell('<div id="introdiv"><br />'.$h3->show().$introInput->show().'</div>','','left','left', null, 'colspan="2"');
            $table->endRow();

            //Adding the FCK_EDITOR

            if ($editmode) {

                if (isset($arrContent['body'])) {
                    $bodyInputValue = stripslashes($arrContent['body']);
                }else{
                    $bodyInputValue = null;
                }

            }

            $bodyInput = $this->newObject('htmlarea', 'htmlelements');
            $bodyInput->init('body', $bodyInputValue);
            $bodyInput->setContent($bodyInputValue);
            $bodyInput->setCMSToolBar();
            $bodyInput->loadCMSTemplates();
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
            //$table2->width = "250px";
            $table2->width = "30%";

            $h3->str = $this->objLanguage->languageText('mod_cmsadmin_contentparams','cmsadmin');
            $h3->type = 3;

            $table2->startRow();
            $table2->addCell($h3->show(), null, 'top', null, null, 'colspan="2"');
            $table2->endRow();

            $table2->startRow();
            $table2->addCell('', null, 'top', null, null, 'style="padding-bottom:10px"');
            $table2->endRow();

            $table2->startRow();

            if (!$editmode) {
                $table2->addCell($this->getConfigTabs());
            }else{
                $table2->addCell($this->getConfigTabs($arrContent));
            }
            $table2->endRow();
            // Content Area

            //Header for main body
            //$h3->str = $this->objLanguage->languageText('mod_cmsadmin_maintext', 'cmsadmin');
            //Pass action
            $txt_action = new textinput('action',$action,'hidden');
            $table->startRow();
            //$table->addCell($h3->show(),null,'center','left');
            //$table->addCell($table2->show(),null,'top','left', null, 'colspan="2"');
            if ($fromModule) { 
                $mod = new textinput('frommodule',$fromModule,'hidden');
                $act = new textinput('fromaction',$fromAction,'hidden');
                $param = new textinput('s_param',$s_param,'hidden');
                $table->addCell($mod->show().$act->show().$param->show()); 
            }
            //$table->addCell(,null,'bottom','center');
            $table->endRow();               

            //Adding the 250px hieght buffer to make sure the jQuery functions won't overlap existing skins
            if (!$is_front) {
                $layer = new layer();
                $layer->height = '250px';
                
                $table->startRow();
                $table->addCell($layer->show());
                $table->endRow();
            }
            
            /*
            //Should be done in the main CSS styles config
            $div1 = new layer();
            $div1->floating = 'left';
            $div1->width = '50%';
            $div1->str = $table->show();
            
            $div2 = new layer();
            $div2->floating = 'right';
            $div2->width = '50%';
            $div2->str = $table2->show();
            */

            $pageParams = new layer();
            $pageParams->id = 'AddContentPageParams';
            $pageParams->str = $table2->show();
    
            $tableContainer->startRow();
            $tableContainer->addCell($table->show(),'', 'top', '', 'AddContentLeft');
            $tableContainer->addCell($pageParams->show(),'', 'top', '', 'AddContentRight');
            $tableContainer->endRow();

            //Add validation for title            
            $errTitle = $this->objLanguage->languageText('mod_cmsadmin_entertitle', 'cmsadmin');
            $objForm->addRule('title', $errTitle, 'required');
            $objForm->addToForm($tableContainer->show());
            //$objForm->addToForm($div1->show());
            //add action
            $objForm->addToForm($txt_action);

            $objForm->addToForm("<br/>");
            $objForm->addToForm(" <input type='submit' value='submit'> ");


            //body
            // $dialogForm = new form();


            // $dialogForm->addToForm($table2->show());
            //add page header for the body

            $display = $objForm->show();    
            return $display;
        }


	   /**
		* Method to return the Flag Options Form (to be returned via AJAX)
		* @param $contentId Id of the content item being flagged
		* @access public
		*/
		public function getFlagOptionsForm($contentId)
		{
			$objForm = new form('frmaddflag', $this->uri(array('action' => 'flagcontent', 'id' => $contentId)));
            $objForm->setDisplayType(3);

			$tbl = new htmlTable();
			$tbl->width = "100%";
			$tbl->cellspacing = "0";
			$tbl->cellpadding = "10";
			$tbl->border = "0";
			$tbl->attributes = "align ='center'";
			
			$objDropDown = new dropdown('flag_options');

            $flagOptions = $this->_objFlagOptions->getPublishedOptions();

			if (!empty($flagOptions)) {

                /*
                $tbl->startRow();
                $tbl->addCell('Why are you flagging this content item:', '', '', '', 'boxy_td_left', '','');
                $tbl->endRow();
                */
                
                foreach ($flagOptions as $opt) {
                    $objDropDown->addOption($opt['id'], $opt['text']);
                }

				$objDropDown->setSelected('0');

				$tbl->startRow();
				$tbl->addCell($objDropDown->show(), '', '', '', 'boxy_td_left', '','');
				$tbl->endRow();

				// Submit Button
            	$button = new button('submitform', 'Flag this Item');
	            $button->setToSubmit();
                
                $objForm->addToForm($tbl->show());
				$objForm->addToForm('<br/>'.$button->show());

			} else {
				$tbl->startRow();
				$tbl->addCell('No published flag options have been loaded.', '', '', '', 'boxy_td_left', '','');
				$tbl->endRow();
                $objForm->addToForm($tbl->show());
			}
						
			$display = $objForm->show();
			
			return $display;
		}

   /**
    * Method to return the Boxy flag option add form
    *
    * @param string $id The id of the form to be edited
    * @access public
    */
        public function getAddFlagOptionAddForm($id = '')
        {
            //Load Edit Values when supplied with id
            if ($id != ''){
                $arrFlagOptions = $this->_objFlagOptions->getOption($id);
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
            //Match
            $lblTitle = 'Title :';

            if (!isset($arrFlagOptions['title'])){
                $arrFlagOptions['title'] = '';
            }
            
            $txtTitle = "<input type='text' id='txtTitle' name='txtTitle' class='match_target_url_init' value='$arrFlagOptions[title]'/>";

            $tbl->startRow();
            $tbl->addCell($lblTitle, '', '', '', 'boxy_td_left', '','');
            $tbl->addCell($txtTitle, '', 'top', 'left');
            $tbl->endRow();

            //Target
            $lblText = 'Text';

            if (!isset($arrFlagOptions['text'])){
                $arrFlagOptions['text'] = '';
            }

            $txtText = "<input type='text' id='txtText' name='txtText' class='match_target_url_init' value='$arrFlagOptions[text]'/>";

            $tbl->startRow();
            $tbl->addCell($lblText, '', '', '', 'boxy_td_left', '','');
            $tbl->addCell($txtText, '', 'top', 'left');
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
            $display = '<form id="frm_addgrid_'.$id.'" class="Form" name="frm_addgrid" action="?module=cmsadmin&action=addeditflagoption" method="POST">';
            $display .= str_replace("\n", '',$table->show());
            $display = str_replace("\n\r", '', $display);
            
            $display .= $action;
            $display .= '</form>';
            
            return $display;
        }


   /**
    * Method to return the Boxy flag option add form
    *
    * @param string $id The id of the form to be edited
    * @access public
    */
        public function getDeleteConfirmForm($id = '', $type = 'flagoption')
        {

$innerHtml = <<<HTSRC
<table>
<tr style="padding-bottom:20px;">
    <td>Are you sure you want to delete this item?</td>
</tr>
<tr>
    <td align="center">
        <form action="?module=cmsadmin&action=delete{$type}&confirm=yes&id={$id}" method="POST">
            <input id="rm_{$id}" value = 'Yes' type="submit" style="width:50px;"/>
            <input type="button" onclick='Boxy.get(this).hide(); return false' value="No" style="width:50px;"/>
            <input type="button" onclick='Boxy.get(this).hide(); return false' value="Cancel" style="width:50px;"/>
        </form>
    </td>
</tr>
</table>
HTSRC;
            $display = str_replace("\n", '',$innerHtml);
            $display = str_replace("\n\r", '', $display);

            return $display;
        }


   /**
    * Method to return the Boxy flag option add form
    *
    * @param string $id The id of the form to be edited
    * @access public
    */
        public function getAddEmailForm($id = '')
        {
            //Load Edit Values when supplied with id
            if ($id != ''){
                $arrFlagEmails = $this->_objFlagEmail->getEmail($id);
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
            //Match
            $lblName = 'Name :';

            if (!isset($arrFlagEmails['name'])){
                $arrFlagEmails['name'] = '';
            }

            $txtName = "<input type='text' id='txtName' name='txtName' class='match_target_url_init' value='".$arrFlagEmails['name']."'/>";

            $tbl->startRow();
            $tbl->addCell($lblName, '', '', '', 'boxy_td_left', '','');
            $tbl->addCell($txtName, '', 'top', 'left');
            $tbl->endRow();

            //Target
            $lblEmail = 'Email';

            if (!isset($arrFlagEmails['email'])){
                $arrFlagEmails['email'] = '';
            }

            $txtEmail = "<input type='text' id='txtEmail' name='txtEmail' class='match_target_url_init' value='".$arrFlagEmails['email']."'/>";

            $tbl->startRow();
            $tbl->addCell($lblEmail, '', '', '', 'boxy_td_left', '','');
            $tbl->addCell($txtEmail, '', 'top', 'left');
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
            $display = '<form id="frm_addgrid_'.$id.'" class="Form" name="frm_addgrid" action="?module=cmsadmin&action=addeditflagemail" method="POST">';
            $display .= str_replace("\n", '',$table->show());
            $display = str_replace("\n\r", '', $display);

            $display .= $action;
            $display .= '</form>';

            return $display;
        }


    }

?>