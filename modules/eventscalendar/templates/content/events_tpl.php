<?php
$myTable = $this->newObject('htmltable', 'htmlelements');
$objIcon = & $this->newObject('geticon', 'htmlelements');
$link = & $this->newObject('link', 'htmlelements');
$objDelIcon = & $this->newObject('geticon', 'htmlelements');

$myTable->width='60%';
$myTable->border='0';
$myTable->cellspacing='1';
$myTable->cellpadding='10';
$myTable->css_class = "";
/*    
$myTable->startHeaderRow();
$myTable->addHeaderCell('');
$myTable->addHeaderCell('');
$myTable->addHeaderCell(';');
$myTable->endHeaderRow(); 
*/
$str = '';
$test = '';
if($events)
{
    foreach ($events as $event)
    {
    	$colour = $this->_objDBCategories->getCategoryColour($event['catid']);
    	if($event['start_time'] == '')
    	{
    		$startTime = '';
    	} else {
    		$startTime = date(" g:i A", $event['start_time']);
    	}
    	
        $test .='<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#276983" style = "width: 680px;border: 1px solid #006699;margin: 0px;padding: 0px;">
			  <tr class="tableDate">
			
			    <td width="50%"><span style="color: #FFFFFF; font-weight: bold; font-size: 12px;">'.date("M d, Y (D)", $event['event_date']).'</span></td>
			    <td><div align="right" style="color: #CCCCCC; font-weight: bold; font-size: 12px;">'.$startTime.'</div></td>
			  </tr>
			</table>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="tableListings" style = "width: 680px;border: 1px solid #006699;margin: 0px;padding: 0px;" >
			  <tr>
			    <td width="12" align="left" valign="top" class="tableCategory s22"  style=" background:'.$colour.'">&nbsp;</td>
			    <td align="left" valign="top" bgcolor="#FFFDF2" style="font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #004262;
	width: 500px;
	text-align: left;
	vertical-align: middle;
	font-weight: bold;
	padding: 2px;">'.$event['title'].'<br/>
			
			    <span style="font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #383838;
	text-align: left;
	vertical-align: middle;
	font-weight: normal;">'.stripslashes($event['description']).'</span></td>
			  </tr>
			</table>';
    }
   
    $str .= $test;
    $myTable = null;
} else {
     $str .= '<div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">No Events Found</div>';
}

$style = '
<style type="text/css">
<!--
.tableListings {
	width: 680px;
	border: 1px solid #006699;
	margin: 0px;
	padding: 0px;
}
.tableDate {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
	width: 180px;
	text-align: left;
	vertical-align: middle;
	font-weight: normal;
	padding: 2px;
}
.tableTitle {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #004262;
	width: 500px;
	text-align: left;
	vertical-align: middle;
	font-weight: bold;
	padding: 2px;
}
.tableCategory {
	width: 8px;
}
.tableDescr {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #383838;
	text-align: left;
	vertical-align: middle;
	font-weight: normal;
}
.tableTime {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #295569;
	font-weight: normal;
}
.newDate {color: #FFFFFF; font-weight: bold; font-size: 12px; }
.newTime {color: #CCCCCC; font-weight: bold; font-size: 12px; }
-->
</style>
<style type="text/css">
<!--
.style21 {color: #999999}
.style34 {color: #94413F}
.style35 {color: #1E4E82}
.style39 {
	color: #FF0000;
	font-style: italic;
}
.style41 {color: #FF0000; font-style: italic; font-weight: bold; }
.style43 {color: #4D6FAE}
.style45 {color: #FF0000}
.headerBorder {	border: 1px solid #86A6CC;
}
.headerBorderShadow {	border-top-width: 1px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-top-style: none;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: none;
	border-top-color: #006699;
	border-right-color: #006699;
	border-bottom-color: #006699;
	border-left-color: #006699;
}
.secutiryBorder {	border: 1px solid #FFFF00;
}
.style46 {color: #000000}
.style47 {color: #EBEBEB}
-->
</style>

<!--link href="esstyle.css" rel="stylesheet" type="text/css"/-->

<style type="text/css">
<!--
.s22 {background-color: #A4CAE6;}
.s21 {background-color: #F2BFBF;}
.s23 {background-color: #CCFF00;}
.s24 {background-color: #FBF484;}
.s29999 {background-color: #FFC18A;}
-->
</style>
<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
<!--
.style37 {
	color: #FF9900;
	font-weight: bold;
}
.demoTableBorder {
	border: 1px solid #ABDD13;
}
.style49 {color: #1E4E82}
.style49 {color: #990000;
	font-weight: bold;
}
.style50 {color: #FFFFFF}
.style51 {font-size: 11px; line-height: 16px; font-family: Verdana, Arial, Helvetica, sans-serif;}
.style52 {
	color: #004F75;
	font-weight: bold;
}
-->
</style>';


  //$this->appendArrayVar('headerParams',$style);
$objH = $this->newObject('htmlheading', 'htmlelements');
$toggleLink = '';
if($calType == 'context')
{
    $objH->str = $this->_objDBContext->getMenuText().' Calendar';
    
    $link->link = 'View My Calendar';
    $link->href = $this->uri(array('type' => 'user', 'typeid' => $this->_objUser->userId()));
    $toggleLink = $link->show();
    
   
   

} else {
    $objH->str = 'My Events';
    if($this->_objDBContext->isInContext())
    {
        $link->link = 'View '.$this->_objDBContext->getMenuText().' Calendar';
        $link->href = $this->uri(array('type' => 'context' , 'typeid' => $this->_objDBContext->getContextCode()));
        $toggleLink = $link->show();
    }
}

$toggles = '<table width="100%"  border="0" cellpadding="1" cellspacing="0"  style = "width: 680px;border: 1px solid #006699;margin: 0px;padding: 0px;"><tr><td align="center">'.$toggleLink.'</td></tr></table><br/>';

echo $objH->show();


print '<center>'.$calendar.'</center><p></p>';
if($this->isContextPlugin)
{
    print '<center>'.$toggles.'</center>';
}
print '<center>'.$str.'</center>';

 $objIcon->setIcon('add');
    $link->href = $this->uri(array('action' => 'addevent', 'catid' => $catId ,'type' => $calType));
    $link->link = 'Add an Event '.$objIcon->show();



echo '<span class="icon">'.$link->show().'</span>';

$link->href = $this->uri(array('action' => 'merge'));
$link->link = 'Merge My Calendar and '.$this->_objDBContext->getTitle();

echo ' <span>'.$link->show().' </span>';
?>


