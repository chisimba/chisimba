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
<H1><?php echo $ttlDeleteGroup.':&nbsp;'.$fullPath; ?></H1>
<?php if( $confirm ) { ?>
<TABLE>

<TR>
    <TD class='confirm'><?php echo $confirmMsg; ?></TD>
</TR>

<TR>
    <TD><?php echo $lnkBack->show(); ?></TD>
</TR>

</TABLE>
<?php } else { ?>
<DIV id='blog-content'>
    <DIV class='warning'><?php echo $objLanguage->code2Txt( 'mod_groupadmin_msgDeleteGroup','groupadmin',array('GROUPNAME'=>":<B>'$fullPath'</B>" ) ); ?></DIV>
</DIV>
<DIV id='blog-footer'><?php echo $frmDeleteForm->show(); ?></DIV>
<?php } ?>