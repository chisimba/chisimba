<?php
/**
* @package
*/

/**
* The admin page grouping all administrative tools together.
* @param array $modules The list of modules for display on the page.
*/
$this->setLayoutTemplate('admin_layout_tpl.php');

// set up html elements
$this->objLanguage =& $this->getObject('language','language');
$tab =& $this->newObject('tabbedbox', 'htmlelements');
$tabpane =& $this->newObject('tabpane', 'htmlelements');
$objIcon =& $this->newObject('geticon', 'htmlelements');
$objLink =& $this->newObject('link', 'htmlelements');
$objTable =& $this->newObject('htmltable', 'htmlelements');
$objHead =& $this->newObject('htmlheading', 'htmlelements');
$objSkin =& $this->newObject('skin', 'skin');

// set up language items
$head = $this->objLanguage->languageText('mod_toolbar_siteadmin','toolbar');

// set up icon folder
$this->iconFolder = $objSkin->getSkinLocation()."_common/icons/";
$this->iconModFolder = $this->iconFolder."modules/";

$objHead->type = 1;
$objHead->str = $head;
$str = $objHead->show();

$objIcon->extra=' height="25" width="25" ';

if(!empty($modules)){
    $langArray = array('context'=>'course', 'contexts'=>'courses', 'author'=>'lecturer', 'authors'=>'lecturers', 'readonly'=>'student', 'readonlys'=>'students');
    // Admin Page Categories:
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
        if(!empty($items)){
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
				// No need to check if the icon exists, done by geticon class.
                $objIcon->setModuleIcon($icon);

                // if an action is specified for the link
                $action = array();
                if(isset($line['action']) && !empty($line['action'])){
                    $action = array('action'=>$line['action']);
                }

                $objLink->link($this->uri($action,$line['module']));

                // if the link text is specified
                if(isset($line['name']) && !empty($line['name'])){
                    $name = ucwords($this->objLanguage->code2Txt($line['name'],$line['module'], $langArray));
                }else{
                    $name = ucwords($this->objLanguage->code2Txt('mod_'.$line['module'].'_name',$line['module']));
                }

                $objLink->link = $objIcon->show().'<br />'.$name;
                $objTable->addCell($objLink->show(), '', 'bottom', 'center');
            }
            
        }
        	$objTable->endRow();
			$tab->tabbedbox();
            $tab->addTabLabel($this->objLanguage->languageText('mod_toolbar_'.$category,'toolbar'));
            $tab->addBoxContent($objTable->show());
           
            $tabpane->addTab(array('name'=>$this->objLanguage->languageText('mod_toolbar_'.$category,'toolbar'),'url'=>'http://localhost','content' => $tab->show()));
    }
}
 
echo  $tabpane->show();
?>