<?php
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass('textarea', 'htmlelements');
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objWashout = $this->getObject('washout', 'utilities');
$objTable = new htmltable();
$objTable->width = '100%';
$objTable->border = 1;
$objTable->attributes = "rules=none frame=box";
$objTable->cellspacing = '3';
//Select Owner Home
$iconSelect = $this->getObject('geticon', 'htmlelements');
$iconSelect->setIcon('home');
$iconSelect->alt = $objLanguage->languageText("mod_eportfolio_eportfoliohome", 'eportfolio');
$mnglink = new link($this->uri(array(
    'module' => 'eportfolio'
)));
$mnglink->link = $iconSelect->show();
$linkManage = $mnglink->show();
//Heading
$objHeading->type = 1;
$objHeading->str = $objUser->fullname() . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $linkManage;
echo "<div>" . $objHeading->show() . "</div>";
//display user's names
// Spacer
$objTable->startRow();
//Get Visible MAIN blocks
$mainBlocks = $this->objEPBlocks->getVisibleBlocks('main');
//Get Visible IDENTIFICATION blocks
$identityBlocks = $this->objEPBlocks->getVisibleBlocks('identity');
//Array to store blockname
$blockname=array();
foreach ($mainBlocks as $mainBlock){
    $blockname[] = $mainBlock["blockname"];
    if($mainBlock["blockname"]=='identification'){
        foreach ($identityBlocks as $identityBlock){
            $blockname[] = $identityBlock["blockname"];
        }        
    }
}
$str = "";
foreach ($blockname as $block){
    if ("identification" == $block)
        $str .= $identification;
    elseif ("demographics" == $block)
        $str .= $demographics;
    elseif ("address" == $block)
        $str .= $address;
    elseif ("contact" == $block)
        $str .= $contact;
    elseif ("email" == $block)
        $str .= $email;
    elseif ("affiliation" == $block)
        $str .= $affiliation;
    elseif ("goals" == $block)
        $str .= $goals;
    elseif ("interests" == $blockname)
        $str .= $interests;
    elseif ("qualifications" == $block)
        $str .= $qualifications;
    elseif ("transcripts" == $block)
        $str .= $transcripts;
    elseif ("activities" == $block)
        $str .= $activities;
    elseif ("competencies" == $block)
        $str .= $competencies;
    elseif ("competencies" == $block)
        $str .= $competencies;
    elseif ("reflections" == $block)
        $str .= $reflections;
    elseif ("assertions" == $block)
        $str .= $assertions;
}
$objTable->addCell($str, Null, 'top', 'right');
$objTable->endRow();
$objTable->startRow();
//$objTable->addCell('&nbsp;');
$objTable->addCell($linkManage, Null, 'top', 'left');
$objTable->endRow();
echo $objTable->show();
?>
