<?php
// Unset default page variables
$this->setVar('pageSuppressIM', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
$objHead=$this->newObject('htmlheading','htmlelements');
$objHead->type=3;
$objHead->str=$this->objLanguage->languageText('mod_assignment_assignmentname','assignment').': '.$assignment['name'];
echo
    "<div align='center'>"
        .$objHead->show()
        .'<p><b>'.$this->objLanguage->languageText('mod_assignment_assignmentdescription','assignment').':</b> '.$assignment['description'].'</p>'
        .'<p><b>'.$this->objLanguage->languageText('mod_assignment_lecturerscomment','assignment').':</b> '.$submission['commentinfo'].'</p>'
    ."</div>";
$this->objIcon->setIcon('close');
$this->objIcon->extra=" onclick='javascript:window.close()'";
$objInnerLayer = $this->newObject('layer','htmlelements');
$objInnerLayer->align='center';
$objInnerLayer->str=$this->objIcon->show();
$objLayer = $this->newObject('layer','htmlelements');
$objLayer->cssClass='content';
$objLayer->border='1px solid';
$objLayer->align='center';
$objLayer->str= $objInnerLayer->show();
echo $objLayer->show();
?>