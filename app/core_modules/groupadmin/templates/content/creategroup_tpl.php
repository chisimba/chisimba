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
<h1><?php echo $pageTitle; ?></h1>
<table id ='confirmTimeout'>
<?php if( $confirm ) { ?>
<tr>
    <td class='confirm'><?php echo $confirmMsg; ?></td>
</tr>
<?php } ?>

<?php if( isset( $invalidName ) && $invalidName ) { ?>
<tr>
    <td class='warning'><?php echo $objLanguage->code2Txt( 'mod_groupadmin_msgInvalidField','groupadmin',array('FIELDNAME'=>"'$lblName'") ); ?></td>
</tr>
<?php } ?>

<?php if( isset( $invalidDescription ) && $invalidDescription ) { ?>
<tr id='warning2'>
    <td class='warning'><?php echo $objLanguage->code2Txt( 'mod_groupadmin_msgInvalidField','groupadmin',array('FIELDNAME'=>"'$lblDesc'") ); ?></td>
</tr>
<?php } ?>
</table>
<?php echo $lnkBack->show(); ?>
<?php echo $form->show(); ?>
