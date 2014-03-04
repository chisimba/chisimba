<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
 * @package eportfolio
 * @subpackage template
 * @version 0.1
 * @since 19 August 2009
 * @author Paul Mungai
 */
// Page headers and layout template
//$this->setLayoutTemplate('contextgroups_layout_tpl.php');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = '<font color="b97721">' . $objUser->fullName() . ' ' . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio') . '</font>';
echo $objHeading->show();
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("phrase_manage", 'eportfolio');
echo $objHeading->show();
$this->appendArrayVar('headerParams', $this->getJavascriptFile('selectbox.js', 'groupadmin'));
?>
<?php
$tblLayout = &$this->newObject('htmltable', 'htmlelements');
$tblLayout->row_attributes = 'align="center"';
$tblLayout->width = '99%';
$tblLayout->startRow();
$tblLayout->addCell($lstUsers->show());
$tblLayout->addCell(implode('<br />', $navButtons) , NULL, NULL);
$tblLayout->addCell($lstMembers->show());
$tblLayout->endRow();
$frmManage->addToForm("<div id='blog-content'>" . $tblLayout->show() . "</div>");
$frmManage->addToForm("<div id='blog-footer'>" . implode(' / ', $ctrlButtons) . "</div>");
?>
<DIV style='padding:1em;'>
        <?php
echo $frmManage->show(); ?>
</DIV>
<?php
//echo $linkToContextHome;

?>
