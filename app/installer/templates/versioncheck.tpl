
<table cellspacing="0" class="install-table" width="50%">

<tr>
		<td width="55%" align="left" style="border-bottom: 1px solid black;">
		<?php echo $checking;?>
		</td>
		<td width="20%" align="center" style="border-bottom: 1px solid black;">
		Minumum Version
		</td>
        <td width="20%" align="center" style="border-bottom: 1px solid black;">
        Maximum Version<br />(If applicable)
        </td>
		<td align="center" style="border-bottom: 1px solid black;">
		Result
		</td>
</tr>
<?php
foreach($required as $setting_name => $setting_details) {
?>

	<tr>
		<td>
		<?php echo $setting_name; ?>
		</td>

		<td align="center">
		<?php echo $setting_details['version']; ?>
		</td>

        <td align="center">
        <?php if(isset($setting_details['max_version'])){
            echo $setting_details['max_version'];
        }?>
        </td>

		<td  align="center">
		<?php if ($setting_details['message'] != '') {
			echo $setting_details['message'];

		}?>
		</td>

	</tr>
	<?php
}
?>

</table>
