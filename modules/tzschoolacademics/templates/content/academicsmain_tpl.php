<?php
/* 
 * The menu template for the academic module home page
 *
 *  @author charles mhoja
 *   @email charlesmdack@gmail.com
 */


// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
die("You cannot view this page directly");
}

include_once 'academics_home_menu_tpl.php';
$home_layoutObj=$this->newObject('csslayout', 'htmlelements');;
$home_layoutObj->setNumColumns(2);
$home_nav=  show_academic_home_menus();
$home_layoutObj->setLeftColumnContent($home_nav);
$home_layoutObj->setMiddleColumnContent('');

echo $home_layoutObj->show();  //displaying the template




/////before
/*
//$sideMenu=$this->getObject('academicsidemenu');
$layout=$this->getObject('cssLayout','htmlelements');
/*
$menuContent=array(
    array(
        'header'=>'head',
        'items'=>array(
            array('Reports','tzschoolacademics'),
            array('kamenu','kalink')
        )
    )
);

$sideMenu->loadMenu($menuContent);
 * 
 
$layout->setLeftColumnContent($sideMenu->show());
echo $layout->show();
*/
?>
