<?php

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
if(isset($id))
{
	$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('mod_cmsadmin_edittemplateitem', 'cmsadmin');	
}
else {
	$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('mod_cmsadmin_templateitem', 'cmsadmin').':'.'&nbsp;'.$this->objLanguage->languageText('word_new');
}

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

$display = '<p>'.$header.$headShow.'</p><hr />';
//Show Header
echo $display;
// Show Form
echo "<br><br><br><br><br>";
echo $templateForm;

?>
