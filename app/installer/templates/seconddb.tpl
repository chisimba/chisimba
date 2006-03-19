
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
			<a href="<?php echo HELP_URL?>/create_second_user#useold" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
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
