<?php
$objHelp = new domtt();
?>
<table class="install-table">
	<tr valign="top">
		<td width="1">
			
		</td>
		<td >
			Current Database Username : 
		</td>
		<td >
			<?php echo $db_user ?>
		</td>
	</tr>
	<tr valign="top">
		<td>
		</td>
		<td>
			Current Database Password : 
		</td>
		<td>
			<?php echo str_pad('', strlen($db_pass), '*'); ?>
		</td>
	</tr>
	
	<tr valign="top">
		<td>
			<?php echo $objHelp->show('Select a secondary database user','<h1>PAUL please give more help this please</h1>');?>
		</td>
		<td >
			Use these values for the secondary user? 
		</td>
		<td>
			<?php echo $use_old ?>
		</td>
	</tr>
	
</tr>
</table>
