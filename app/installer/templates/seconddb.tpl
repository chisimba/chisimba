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
			<?php echo $objHelp->show('Select a secondary (read-only) database user','A read-only user will have limited permissions on the database, and is used to create views only. This user is unable to write any data to the database.');?>
		</td>
		<td >
			Use these values for the read-only database user?
		</td>
		<td>
			<?php echo $use_old ?>
		</td>
	</tr>

</tr>
</table>
