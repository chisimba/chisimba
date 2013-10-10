<?php

$cssLayout = $this->getObject('csslayout', 'htmlelements');
$objHead = $this->newObject('htmlheading','htmlelements');

   
$objHead->str="$heading";
$objHead->type=1;
$head = $objHead->show();

$desc = "<ul>";
foreach ($this->allForms as $form) {
  if ($this->getParam('action') == 'viewform' || $this->getParam('action') == 'submitform') {
    $action = 'viewform';
  }
  $formnumber = $form;
  $courseid = getParam('courseid');
  $link = new link($this->uri(array('action'=>$action, 'formnumber'=>$formnumber, 'courseid'=>$courseid),"ads"));
  $link->link=   "Section $form";
  $desc .= "<li>".$link->show()."</li>\n";
}
$desc .= "</ul>";

/*
$sectionA = new link ($this->uri(array('action'=>'overview'),"ads"));
$sectionA->link=   $this->objLanguage->languageText('mod_ads_section_a_overview', 'ads');

$sectionB = new link ($this->uri(array('action'=>'rules_and_syllabus'),"ads"));
$sectionB->link=   $this->objLanguage->languageText('mod_ads_section_b_rules_and_syllabus', 'ads');

$sectionC = new link ($this->uri(array('action'=>'subsidy_requirements'),"ads"));
$sectionC->link=   $this->objLanguage->languageText('mod_ads_section_c_subsidy_requirements', 'ads');

$sectionD = new link ($this->uri(array('action'=>'outcomes'),"ads"));
$sectionD->link=   $this->objLanguage->languageText('mod_ads_section_d_outcomes', 'ads');

$sectionE = new link ($this->uri(array('action'=>'resources'),"ads"));
$sectionE->link=   $this->objLanguage->languageText('mod_ads_section_e_resources', 'ads');

$sectionF = new link ($this->uri(array('action'=>'collab'),"ads"));
$sectionF->link=   $this->objLanguage->languageText('mod_ads_section_f_collab', 'ads');

$sectionG = new link ($this->uri(array('action'=>'review'),"ads"));
$sectionG->link=   $this->objLanguage->languageText('mod_ads_section_g_review', 'ads');

$sectionH = new link ($this->uri(array('action'=>'contact'),"ads"));
$sectionH->link=   $this->objLanguage->languageText('mod_ads_section_h_contact', 'ads');

$desc=
'<ul>
<li>'.$sectionA->show().'</li>
<li>'.$sectionB->show().'</li>
<li>'.$sectionC->show().'</li>
<li>'.$sectionD->show().'</li>
<li>'.$sectionE->show().'</li>
<li>'.$sectionF->show().'</li>
<li>'.$sectionG->show().'</li>
<li>'.$sectionH->show().'</li>
</ul>';

*/
$cssLayout->setLeftColumnContent($desc);
$cssLayout->setMiddleColumnContent($head.$this->getContent());

echo $cssLayout->show();
?>