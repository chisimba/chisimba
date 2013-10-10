<?php
/**
 * This template will list all the Flags
 */

//initiate objects
$table =  $this->newObject('htmltable', 'htmlelements');
$objH = $this->newObject('htmlheading', 'htmlelements');
$link =  $this->newObject('link', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$objLayer =$this->newObject('layer','htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');

//Boxy New Flag Option Form
$innerHtml = $this->_objUtils->getAddFlagOptionAddForm();
$this->_objBox->setHtml($innerHtml);
$this->_objBox->setTitle('Add Flag Option');
$this->_objBox->attachClickEvent('btn_new');

//Boxy New Email Form
$innerHtml = $this->_objUtils->getAddEmailForm();
$this->_objBox->setHtml($innerHtml);
$this->_objBox->setTitle('Add Email to Alert');
$this->_objBox->attachClickEvent('btn_add_email');

if (!isset($middleColumnContent)) {
	$middleColumnContent = '';
}

$objIcon = $this->newObject('geticon', 'htmlelements');
$tbl = $this->newObject('htmltable', 'htmlelements');
$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');
$objIcon->setIcon('flag_small', 'png', 'icons/cms/');
$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('mod_cmsadmin_flag_heading', 'cmsadmin');

$objLayer->str = $h3->show();
//$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_left';
$header = $objLayer->show();

$objLayer->str = $topNav;
//$objLayer->border .= '; float:right; align:right; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_right';
$header .= $objLayer->show();

$objLayer->str = '';
//$objLayer->border = '; clear:both; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_clear';
$objLayer->cssClass = 'clearboth';
$headShow = $objLayer->show(); 

$display = '<p>'.$header.$headShow.'</p><hr />';
//Show Header
echo $display;
// Show Form
//echo "<br><br><br><br>";
//echo $objLayer->show();//$tbl->show());


$table = new htmltable();
$table->cellspacing = '2';
$table->cellpadding = '5';


$h3->str = '<p>'. $this->objLanguage->languageText('mod_cmsadmin_flag_options', 'cmsadmin').'</p>';
echo $h3->show();


//setup the table headings
$table->startHeaderRow();
//$table->addHeaderCell("<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"javascript:ToggleCheckBoxes('select', 'arrayList[]', 'toggle');\" />" . " " . $this->objLanguage->languageText("mod_cms_selectall", "cmsadmin"));
//$table->addHeaderCell($this->objLanguage->languageText('word_image', 'system'));
$table->addHeaderCell($this->objLanguage->languageText('word_title', 'system'));
$table->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_flag_text', 'cmsadmin'));
$table->addHeaderCell($this->objLanguage->languageText('word_publish', 'system'));
$table->addHeaderCell($this->objLanguage->languageText('word_options'));
$table->endHeaderRow();

$rowcount = 0;

if (!isset($arrFlagOptions)) {
	$arrFlagOptions = array();
}

//setup the tables rows  and loop though the records
if (count($arrFlagOptions) > 0) {
	foreach($arrFlagOptions as $flagOption) {
	    //Set odd even row colour
	    $oddOrEven = ($rowcount == 0) ? "even" : "odd";
		
        //$flagOptionThumb = '<img src="'.$flagOption['image'].'" width="100px" height="70px"/>';

		//Set up select form
		$objCheck = new checkbox('arrayList[]');
		$objCheck->setValue($flagOption['id']);
		$objCheck->extra = "onclick=\"javascript: ToggleMainBox('select', 'toggle', this.checked);\"";
		
	    //publish, visible
	    if($flagOption['published']){
	       $url = $this->uri(array('action' => 'flagpublish', 'id' => $flagOption['id'], 'mode' => 'unpublish'));
	       $icon = $this->_objUtils->getCheckIcon(TRUE);
	    }else{
	       $url = $this->uri(array('action' => 'flagpublish', 'id' => $flagOption['id'], 'mode' => 'publish'));
	       $icon = $this->_objUtils->getCheckIcon(FALSE);
	    }
	    $objLink = new link($url);
	    $objLink->link = $icon;
	    $visibleLink = $objLink->show();
	
	    //Create delete icon
        //TODO: Enable Security
		//if ($this->_objSecurity->canUserWriteSection($flagOption['id'])){

            /*
		    $delArray = array('action' => 'deletetemplate', 'confirm'=>'yes', 'id'=>$flagOption['id']);
            $deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdeltemplate', 'cmsadmin');
		    $delIcon = $objIcon->getDeleteIconWithConfirm($flagOption['id'], $delArray,'cmsadmin',$deletephrase);
            */
            
            $objIcon->setIcon('bigtrash');
            $deleteIcon = "<a id='btn_del_{$flagOption['id']}' title='Delete' href='javascript:void(0)'>".$objIcon->show()."</a>";

            $innerHtml = $this->_objUtils->getDeleteConfirmForm($flagOption['id']);
            $this->_objBox->setHtml($innerHtml);
            $this->_objBox->setTitle('Confirm');
            $this->_objBox->attachClickEvent("btn_del_{$flagOption['id']}");


		//} else {
		//	$delIcon = '';
		//}
	    
	    //edit icon
        //TODO: Enable Security
		//if ($this->_objSecurity->canUserWriteSection($flagOption['id'])){
            $span = '<span id="'.'btn_add_' . $flagOption['id'].'">'.$objIcon->getEditIcon('#') . '</span>';
	    	$editIcon = $span;

            $innerHtml = $this->_objUtils->getAddFlagOptionAddForm($flagOption['id']);
            $this->_objBox->setHtml($innerHtml);
            $this->_objBox->setTitle('Add Flag Option');
            $this->_objBox->attachClickEvent('btn_add_' . $flagOption['id']);

		//} else {
		//	$editIcon = '';
		//}
	
	    $tableRow = array();
	    //$tableRow[] = $objCheck->show();
        //$tableRow[] = $flagOptionThumb;
	    $tableRow[] = html_entity_decode($flagOption['title']);
        $tableRow[] = $flagOption['text'];
	    $tableRow[] = $visibleLink;

        /*
        if (!$this->_objSecurity->canUserWriteSection($flagOption['id'])){
            $editIcon = '';
            $deleteIcon = '';
            $visibleLink = '';
        }
        */
        
        $tableRow[] = '<nobr>'.$editIcon.$deleteIcon.'</nobr>';
        
	    $table->addRow($tableRow, $oddOrEven);
        
	    $rowcount = ($rowcount == 0) ? 1 : 0;
        
	}
}else{
    $table->startRow();
    $table->addCell('<div class="noRecordsMessage">'.$objLanguage->languageText('mod_cmsadmin_no_flag_options_found', 'cmsadmin').'</div>', '', 'top', 'center', '', 'colspan="4"');
    $table->endRow();
}

$frm_select = new form('select', $this->uri(array('action' => 'select'), 'cmsadmin'));
$frm_select->id = 'select';

$objLayer = new layer();
$objLayer->id = 'listTable';
$objLayer->str = $table->show();

$frm_select->addToForm($objLayer->show());

$addEmailText = $this->objLanguage->languageText('mod_cmsadmin_flag_add_email', 'cmsadmin');

$h3->str = '<hr>'. $this->objLanguage->languageText('mod_cmsadmin_flag_email', 'cmsadmin');
$frm_select->addToForm('<p>' . $h3->show() . "<a id='btn_add_email' href='#'>$addEmailText</a> </p>");

$tableEmail = new htmltable();
$tableEmail->cellspacing = '2';
$tableEmail->cellpadding = '5';

$tableEmail->startHeaderRow();
$tableEmail->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_word_name', 'cmsadmin'));
$tableEmail->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_word_email', 'cmsadmin'));
$tableEmail->addHeaderCell($this->objLanguage->languageText('word_options'));
$tableEmail->endHeaderRow();

$rowcount = 0;

if (!isset($arrEmail)) {
    $arrEmail = array();
}

//setup the tables rows  and loop though the records
if (count($arrEmail) > 0) {
    foreach($arrEmail as $email) {
        //Set odd even row colour
        $oddOrEven = ($rowcount == 0) ? "even" : "odd";

        //$emailThumb = '<img src="'.$email['image'].'" width="100px" height="70px"/>';

        //Set up select form
        $objCheck = new checkbox('arrayList[]');
        $objCheck->setValue($email['id']);
        $objCheck->extra = "onclick=\"javascript: ToggleMainBox('select', 'toggle', this.checked);\"";

        //Trash
        $objIcon->setIcon('bigtrash');
        $deleteIcon = "<a id='btn_del_mail_{$email['id']}' title='Delete' href='javascript:void(0)'>".$objIcon->show()."</a>";

        $innerHtml = $this->_objUtils->getDeleteConfirmForm($email['id'], 'flagemail');
        $this->_objBox->setHtml($innerHtml);
        $this->_objBox->setTitle('Confirm');
        $this->_objBox->attachClickEvent("btn_del_mail_{$email['id']}");


        //Edit
        $span = '<span id="'.'btn_add_mail_' . $email['id'].'">'.$objIcon->getEditIcon('#') . '</span>';
        $editIcon = $span;

        $innerHtml = $this->_objUtils->getAddEmailForm($email['id']);
        $this->_objBox->setHtml($innerHtml);
        $this->_objBox->setTitle('Edit Alert Email');
        $this->_objBox->attachClickEvent('btn_add_mail_' . $email['id']);

        $tableRow = array();
        $tableRow[] = html_entity_decode($email['name']);
        $tableRow[] = $email['email'];

        $tableRow[] = '<nobr>'.$editIcon.$deleteIcon.'</nobr>';

        $tableEmail->addRow($tableRow, $oddOrEven);

        $rowcount = ($rowcount == 0) ? 1 : 0;

    }
}else{
    $tableEmail->startRow();
    $tableEmail->addCell('<div class="noRecordsMessage">'.$objLanguage->languageText('mod_cmsadmin_no_email_found', 'cmsadmin').'</div>', '', 'top', 'center', '', 'colspan="4"');
    $tableEmail->endRow();
}

$objLayer = new layer();
$objLayer->id = 'listTable';
$objLayer->str = $tableEmail->show();

$frm_select->addToForm($objLayer->show());

$middleColumnContent .= $frm_select->show();
$middleColumnContent .= '&nbsp;'.'<br/>';

echo $middleColumnContent;

?>
