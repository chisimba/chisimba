<?php
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package groupadmin
* @subpackage template
* @version 0.1
* @since 22 November 2004
* @author Jonathan Abrahams
* @filesource
*/
?>
<SCRIPT LANGUAGE="JavaScript" SRC="modules/groupadmin/resources/selectbox.js"></SCRIPT>
<H1><?php echo $ttlEditGroup.":&nbsp;".$fullPath ?></H1>

<TABLE>
<?php if( $confirm ) { ?>
<TR>
    <TD class='confirm'><?php echo $confirmMsg; ?></TD>
</TR>
<?php } ?>
<TR>
    <TD><?php echo $return=='context' ? $lnkContextHome->show() : $lnkBack->show(); ?></TD>
</TR>
</TABLE>

<?php echo $frmEdit->show(); ?>
