<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package contextgroups
* @subpackage template
* @version 0.1
* @since 15 February 2005
* @author Jonathan Abrahams
* @filesource
*/
?>
<?php
// Page headers and layout template
$this->setLayoutTemplate('contextgroups_layout_tpl.php');
$this->appendArrayVar('headerParams',$this->getJavascriptFile('selectbox.js','groupadmin') );
?>
<?php
    $tblLayout = $this->newObject( 'htmltable', 'htmlelements' );
    $tblLayout->row_attributes = 'align="center"';
    $tblLayout->width = '99%';
    $tblLayout->startRow();
    $tblLayout->addCell( $lstUsers->show() );
    $tblLayout->addCell( implode('<br />',$navButtons),NULL,NULL );
    $tblLayout->addCell( $lstMembers->show() );
    $tblLayout->endRow();

    $frmManage->addToForm("<div id='blog-content'>".$tblLayout->show()."</div>" );
    $frmManage->addToForm("<div id='blog-footer'>".implode(' / ', $ctrlButtons)."</div>" );
?>
<DIV style='padding:1em;'>
        <?php  echo $frmManage->show(); ?>
</DIV>
<?php
echo $linkToContextHome;
?>