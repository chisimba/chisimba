<?PHP
$treeMenu = & $this->newObject('contenttree', 'cmsadmin');
$this->appendArrayVar('headerParams', $this->getJavascriptFile('tree.js', 'cmsadmin'));
$css = '<link rel="stylesheet" type="text/css" media="all" href="'.$this->getResourceURI("tree.css", 'cmsadmin').'" />';

$this->appendArrayVar('headerParams', $css);
//Set to automatically render htmllist into tree menu
$this->appendArrayVar('bodyOnLoad', 'autoInit_trees()');


if (!isset($content)) {
    $content = '';
}



    $tree = $treeMenu->show($sectionId, TRUE, 'cmsadmin', null, 'showcmspages');

//$content = '<button onclick="parent.editfrm.input_link_reference.value=\'Hello\'; window.close()">Click here</button>';
$content .= '<br /><button onclick="javascript:opener.document.getElementById(\'input_linkreference\').value=\''.$contentId.'\';window.close()">Link</button>';
$mainContent = $content;


$myTable2=$this->newObject('htmltable','htmlelements');
$myTable2->width='700';

$myTable2->border='0';
$myTable2->cellspacing='1';
$myTable2->cellpadding='30';

//$treeLink = 'index.php?module=publicportal&action=admin&pageid=[-ID-]';
$treeLink =  $this->uri(array('action' => 'admin', 'pageid'=>'[-ID-]'), 'cmsadmin');
$myTable2->startRow();
$myTable2->addCell($tree,200,'top','left','','bgcolor="#FFFFFF" colspan="4" height="2" rowspan="2"');
$myTable2->addCell($mainContent,500,'top','left','','bgcolor="#FFFFFF" colspan="2" height="2"');
$myTable2->endRow();

echo $myTable2->show();

?>