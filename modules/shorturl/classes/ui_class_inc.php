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

    class ui extends object
    {
       /**
        * The map object
        *
        * @access private
        * @var object
        */
        protected $objMap;	

      /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                $this->objMap =$this->newObject('dbmap', 'shorturl');
                $this->objGrid =$this->newObject('jqgrid', 'jquery');

                $this->setVar('jquery_boxy_theme', 'shorturl');

                $this->objBox = $this->newObject('jqboxy', 'jquery');
                $this->jQuery =$this->newObject('jquery', 'jquery');
                //$this->objConfig =$this->newObject('altconfig', 'config');
                $this->objLanguage =$this->newObject('language', 'language');

                //Loading the jqGrid with cms theme
                $this->objGrid->loadGrid('shorturl');
                
                //Live Query
                $this->jQuery->loadLiveQueryPlugin();
                $this->jQuery->loadFormPlugin();

                $this->loadClass('textinput', 'htmlelements');
                $this->loadClass('checkbox', 'htmlelements');
                $this->loadClass('radio', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                //$this->loadClass('form', 'htmlelements');
                $this->loadClass('button', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
                //$this->loadClass('label', 'htmlelements');
                //$this->loadClass('hiddeninput', 'htmlelements');
                //$this->loadClass('textarea','htmlelements');
                $this->loadClass('htmltable','htmlelements');
                $this->loadClass('layer', 'htmlelements');
                $this->loadClass('jqboxy', 'jquery');

            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

       /**
	* Method to return the Main Form
	*
	* @access public
	* @return HTML
	*/
        public function showList()
        {
            $h3 = $this->newObject('htmlheading', 'htmlelements');
            $link =  $this->newObject('link', 'htmlelements');
            $objIcon =  $this->newObject('geticon', 'htmlelements');

            $objForm = new form('addfrm', $this->uri(array('action' => $action, 'id' => $contentId, 'frontman' => $frontMan), 'cmsadmin'));
            $objForm->setDisplayType(3);

            $table_list = new htmlTable();
            $table_list->width = "99%";
            $table_list->cellspacing = "0";
            $table_list->cellpadding = "0";
            $table_list->border = "0";
            $table_list->attributes = "align ='center'";

            $this->bindEditEvents();
            $this->bindDeleteConfirmEvents();

            $table_list->startHeaderRow();
            $table_list->addHeaderCell('Match URL');
            $table_list->addHeaderCell('Target URL');
            //$this->objGrid->addColumn('Dynamic', 'is_dynamic', '70', 'center', false);
            //$table_list->addHeaderCell('Order');
            $table_list->addHeaderCell('Options');
            $table_list->addHeaderCell('Date');
            $table_list->endHeaderRow();

            //Traversing the array of maps
            $arrMaps = $this->objMap->getAll();
            
            if (!empty($arrMaps)){
            
				$class = 'odd';

                //Adding Mappings Here
                foreach ($arrMaps as $map){
                
                    //Setting up the fields for display
            
                    $matchUrl = $map['match_url'];
                    $targetUrl = $map['target_url'];
                    $order = $map['ordering'];
                    $date = $map['datestamp'];
            
                    //is Dynamic Checkbox
                    /* //Ditched this for only displaying (Overhead of forms loading not worth the bandwith)
                    $chkBool = ($map['is_dynamic'] == '1')? true : false;
                    $check = new checkbox('chk_dynamic', '', $chkBool);
                    $check->cssId = 'edit_'.$map['id'];
                    $dynamic = $check->show();
                    */
            
                    if ($map['is_dynamic'] == '1') {
                        $objIcon->setIcon('checked', 'gif', 'icons/shorturl/');
                    } else {
                        $objIcon->setIcon('unchecked', 'gif', 'icons/shorturl/');
                    }
            
                    $dynamic = $objIcon->show();
            
                    //options [edit/delete]
                    //TODO: Add Node level Security for Rules
            
                    //if ($this->_objSecurity->canUserWriteSection($section['id'])){


                    //$delArray = array('action' => 'deletemapping', 'confirm'=>'yes', 'id'=>$map['id']);
                    //$deletephrase = $this->objLanguage->languageText('mod_shorturl_confirmdelmapping', 'shorturl');
                    //$delIcon = $objIcon->getDeleteIconWithConfirm($map['id'], $delArray,'shorturl',$deletephrase);


            
                    $objIcon->setIcon('bigtrash');
                    $delIcon = "<a id='del_$map[id]' title='Delete' href='javascript:void(0)'>".$objIcon->show()."</a>";
            
                    //} else {
                    //    $delIcon = '';
                    //}
            
                    //edit icon
                    //if ($this->_objSecurity->canUserWriteSection($section['id'])){
                    $objIcon->setIcon('edit');
                    $editIcon = "<a id='edit_$map[id]' title='Edit' href='javascript:void(0)'>".$objIcon->show()."</a>";
                    //$editIcon = "<a id='edit_$map[id]' title='Edit URL Mapping' href='http://localhost/fresh/?module=shorturl&action=getform&type=addeditform&id=$map[id]'>".$objIcon->show()."</a>";
                    //} else {
                    //    $editIcon = '';
                    //}
            
                    /*
                    if (!$this->_objSecurity->canUserWriteSection($section['id'])){
                        $editIcon = '';
                        $deleteIcon = '';
                    }
                    */
            
                    $options = $editIcon.$delIcon;

                    $class = ($class == 'odd')? 'even' : 'odd';

					$table_list->startRow($class);
					
                    $matchUrl = '<div> <a href="' . $matchUrl . '"> ' . $matchUrl . ' </a></div>';
                    $targetUrl = '<div> <a href="' . $targetUrl . '"> ' . $targetUrl . ' </a></div>';
                    
                    $table_list->addCell($matchUrl);
                    $table_list->addCell($targetUrl);
                    //$table_list->addCell($order);
                    $table_list->addCell($options);
                    $table_list->addCell($date);
                    
                    $table_list->endRow();


                }
  
            }


/*
            $tableContainer = new htmlTable();
            $tableContainer->width = "100%";
            $tableContainer->cellspacing = "0";
            $tableContainer->cellpadding = "0";
            $tableContainer->border = "0";
            $tableContainer->attributes = "align ='center'";

            $tableContainer->startRow();
            $tableContainer->addCell($this->objGrid->show());
            $tableContainer->endRow();
*/  
            //Add validation for title            
            $errTitle = $this->objLanguage->languageText('mod_cmsadmin_entertitle', 'cmsadmin');
            $objForm->addRule('title', $errTitle, 'required');
            //$objForm->addToForm($tableContainer->show());
            
            $objForm->addToForm($txt_action);
            
            //Testing the output div of the ajax form submission
            $objLayer = new layer();
            $objLayer->id = 'output_div';
            $objLayer->str = $table_list->show();
            
            $display = $objForm->show().$objLayer->show();

            return $display;
        }

       /**
        * Method to Bind the Delete Confirm requests for the current mappings
        * Depricated: Use callback binding instead
        */
        public function bindDeleteConfirmEvents()
        {
            $arrMaps = $this->objMap->getAll();
            if (!empty($arrMaps)){
                foreach ($arrMaps as $map){
                    //Attaching the Delete/Confirm Dialogs to the links

$innerHtml = <<<HTSRC
<table>
    <tr style="padding-bottom:20px;">
        <td>Are you sure you want to delete this item?</td>
    </tr>
    <tr>
        <td align="center">
            <form action="?module=shorturl&action=deletemapping&confirm=yes&id={$map['id']}" method="POST">
                <input id="rm_$map[id]" value = 'Yes' type="submit" style="width:50px;"/>
                <input type="button" onclick='Boxy.get(this).hide(); return false' value="No" style="width:50px;"/>
                <input type="button" onclick='Boxy.get(this).hide(); return false' value="Cancel" style="width:50px;"/>
            </form>
        </td>
    </tr>
</table>
HTSRC;
                    //Stripping new lines for js compatibility
                    $innerHtml = str_replace("\n", '', $innerHtml);
                    $this->objBox->setHtml($innerHtml);
                    $this->objBox->setTitle('Confirm Delete');
                    $this->objBox->attachClickEvent('del_'.$map['id']);

                    // Binding the Actual Delete events to the 'OK' buttons of the corresponding confirm dialog
                    // When you click ok to delete, the handler will be ready to remove the specified row from 
                    // the grid via ajax request.
                    $this->objGrid->attachDeleteEvent("rm_$map[id]", $map['id']);
                }
            }
        }

       /**
        * Method to Bind the Edit for the current mappings
        * 
        */
        public function bindEditEvents()
        {
            $arrMaps = $this->objMap->getAll();
            if (!empty($arrMaps)){
                foreach ($arrMaps as $map){
                    //Attaching the Delete/Confirm Dialogs to the links

                    $innerHtml = $this->getAddMappingForm($map['id']);

                    //Stripping new lines for js compatibility
                    $innerHtml = str_replace("\n", '', $innerHtml);
                    $this->objBox->setHtml($innerHtml);
                    $this->objBox->setTitle('Edit URL Mapping');
                    $this->objBox->attachClickEvent('edit_'.$map['id']);

                    // Binding the Actual Delete events to the 'OK' buttons of the corresponding confirm dialog
                    // When you click ok to delete, the handler will be ready to remove the specified row from 
                    // the grid via ajax request.
                    //$this->objGrid->attachDeleteEvent("rm_$map[id]", $map['id']);
                }
            }

        }

   /**
    * Method to return the Mapping Form
    *
    * @param string $mapId The id of the mapping to be edited.Default NULL for adding new mapping
    * @access public
    */
        public function getAddMappingForm($mapId = '')
        {

            //Load Edit Values when supplied with id
            $arrMaps = array();
            $arrKeys = array();
            if ($mapId != '') {
                $arrMaps = $this->objMap->getAll(" WHERE id = '$mapId' ");
                $arrMaps = $arrMaps[0];
                //Getting associated keys if dynamic
                if ($arrMaps['is_dynamic']) {
                    $arrKeys = $this->objMap->getKeys($mapId);
                }
            }


//Attaching the callback method to dynamically bind EDIT events for Ajax loaded items
//This will be called when the grid completes the JSON load
/*
$script = <<<EDITCALLBACK
<script type="text/javascript">
    //Method to provide a callback function to bind edit events
    function bindAjaxEditForm(anchor_id, row_id){
        //Initializing the AJAX Boxy Window
        options = {
            sessionName: 'PHPSESSID',
            showBoxOnSessionExpiry: false,
            ajaxComplete: function(data){
                jQuery('#chisimba_grid_01').hideLoading();

                //Firefox 3.05beta won't rebind after first event fired (Boxy submit only works once)
                bindFormSubmit('frm_addgrid_' + row_id, 'frm_submit_btn_' + row_id);

            },
            ajaxError: function(data){
                location.href="?module=shorturl";
            },
            ajaxSessionExpired: function(data){
                jQuery('#chisimba_grid_01').hideLoading();
                document.location.href="?module=shorturl";
            }
        };

        jQuery('#' + anchor_id).livequery('click',function(){
            jQuery('#chisimba_grid_01').showLoading();
        });

        jQuery('#' + anchor_id).boxy(options);

    }

</script>
EDITCALLBACK;
$this->appendArrayVar('headerParams', $script);
*/

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
            $lblMatchUrl = 'Match URL:';

            if ($arrMaps['is_dynamic']) {
                //Disabled
                $txtMatchUrl = "<input type='text' id='txtMatchUrl' name='txtMatchUrl' class='match_target_url_init' value='$arrMaps[match_url]' disabled/>";
            } else {
                //Enabled
                $txtMatchUrl = "<input type='text' id='txtMatchUrl' name='txtMatchUrl' class='match_target_url_init' value='$arrMaps[match_url]'/>";
            }

            $tbl->startRow();
            $tbl->addCell($lblMatchUrl, '', '', '', 'boxy_td_left', '','');
            $tbl->addCell($txtMatchUrl, '', 'top', 'left');
            $tbl->endRow();

            //Target
            $lblTargetUrl = 'Target URL:';
            $txtTargetUrl = "<input type='text' id='txtTargetUrl' name='txtTargetUrl' class='match_target_url_init' value='$arrMaps[target_url]'/>";

            $tbl->startRow();
            $tbl->addCell($lblTargetUrl, '', '', '', 'boxy_td_left', '','');
            $tbl->addCell($txtTargetUrl, '', 'top', 'left');
            $tbl->endRow();

            //Dynamic
            $lblDynamic = 'Dynamic:';
            $chkbox = new checkbox('is_dynamic');
            $chkbox->ischecked = $arrMaps['is_dynamic'];
            $chkbox->extra = 'onclick="doToggle(\'addkeysform\', \'input_is_dynamic\'); doToggle(\'addkeybutton\', \'input_is_dynamic\'); activateAddLink()"';


            //$chkDynamic = $chkbox->show(); //ENABLE DYNAMIC

            //$btnAddKey = '<a id="addkeybutton" href="#"> Add Key </a>';

            $tbl->startRow();
            //$tbl->addCell($lblDynamic, '', '', '', 'boxy_td_left', '',''); //ENABLE DYNAMIC
            $tbl->addCell('<div style="float:left;">'.$chkDynamic.' </div> <div id="addkeybuttoncontainer" style="float:right;display:none;">'.$btnAddKey.'</div>');
            $tbl->endRow();

            //Submit/Cancel
            $btnOk = "<input type='submit' id='sData' name='sData' value='Save' style='width:50px;'/>";
            //$btnOk = "<input type='button' id='frm_submit_btn_$mapId' name='frm_add_submit' value='Save' style='width:50px;'/>";
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

            //addkeysform
            $tblKeys = new htmlTable();
            $tblKeys->width = "100%";
            $tblKeys->cellspacing = "0";
            $tblKeys->cellpadding = "0";
            $tblKeys->border = "0";
            $tblKeys->attributes = "align ='center'";
            //$tblKeys->id = 'addkeysform';

            $tblKeys->row_attributes = 'id="keyclonesource"';
            $tblKeys->startRow();
            $tblKeys->addCell('', '', '', '', 'boxy_td_key_left', '','');
            $tblKeys->addCell('Table:');
            $tblKeys->addCell('Column:');
            $tblKeys->endRow();

            //TODO: Revisit the key cloning
            // Fixed Amount of Keys
            /*
            if ($arrMaps['is_dynamic']) {
                var_dump($arrKeys);
                exit;
            }*/


            for ($i = 1; $i < 6; $i++) {
                //Key[$i]
                $lblKey = "Key $i:";
                $txtKeyTable = "<input type='text' id='txtKeyTable$i' name='txtKeyTable$i' value='".$arrKeys[$i-1]['tbl_name']."'/>";
                $txtKeyField = "<input type='text' id='txtKeyField$i' name='txtKeyField$i' value='".$arrKeys[$i-1]['tbl_field']."'/>";
    
                $tblKeys->row_attributes = 'id="keyclonesource"';
                $tblKeys->startRow();
                $tblKeys->addCell($lblKey, '', '', '', 'boxy_td_key_left', '','');
                $tblKeys->addCell($txtKeyTable, '', '', '', 'boxy_td_key_table_pad_right', '');
                $tblKeys->addCell($txtKeyField);
                $tblKeys->endRow();
            }

            /* // Dynamic UI key cloning works but not feasible for purpose of adding shorturls
            $lblKey = "Key:";
            $txtKeyTable = "<input type='text' id='txtKeyTable' name='txtKeyTable' value=''/>";
            $txtKeyField = "<input type='text' id='txtKeyField' name='txtKeyField' value=''/>";

            $tblKeys->row_attributes = 'id="keyclonesource"';
            $tblKeys->startRow();
            $tblKeys->addCell($lblKey, '', '', '', 'boxy_td_key_left', '','');
            $tblKeys->addCell($txtKeyTable, '', '', '', '', 'style="padding-right:10px;"');
            $tblKeys->addCell($txtKeyField);
            $tblKeys->endRow();
            */

            $layer = new layer();
            $layer->id = 'addkeysform';

            if ($arrMaps['is_dynamic']) {
                $layer->cssClass = 'toggleShow';
            } else {
                $layer->cssClass = 'toggleHide';
            }

            $layer->str = $tblKeys->show();

            //Adding All to Container here
            $table->startRow();
            $table->addCell($tbl->show()/*.$layer->show()*/.'<div style="padding-bottom:10px"></div>'.$tbl1->show(), '', '', 'center', '', '','');
            $table->endRow();

            $action = '<input type="hidden" name="oper" value="edit" />';
            $action .= '<input type="hidden" name="id" value="'.$arrMaps['id'].'" />';

            //Stripping New Lines and preparing for boxy input = (Facebook style window)
            $display = '<form id="frm_addgrid_'.$mapId.'" class="FormGrid" name="frm_addgrid" action="?module=shorturl&action=editmapping" method="POST">';
            $display .= str_replace("\n", '', $table->show());
            $display .= $action;
            $display .= '</form>';

            return $display;
        }


       /**
        * Method to return the Header + Top Navigation items
        *
        * @return string The top navigation header
        * @access public
        */
        public function showTopNav()
        {

            $objIcon = $this->newObject('geticon', 'htmlelements');
            $tbl = $this->newObject('htmltable', 'htmlelements');
            $tblH = $this->newObject('htmltable', 'htmlelements');
            $h3 = $this->getObject('htmlheading', 'htmlelements');
            //$Icon = $this->newObject('geticon', 'htmlelements');
            $objContainerLayer = $this->newObject('layer', 'htmlelements');
            $objLayer = $this->newObject('layer', 'htmlelements');
            //$Icon->setIcon('loading_circles_big');
            $objRound =$this->newObject('roundcorners','htmlelements');
            $objIcon->setIcon('shorturl_big', 'png', 'icons/modules/');

            $topNav = $this->getTopNav();

            //$strShortUrl = $this->objLanguage->languageText('mod_shorturl_main', 'cmsadmin')
            $strShortUrl = 'Short URL Manager';
            $h3->str = $strShortUrl;

            $tblH->width = '300px';
            $tblH->startRow();
            $tblH->addCell($objIcon->show(), '50px');
            $tblH->addCell($h3->show(), '150px', 'center');
            $tblH->endRow();
            
            $objLayer->str = $tblH->show();
            $objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
            $header = $objLayer->show();
            
            $objLayer->str = $topNav;
            $objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
            $header .= $objLayer->show();
            
            $objLayer->str = '';
            $objLayer->border = '; clear:both; margin:0px; padding:0px;';
            $headShow = $objLayer->show();
            
            $objContainerLayer->str = $header.$headShow.'<hr />';
            $objContainerLayer->id = 'shorturl_header';
            
            return $objContainerLayer->show();
        }


       /**
        * Method to get the Top Navigation Icons for Short URL
        * 
        * @return str the string top navigation
        *
        * @access public
        */
        public function getTopNav(){

            //Declare objects
            $tbl = $this->newObject('htmltable', 'htmlelements');
            $objIcon = $this->newObject('geticon', 'htmlelements');

            $iconList = '';

            //Setting up the Boxy Form
            
            $this->objBox->setHtml($this->getAddMappingForm());
            $this->objBox->setTitle('Add URL Mapping');
            $this->objBox->attachClickEvent('add_mapping_form');
            
            // New / Add
            $url = 'javascript:void(0)';
            $linkText = $this->objLanguage->languageText('word_new');
            $iconList .= $objIcon->getCleanTextIcon('add_mapping_form', $url, 'new', $linkText, 'png', 'icons/shorturl/');

            /*
            // Refresh Grid
            $url = 'javascript:void(0)';
            //$linkText = $this->objLanguage->languageText('word_refresh');
            $linkText = 'Refresh';
            $iconList .= $objIcon->getCleanTextIcon('refresh_grid', $url, 'refresh', $linkText, 'png', 'icons/shorturl/');
            */

            /* //Need to revisit multiple deletes using jqGrid - Need to fix this in the jqgrid.formedit.js
            // Delete
            $url = 'javascript:void(0)';
            //$linkText = $this->objLanguage->languageText('word_refresh');
            $linkText = 'Delete';
            $iconList .= $objIcon->getCleanTextIcon('delete_griditems', $url, 'delete', $linkText, 'png', 'icons/shorturl/');
            */

            return '<div style="align:right;">'.$iconList.'</div>';

            //return $tbl->show();

        }



    }

?>