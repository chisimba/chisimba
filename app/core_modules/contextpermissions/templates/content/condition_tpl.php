<H1><?php echo $title.$this->lnkIcnCreate('create_condition'); ?></H1>
<DIV><?php

$viewCondition = $this->getObject('viewcondition','decisiontable');
$viewCondition->connect( $condition );
extract($viewCondition->elements());

$ddbType = $this->newObject( 'dropdown', 'htmlelements' );
$ddbType->dropdown('type');
foreach( $condition->objConditionType->getAll() as $option ) {
    $ddbType->addOption( $option['name'], $option['name'] );
}
$ddbType->setSelected( $condition->_function );
$ddbType->extra = 'onChange="javascript:document.frmCond[\'button\'].value=\'save\';document.frmCond.submit();"';
$lnkSave = $this->newObject('link', 'htmlelements');
$lnkSave->href = "#";
$lnkSave->extra = 'onclick="javascript:document.frmCond[\'button\'].value=\'save\';document.frmCond.submit();"';
$lnkSave->link = $this->objLanguage->languageText("word_save");
$lnkBack = $this->newObject('link', 'htmlelements');
$lnkBack->href = '#';
$lnkBack->extra = 'onclick="javascript:document.frmCond[\'button\'].value=\'cancel\';document.frmCond.submit();"';
$lnkBack->link = $this->objLanguage->languageText("word_back");
$arrControls = array( $lnkSave->show(), $lnkBack->show() );

$lblConditionType = $this->objLanguage->code2Txt('mod_contextpermissions_lblConditionType','contextpermissions');
$objLabel = &$this->getObject('label', 'htmlelements');
$objLabel->label( $lblConditionType, 'input_type' );
$lblType = $objLabel->show();

$frmCond = $this->newObject('form','htmlelements');
$frmCond->name = 'frmCond';
$frmCond->displayType = '3';
$frmCond->action = $this->uri ( array( 'action' => 'condition_form' ) );
$frmCond->addToForm("<input type=hidden name=button value=''>\n");
$frmCond->addToForm("<input type=hidden name=id value=$id>\n");

$frmCond->addToForm("<DIV id=blog-content>\n");
$frmCond->addToForm("    <DIV id=formline>\n");
$frmCond->addToForm("        <DIV id=formlabel>$lblType</DIV>\n");
$frmCond->addToForm("        <DIV id=formelement>".$ddbType->show()."</DIV>\n");
$frmCond->addToForm("    </DIV>\n");
$frmCond->addToForm("    <DIV id=formline>\n");
$frmCond->addToForm("        <DIV id=formlabel>$lblName</DIV>\n");
$frmCond->addToForm("        <DIV id=formelement>$element</DIV>\n");
$frmCond->addToForm("    </DIV>\n");
$frmCond->addToForm("</DIV>\n");
$frmCond->addToForm("<DIV id=blog-footer>\n");
$frmCond->addToForm( implode( ' / ', $arrControls ) );
$frmCond->addToForm("</DIV>\n");

if( $msg ) {
    $timer = $this->getObject('timeoutmessage','htmlelements');
    $timer->setMessage($msg);
    echo $timer->show();
}
echo $frmCond->show();
?>
