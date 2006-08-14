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
<H1><?php echo $pageTitle; ?></H1>
<TABLE id ='confirmTimeout'>
<?php if( $confirm ) { ?>
<TR>
    <TD class='confirm'><?php echo $confirmMsg; ?></TD>
</TR>
<?php } ?>

<?php if( isset( $invalidName ) && $invalidName ) { ?>
<TR>
    <TD class='warning'><?php echo $objLanguage->code2Txt( 'mod_groupadmin_msgInvalidField','groupadmin',array('FIELDNAME'=>"'$lblName'") ); ?></TD>
</TR>
<?php } ?>

<?php if( isset( $invalidDescription ) && $invalidDescription ) { ?>
<TR id='warning2'>
    <TD class='warning'><?php echo $objLanguage->code2Txt( 'mod_groupadmin_msgInvalidField','groupadmin',array('FIELDNAME'=>"'$lblDesc'") ); ?></TD>
</TR>
<?php } ?>
</TABLE>
<?php echo $lnkBack->show(); ?>
<?php echo $form->show(); ?>
