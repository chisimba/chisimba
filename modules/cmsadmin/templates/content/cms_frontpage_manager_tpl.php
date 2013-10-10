<?php
/**
 * This template outputs the front page manager
 * for the cms module
 * 
 * @package cmsadmin
 * @author Prince Mbekwa
 */
/**
 * The Dialog component is an extension of Panel that is meant to emulate the behavior of an dialog window using a floating, draggable HTML element. 
 * Dialog provides an interface for easily gathering information from the user without leaving the underlying page context. 
 * The information gathered is collected via a standard HTML form; 
 * Dialog supports the submission of form data through XMLHttpRequest, through a normal form submission, or through a manual script-based response 
 * (where the script reads and responds to the form values and the form is never actually submitted to the server).
*/

/*
$script ='
<script type="text/javascript">
//<![CDATA[

YAHOO.namespace("example.container");

				function init() {
					
					// Define various event handlers for Dialog
					var handleSubmit = function() {
						this.submit();
					};
					var handleCancel = function() {
						this.cancel();
					};
					var handleSuccess = function(o) {
						var response = o.responseText;
						response = response.split("<!")[0];
						document.getElementById("resp").innerHTML = response;
						eval(response);
					};
					var handleFailure = function(o) {
						alert("Submission failed: " + o.status);
					};

					// Instantiate the Dialog
					YAHOO.example.container.dialog1 = new YAHOO.widget.Dialog("dialog1", 
																				{ width : "100%",
																				  fixedcenter : true,
																				  visible : false, 
																				  constraintoviewport : true,
																				 
																				 } );
					
					// Validate the entries in the form to require that both first and last name are entered
					YAHOO.example.container.dialog1.validate = function() {
						var data = this.getData();
						
							return true;
						
					};

					// Wire up the success and failure handlers
					YAHOO.example.container.dialog1.callback = { success: handleSuccess,
																 failure: handleFailure };
					
					// Render the Dialog
					YAHOO.example.container.dialog1.render();

					YAHOO.util.Event.addListener("show", "click", YAHOO.example.container.dialog1.show, YAHOO.example.container.dialog1, true);
					YAHOO.util.Event.addListener("hide", "click", YAHOO.example.container.dialog1.hide, YAHOO.example.container.dialog1, true);
				}

				YAHOO.util.Event.addListener(window, "load", init);

//]]>
</script>
';
$this->appendArrayVar('headerParams', $this->getJavascriptFile('yahoo/yahoo.js', 'yahoolib'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('event/event.js', 'yahoolib'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('dom/dom.js', 'yahoolib'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('dragdrop/dragdrop.js', 'yahoolib'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('container/container.js', 'yahoolib'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('connection/connection.js', 'yahoolib'));
$this->appendArrayVar('headerParams', $script);
*/


//initiate objects
//$table =  $this->newObject('htmltable', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$objH = $this->newObject('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');
$objLayer =$this->newObject('layer','htmlelements');
$h3 = $this->getObject('htmlheading', 'htmlelements');
//create link to add blocks to the front page
//Get blocks icon
$objIcon->setIcon('modules/blocks');
$objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_addremoveblocks', 'cmsadmin');
$blockIcon = $objIcon->show();

//Check if blocks module is registered
$this->objModule = $this->newObject('modules', 'modulecatalogue');
$isRegistered = $this->objModule->checkIfRegistered('blocks');

//Create link
$url = $this->uri(array('action' => 'positionblock', 'blockcat' => 'frontpage'));
$objBlocksLink = new link('#');
$objBlocksLink -> link = $blockIcon;
$objBlocksLink -> extra = "onclick = \"javascript:window.open('" . $url . "', 'branch', 'width=500, height=350, top=50, left=50, scrollbars');\"";

//Heading box
$objIcon->setIcon('frontpage_small', 'png', 'icons/cms/');
$objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_sectionmanager', 'cmsadmin');
//$objH->str =  $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_sectionmanager', 'cmsadmin');

//create a heading
if($isRegistered){
    $objH->str = $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_frontpagemanager', 'cmsadmin').'&nbsp;'.$objBlocksLink->show();
} else {
    $objH->str = $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_frontpagemanager', 'cmsadmin');
}
$objH->type = '1';

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

//Get Selectall js
echo $this->getJavascriptFile('selectall.js');

$header = $header;//.$headShow;//$tbl->show());

//counter for records
$cnt = 1;

$objCheck = new checkbox('toggle');
$objCheck->extra = "onclick=\"javascript:ToggleCheckBoxes('select', 'arrayList[]', 'toggle');\"";

$table = new htmltable();
$table->cellspacing = '2';
$table->cellpadding = '5';

//setup the table headings
$table->startHeaderRow();
$table->addHeaderCell($objCheck->show());
$table->addHeaderCell($this->objLanguage->languageText('word_title'));
$table->addHeaderCell($this->objLanguage->languageText('word_published'));
$table->addHeaderCell($this->objLanguage->languageText('word_section'));
$table->addHeaderCell($this->objLanguage->languageText('word_order'));
$table->addHeaderCell($this->objLanguage->languageText('word_options'));
$table->endHeaderRow();

$lbConfirm = $this->objLanguage->languageText('mod_cmsadmin_confirmremovefromfp', 'cmsadmin');

//setup the tables rows  and loop though the records
if(!empty($files)){
    $count = 1;
    $total = count($files);
    
    $oddOrEven = 'even';
    foreach($files as $file) {
        //$arrFile = $this->_objContent->getContentPage($file['content_id']);
    
        $oddOrEven = ($oddOrEven == 'odd') ? 'even' : 'odd';
    
        //Create delete & edit icon for removing content from front page
        $objIcon->setIcon('edit');
        $link = new link($this->uri(array('action' => 'addcontent', 'id' => $file['content_id'], 'parent' => $file['sectionid'], 'frontmanage' => TRUE)));
        $link->link = $objIcon->show();
        $editPage = $link->show();
    
        $delArray = array('action' => 'removefromfrontpage', 'id' => $file['front_id']);
        $delIcon = $objIcon->getDeleteIconWithConfirm('', $delArray, 'cmsadmin', $lbConfirm);
    
        $objCheck = new checkbox('arrayList[]');
        $objCheck->setValue($file['content_id']);
        $objCheck->extra = "onclick=\"javascript: ToggleMainBox('select', 'toggle', this.checked);\"";
        
	    //publish, visible
	    if($file['published']){
	       $url = $this->uri(array('action' => 'contentpublish', 'id' => $file['content_id'], 'mode' => 'unpublish'));
	       $icon = $this->_objUtils->getCheckIcon(TRUE);
	    }else{
	       $url = $this->uri(array('action' => 'contentpublish', 'id' => $file['content_id'], 'mode' => 'publish'));
	       $icon = $this->_objUtils->getCheckIcon(FALSE);
	    }
	    $objLink = new link($url);
	    $objLink->link = $icon;
	    $visibleLink = $objLink->show();
    
        $tableRow = array();
        $tableRow[] = $objCheck->show();
        $tableRow[] = $file['title'];
        $tableRow[] = $visibleLink;
    
        $link = new link($this->uri(array('action' => 'viewsection', 'id' => $file['sectionid'])));
        $link->link = $this->_objSections->getMenuText($file['sectionid']);
    
        $tableRow[] = $link->show();
        $tableRow[] = $this->_objFrontPage->getOrderingLink($file['front_id'], $file['pos'], $count++, $total);
        $tableRow[] = '<nobr>'.$editPage.$delIcon.'</nobr>';
    
        $table->addRow($tableRow, $oddOrEven);
    }
}

$objInput = new textinput('task', '', 'hidden');
$hidden = $objInput->show();
    
$objForm = new form('select', $this->uri(array('action' => 'publishfrontpage')));
$objForm->addToForm($table->show().$hidden);

//print out the page
$middleColumnContent = "";
$middleColumnContent .= $header;//objH->show();
$middleColumnContent .= '<br /><br><br><br><br>';
$middleColumnContent .= '<br><br>'.$objForm->show();

if (empty($files)) {
    $middleColumnContent .= '<div class="noRecordsMessage" >'.$this->objLanguage->languageText('mod_cmsadmin_nopagesonfrontpage', 'cmsadmin').'</div>';
}

echo $middleColumnContent;

if (empty($files)) {
    $middleColumnContent .= '<div class="noRecordsMessage" >'.$this->objLanguage->languageText('mod_cmsadmin_nopagesonfrontpage', 'cmsadmin').'</div>';
}

//echo '<br />';
//echo '<h2>'.$this->objLanguage->languageText('mod_cmsadmin_frontpageblocks', 'cmsadmin', 'Front Page Blocks').'</h2>';

//echo $this->_objUtils->showFrontBlocksForm();

?>