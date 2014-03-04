<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

$topNav = $this->objUi->topNav('createform');

$checkOpenerScript = '
<script type="text/javascript">
function SetUrl(filelink)
{   
    //alert(filelink);
    document.getElementById("template_thumb").src = filelink;
    document.getElementById("template_thumb_input").value = filelink;
}
</script>
';

$this->appendArrayVar('headerParams', $checkOpenerScript);

$objIcon = $this->newObject('geticon', 'htmlelements');
$tbl = $this->newObject('htmltable', 'htmlelements');
$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');
$objRound =$this->newObject('roundcorners','htmlelements');
$objIcon->setIcon('templates_small', 'png', 'icons/cms/');


if(!isset($formId)) {
    $formId = $this->getParam('id');
}

if($formId != '')
{
	$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('mod_forms_edititem', 'forms');	
}
else {
	$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('mod_forms_additem', 'forms');
}

$objLayer->str = $h3->show();
$objLayer->cssClass = 'headleft';
$header = $objLayer->show();

$objLayer->str = $topNav;
$objLayer->cssClass = 'headright';
$header .= $objLayer->show();

$objLayer->str = '';
$objLayer->cssClass = 'headclear';
$headShow = $objLayer->show();

$display = '<p>'.$header.$headShow.'</p><hr />';
//Show Header
$middleColumnContent = $display;
// Show Form

$middleColumnContent .= $formDisplay;

$objFormsTree =$this->newObject('simpletreemenu', 'forms');

$this->setVar('leftContent', $objFormsTree->show());
$this->setVar('middleContent', $middleColumnContent);


?>
