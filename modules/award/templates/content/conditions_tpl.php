<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS
* @author Nic Appleby
* @version $id$
*/

$heading = $this->newObject('htmlheading','htmlelements');
$heading->type = 2;
$heading->str = $this->objLanguage->languageText('mod_lrspostlogin_conditions', 'award');

$conditionsNote = "<span class='warning'>".
				$this->objLanguage->languageText('mod_award_conditionspercentagenote', 'award').
				"</span>";

$tabs = $this->newObject('tabcontent','htmlelements');
$condType = $this->objBenefitType->getAll("ORDER BY id");
$tabCount = 0;
foreach ($condType as $type) {
    $default = ($benefitTypeId == $type['id'])? true : false;
    $tabContent = $this->objTemplates->getConditions($type['id'], $defaultYear, $agreeTypeId, $sicId, $aggregate, $subSic);
    $tabs->addTab($type['name'], $tabContent, null, $default);
}
$tabs->width = '835px';
echo $heading->show().$conditionsNote.$tabs->show();
?>