<?php
/**
* @package cmsadmin
* @version 0.1
* @author Charl Mert
*/

echo $this->getJavascriptFile('selectbox.js');

echo $header.$headShow;
?>
<br/>
<TABLE>
<?php if( $confirm ) { ?>
<TR>
    <TD class='confirm'><?php echo $confirmMsg; ?></TD>
</TR>
<?php } ?>
</TABLE>

<?php echo $frmEdit->show(); ?>

