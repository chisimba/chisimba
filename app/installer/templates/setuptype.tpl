<?php
$objHelp = new domtt();
?>
<table class="install-table">
<tr valign="top">
	<td><?php echo $objHelp->show('Install Chisimba','Select Install to create a new installation');?>
	
	</td>
	<td>
	<?php echo $install ?>
	</td>
	<td width="100%">
	Install
	</td>
</tr>
<tr valign="top">
	<td><?php echo $objHelp->show('Re-install Chisimba','Select Re-install to re-install Chisiba <p /> [For first time installation, this option will be disabled]');?>
	
	</td>
	<td>
	<?php echo $update ?>
	</td>
	<td>
	Re-install. 
	</td>
	
</tr>
<tr valign="top">
	<td>
	<?php echo $objHelp->show('Uninstall Chisimba','Select Unistall to remove Chisiba <p /> [For first time installation, this option will be disabled]');?>	</td>
	<td>
	<?php echo $repair ?>
	</td>
	<td>
	Uninstall. 
	</td>	
</tr>

</table>