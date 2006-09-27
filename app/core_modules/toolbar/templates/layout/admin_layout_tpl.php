<?
/**
* @package toolbar
*/

/**
* Layout template for the test module
*/

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$leftMenu =& $this->newObject('usermenu','toolbar');
$rightMenu =& $this->getObject('userLoginHistory','security');
$tabpane =& $this->newObject('tabpane', 'htmlelements');
$objTable =& $this->newObject('htmltable', 'htmlelements');
$tab =& $this->newObject('tabbedbox', 'htmlelements');
$nav = $rightMenu->getnowLogin();
$arrayNav = array('current'=>$this->objLanguage->languageText('mod_loggedin','toolbar'));
$tableRow=array();
foreach($arrayNav as $category=>$items){
        // set up table and column widths
        $objTable->init();
        $objTable->width='100%';

        $objTable->startHeaderRow();
        $objTable->addHeaderCell($this->objLanguage->languageText('word_username'),'40%');
        $objTable->addHeaderCell($this->objLanguage->languageText('word_userid'),'40%');
        $objTable->addHeaderCell($this->objLanguage->languageText('word_laston'),'40%');
        $objTable->endHeaderRow();

        $objTable->startRow();
        $i = 0;
        if(!empty($items)){
            // Items (modules) in each category
            foreach($nav as $key){
                if($i++ % 3 == 0){
                    $objTable->endRow();
                    $objTable->addRow(array('&nbsp;'));
                    $objTable->startRow();
                }
				// if the link text is specified
				
                if(isset($key['username']) && !empty($key['userid'])){
                    $tableRow[] = $key['username'];
                    $tableRow[] = $key['userid'];
                    $tableRow[] = $key['laston'];
                }

                $objTable->addRow($tableRow, '', 'bottom', 'center');
            }
            $objTable->endRow();
			
        }
        $tab->tabbedbox();
        $tab->addTabLabel($items);
        $tab->addBoxContent($objTable->show());
       
        //$tabpane->addTab(array('name'=>$category,'url'=>'http://localhost','content' => $objTable->show()),'luna-tab-style-sheet');
    }

//set columns to 2
$cssLayout->setNumColumns(3);
$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($this->getContent());
$cssLayout->setRightColumnContent( $tab->show());

echo $cssLayout->show();
?>