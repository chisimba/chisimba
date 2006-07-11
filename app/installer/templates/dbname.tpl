<?php
$objHelp = new domtt();
?>
<table class="install-table">
<tr valign="top">
	<td width="1">
	</td>
	<td width="50%">
	Database Name.
	</td>
	<td >
	<?php echo $db_name ?>
	</td>
</tr>
<tr valign="top">
	<td>
	<?php echo $objHelp->show('Select a Name','If you have chosing to re-install Chisimba, it will deleted all the tables in the database that you select');?>
	
		</td>
	<td>
	Create database. <br /><span style="color: #BB2222">(Note that this will delete and re-create ALL tables in the named database, not just 5ive tables)</span>
	</td>
	<td>
	<?php echo $create_db ?>
	</td>
	
</tr>

</table>
