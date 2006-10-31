<?php
/**
* @package contextadmin
*/

/**
* The lecturers page grouping all the lecturers tools together.
* @param array $modules The list of modules for display on the page.
*/

// set up html elements
$this->objLanguage =& $this->getObject('language','language');
$tab =& $this->newObject('tabbedbox', 'htmlelements');
$objIcon =& $this->newObject('geticon', 'htmlelements');
$objLink =& $this->newObject('link', 'htmlelements');
$objTable =& $this->newObject('htmltable', 'htmlelements');
$objHead =& $this->newObject('htmlheading', 'htmlelements');
$objSkin =& $this->newObject('skin', 'skin');

// language items
$heading = ucwords($this->objLanguage->code2Txt('mod_contextadmin_contextmanagement','contextadmin'));

$str = '';

$objHead->str = $heading;
$objHead->type = 1;

$str .= $objHead->show();

// set up icon folder
$this->iconFolder = $objSkin->getSkinLocation()."icons/";
$this->iconModFolder = $this->iconFolder."modules/";

//$objIcon->extra=' height=25 width=25 ';

if(!empty($modules)){
    $langArray = array('context'=>'course', 'contexts'=>'courses', 'author'=>'lecturer', 'authors'=>'lecturers', 'readonly'=>'student', 'readonlys'=>'students');
    // Lecturer Page Categories:
    foreach($modules as $category=>$items){
        // set up table and column widths
        $objTable->init();
        $objTable->width='99%';

        $objTable->startRow();
        $objTable->addCell('', '33%');
        $objTable->addCell('', '33%');
        $objTable->addCell('', '33%');
        $objTable->endRow();

        $objTable->startRow();
        $i = 0;
        // Items (modules) in each category
        foreach($items as $key=>$line){
            if($i++ % 3 == 0){
                $objTable->endRow();
                $objTable->addRow(array('&nbsp;'));
                $objTable->startRow();
            }

            // If the icon is specified.
            if(isset($line['icon']) && !empty($line['icon'])){
                $icon = $line['icon'];
            }else{
                $icon = $line['module'];
            }

            // Check the icon exists or use the default.
            if(file_exists($this->iconModFolder.$icon.'.gif')){
                $objIcon->setModuleIcon($icon);
            }else if(file_exists($this->iconFolder.$icon.'.gif')){
                $objIcon->setIcon($icon);
            }else{
                $objIcon->setModuleIcon('default');
            }

            $action = array();
            if(isset($line['action']) && !empty($line['action'])){
                $action = array('action'=>$line['action']);
            }

            $objLink->link($this->uri($action,$line['module']));

            if(isset($line['name']) && !empty($line['name'])){
                $name = ucwords($this->objLanguage->code2Txt($line['name'], $langArray));
            }else{
                $name = ucwords($this->objLanguage->code2Txt('mod_'.$line['module'].'_name'));
            }

            $objLink->link = $objIcon->show().'<br />'.$name;
            $objTable->addCell($objLink->show(), '', 'bottom', 'center');
        }
        $objTable->endRow();

        $tab->tabbedbox();
        $tab->addTabLabel($this->objLanguage->languageText('mod_contextadmin_'.$category,'contextadmin'));
        $tab->addBoxContent($objTable->show());
        $str .= $tab->show();
    }
}

$this->objIcon->extra='';
$this->setVar('footerStr', $this->getContextLinks().$this->getContentLinks());
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$leftMenu =& $this->newObject('contextmenu','toolbar');

$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($str);

echo $cssLayout->show();
?>