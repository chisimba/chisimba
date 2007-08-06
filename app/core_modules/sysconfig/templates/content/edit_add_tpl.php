<?php
//Create page header
$pgTitle = $this->getObject('htmlheading', 'htmlelements');
$pgTitle->type = 1;
if($mode == 'edit'){
$pgTitle->str = $this->objLanguage->languageText("mod_sysconfig_edtxt",'sysconfig');
}else {
$pgTitle->str = $this->objLanguage->languageText("mod_sysconfig_addtxt",'sysconfig');
}
$title = $pgTitle->show();
?>
<style type="text/css">
<!--
.steplayout {
	font-family: Arial, Helvetica, sans-serif;
	font-style: normal;
	line-height: normal;
	background-color: #FFFFCC;
	color: #666666;
	border: thin dotted #FF6600;
}
.infocell {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	background-color: #F2EBD2;
}
-->
</style>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="3" class="steplayout">
  <tr>
    <td><h3><?php echo $title ?></h3></td><td width="32%" rowspan="2" valign="top" class="infocell"><?php echo $step ?></td>
  </tr>
  <tr>
    <td width="68%" height="300" valign="top">
	
	<?php
    if (isset($str)) {
      echo $str;
    }
	?>
	
	</td>
  </tr>
</table>
