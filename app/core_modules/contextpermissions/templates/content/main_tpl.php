<DIV><?php
// Get all modules
$objModAdmin = &$this->getObject('modules','modulecatalogue');
$modules = $objModAdmin->getAll('ORDER BY module_id');
$ddbModules = $this->newObject( 'dropdown', 'htmlelements' );
$ddbModules->dropdown('module_name');
$ddbModules->extra = ' onChange="document.frmMod.submit();"';
foreach ( $modules as $aModule ) {
    $ddbModules->addOption( $aModule['module_id'], $aModule['module_id'] );
}
$ddbModules->setSelected( $this->getSession('module_name') );

// Select a module:
$lblSelectModule = &$this->objLanguage->languageText("mod_contextpermissions_lblSelectModule",'contextpermissions',"[Select a module: ]");
$objLabel = &$this->getObject( 'label', 'htmlelements' );
$objLabel->label( $lblSelectModule, 'input_module_name' );

$frmMod = $this->newObject( 'form', 'htmlelements' );
$frmMod->action = $this->uri( array('action'=>'show_main') );
$frmMod->name ='frmMod';
$frmMod->addToForm( '<H1>'.$title.' </H1>' );
$frmMod->addToForm( '<P>'.$objLabel->show().'&nbsp;'.$ddbModules->show().'</P>' );
echo $frmMod->show();

// Initialize links
$lnkCreateAction = $this->lnkText($lblCreateAction,'create_action',NULL);
$lnkCreateRule = $this->lnkText($lblCreateRule,'create_rule',NULL);
$lnkCreateCondition = $this->lnkText($lblCreateCondition,'create_condition',NULL);
$lnkGenerateConfig = $this->lnkText($lblGenerateConfig,'generate_config',NULL);
$lnkUpdatePermissions = $this->lnkText($lblUpdatePerms,'update_perms',NULL);
$lnkGetControllerActions = $this->lnkText($lblControllerActions,'controller_actions',NULL);

$properties= array();
$properties['lblAction'] = $lblAction;
$properties['lblRule'] = $lblRule;
$properties['lblCondition'] = $lblCondition;
$properties['colWidth'] = '10%';

$objViewGrid = &$this->getObject( 'editGrid','contextpermissions');
$objViewGrid->connect( $this->objDecisionTable, $properties );
$objViewGrid->name = $this->getParam('id');
$objViewGrid->class = $this->getParam('class');
echo $objViewGrid->show();

echo "<P>".implode( ' / ', array( $lnkCreateAction,$lnkCreateRule, $lnkCreateCondition, $lnkGetControllerActions, $lnkGenerateConfig, $lnkUpdatePermissions ))."</P>";

?></DIV>