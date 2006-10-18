<?php

/* ------------icon request template----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

$script = "
<script language='javascript'>

function updateForm(form) {
	if (form.type[0].checked) {
		form.content.style.display = 'none';
		form.moduleblock.style.display = '';
		document.getElementById('content_label').style.display = 'none';
		document.getElementById('block_label').style.display = '';
	} else {
		form.content.style.display = '';
		form.moduleblock.style.display = 'none';
		document.getElementById('content_label').style.display = '';
		document.getElementById('block_label').style.display = 'none';
	}
}

function window_loaded() {
	updateForm(document.forms['blockform']);
}

window.onload = window_loaded;

</script>
";

$objH = &$this->newObject('htmlheading','htmlelements');
$objH->type = 1;
$objH->str = $heading;

$tableHd[] = $this->objLanguage->languageText('mod_prelogin_blockinfo','prelogin');
$tableHd[] = ' ';

$table = &$this->newObject('htmltable','htmlelements');
$table->cellspacing = "2";
$table->cellpadding = "2";
$table->width = "50%";
$table->attributes = "border='0'";
$table->addHeader($tableHd,'heading','align="left"');

$this->loadClass('radio','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('textinput','htmlelements');

if (!isset($blockName)) {
	$blockName = '';
	$location = 'left';
	$blockContent = '';
	$blockType = 'block';
}
$nameInput = &new textinput('title',$blockName,null,40);
$radio = &new radio('side');
$radio->addOption('left',$objLanguage->languageText('word_left'));
$radio->addOption('middle',$objLanguage->languageText('word_middle'));
$radio->addOption('right',$objLanguage->languageText('word_right'));
$radio->setSelected($location);
$radio2 = &new radio('type');

$radio2->addOption('block','Block');
$radio2->addOption('nonblock','Content');
$radio2->extra = 'onchange="updateForm(this.form)"';
$radio2->setSelected($blockType);

$contInput = &new textarea('content',htmlentities(html_entity_decode($blockContent,ENT_QUOTES),ENT_NOQUOTES),6,37);

$objModuleBlocks = &$this->getObject('dbmoduleblocks','modulecatalogue');
$blockList = $objModuleBlocks->getBlocks();
$moduleDrop = &new dropdown('moduleblock');
$moduleDrop->addOption(NULL,$this->objLanguage->languageText('mod_prelogin_selectblock','prelogin'));
if (isset($blockList)) {
	foreach($blockList as $moduleBlock){
	    $moduleDrop->addOption($moduleBlock['moduleid']."|".$moduleBlock['blockname'],$moduleBlock['moduleid']." - ".$moduleBlock['blockname']);
	}
}
if (isset($block)) {
	$moduleDrop->setSelected("{$block['module']}|{$block['name']}");
}

$submit = &new button('editform_submit',$this->objLanguage->languageText('word_update'));
$submit->setToSubmit();
$cancel = &new button('editform_cancel',$this->objLanguage->languageText('word_cancel'));
$returnUrl = $this->uri(array('action'=>'admin'));
$cancel->setOnClick("window.location = '$returnUrl'");

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_prelogin_blockname','prelogin'),'50%');
$table->addCell($nameInput->show(),'50%');
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_prelogin_location','prelogin'),'50%');
$table->addCell($radio->show(),'50%');
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_prelogin_selectblocktype','prelogin'),'50%');
$table->addCell($radio2->show(),'50%');
$table->endRow();

$table->startRow();
$table->addCell("<div id='content_label'>".$this->objLanguage->languageText('word_content')."</div>",'50%');
$table->addCell($contInput->show(),'50%');
$table->endRow();

$table->startRow();
$table->addCell("<div id='block_label'>".$this->objLanguage->languageText('mod_prelogin_moduleblock','prelogin')."</div>",'50%');
$table->addCell($moduleDrop->show(),'50%');
$table->endRow();

$table->startRow();
$table->addCell($submit->show().' '.$cancel->show(),'50%');
$table->endRow();

$form = &new form('blockform',$this->uri(array('action'=>'submitblock')));
$form->addToForm($table);
if (isset($id)) {
	$form->addToForm(new textinput('id',$id,'hidden'));
}

$content = $script.$objH->show().$form->show();
echo $content;

?>