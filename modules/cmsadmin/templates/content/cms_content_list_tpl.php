<?php
/**
 * This template will show all content together with its sections and categories
 */

//initiate objects
$table = & $this->newObject('htmltable', 'htmlelements');
$h3 = &$this->newObject('htmlheading', 'htmlelements');
$objIcon = & $this->newObject('geticon', 'htmlelements');
$link = & $this->newObject('link', 'htmlelements');

//create a heading
$h3->str = $this->objLanguage->languageText('mod_cmsadmin_contentitemsmanager', 'cmsadmin').'&nbsp;'.$objIcon->getAddIcon($this->uri(array('action' => 'addcontent')));

//counter for records
$cnt = 1;

//init mode
$mode = '';

//check for filter
if($this->getParam('filter') == 'trash') {
    $mode = 'trash';
}

//get the pages
if($this->inContextMode) {
    $objContextContent = $this->newObject('dbcontextcmscontent', 'contextcmscontent');
    $arrPages = $objContextContent->getContextPages($this->contextCode);
} else {
    $arrPages = $this->_objContent->getContentPages($this->getParam('filter'));
}
//


//setup the table headings
$table->startHeaderRow();
$table->addHeaderCell('#');
$table->addHeaderCell('Title');
$table->addHeaderCell('Front Page');
$table->addHeaderCell('Published');
//$table->addHeaderCell('Reorder');
//$table->addHeaderCell('Order');
$table->addHeaderCell('Access');
$table->addHeaderCell('Section');
//$table->addHeaderCell('Category');
$table->addHeaderCell('Author');
$table->addHeaderCell('Date');


if($mode == 'trash')
{
    $link->href = $this->uri(array('action' => 'content'));
    $link->link = 'Content';
} else
{
    $link->href = $this->uri(array('action' => 'trashcontent', 'filter' => 'trash'));
    $link->link = 'Recycle Bin';
}

$table->addHeaderCell($link->show());
$table->endHeaderRow();

$rowcount = 0;

//setup the tables rows  and loop though the records
if ($arrPages)
{
    foreach($arrPages as $page) {
        $oddOrEven = ($rowcount == 0) ? "odd" : "even";
        //$table->startRow();
        $tableRow = array();
        $tableRow[]=$cnt++;



        $link->link = $page['title'];
        $link->href = $this->uri(array('action' => 'addcontent', 'mode' => 'edit', 'id' => $page['id']));

        $tableRow[]=$link->show();
        $tableRow[]=$this->_objUtils->getCheckIcon($this->_objFrontPage->isFrontPage($page['id']), FALSE);

        //the publish link
        $link->href = $this->uri(array('action' => 'contentpublish', 'id' => $page['id']));
        $link->link = $this->_objUtils->getCheckIcon($page['published']);

        $tableRow[]= $link->show(); //$this->_objUtils->getCheckIcon($page['published'], TRUE);
        //  $table->addCell('up down');
        //$table->addCell($page['ordering']);
        $tableRow[]='<span class="subdued">'.$this->_objUtils->getAccess($page['access']).'</span>';
        $tableRow[]='<b>'.$this->_objSections->getMenuText($page['sectionid']).'</b>';
        //$tableRow[]=$this->_objCategories->getMenuText($page['catid']);
        $tableRow[]='<span class="subdued">'.$this->_objUser->fullname($page['created_by']).'</span>';
        $tableRow[]='<span class="subdued">'.$this->_objUtils->formatShortDate($page['created']).'</span>';

        //trash or delete link depending on the mode
        if($mode == 'trash') {
            $link->href = $this->uri(array('action' => 'deletecontent', 'id' => $page['id']));
        } else {
            $link->href = $this->uri(array('action' => 'trashcontent', 'id' => $page['id']));
        }

        $objIcon->setIcon('delete');
        $link->link = $objIcon->show();
        $tableRow[] = $link->show();

        //$table->endRow();
        $table->addRow($tableRow, $oddOrEven);
        $rowcount = ($rowcount == 0) ? 1 : 0;



    }

    $str = $table->show();
} else
{
    if($mode == 'trash') {
        $txt = 'Recycling Bin is Empty';
    } else {
        $txt = 'No Pages Found';
    }
    $str = '<div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">'.$txt.'</div>';
}

//print out the page
print $h3->show();
if($mode == 'trash')
{
    print '<h3> Recycling Bin</h3>';
}
print $str;


?>
