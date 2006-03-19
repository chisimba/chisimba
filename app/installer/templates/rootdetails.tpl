<table class="install-table">
<tbody valign="top">
<tr valign="top">
	<td>
	<a href="<?php echo HELP_URL?>/system_details#systemname" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td width="50%">
	System Name.
	</td>
	<td width="50%" >
	<?php echo $sys_name?>
	</td>
</tr>
<tr valign="top">
	<td>
	<a href="<?php echo HELP_URL?>/system_details#servername" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td width="50%">
	Server Name.
	</td>
	<td width="50%" >
	<?php echo $serverName?>
	<input type="hidden" name="generatedName" value="FALSE" />
    </td>
</tr>
<tr valign="top">
	<td>
	<a href="<?php echo HELP_URL?>/system_details#serverlocation" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td width="50%">
	Server location?
	</td>
	<td width="50%" >
	<?php echo $serverLocation;?>
	</td>
</tr>
<tr valign="top">
	<td>
	<a href="<?php echo HELP_URL?>/system_details#systemowner" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td>
	System Owner.
	</td>
	<td >
	<?php echo $sys_owner?>
	</td>
	
</tr>
<tr valign="top">
	<td>
	<a href="<?php echo HELP_URL?>/system_details#defaultemail" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td >
	Default Email Address.
	</td>
	<td >
	<?php echo $root_email?>
	</td>
	
</tr>
<tr valign="top">
	<td>
	<a href="<?php echo HELP_URL?>/system_details#logActivity" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td >
	Log user activity?
	</td>
	<td >
	<?php echo $log_enable?>
	</td>
	
</tr>

<tr valign="top">
	<td>
	<a href="<?php echo HELP_URL?>/system_details#logpath" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td >
	Error Log Path
	</td>
	<td >
	<?php echo $log_path?>
	</td>
	
</tr>
<tr valign="top">
	
	<td>
	<a href="<?php echo HELP_URL?>/system_details#defaultpostlogin" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td >
	Default postlogin module?
	</td>
	<td>
	<?php echo $postLogin?>
	</td>
	
</tr>
<tr valign="top">
	
	<td>
	<a href="<?php echo HELP_URL?>/system_details#defaultskin" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td >
	Defaul Skin
	</td>
	<td><?php echo $defaultskin ?>
    </td>
	
</tr>
<tr>
	<td>
	<a href="<?php echo HELP_URL?>/system_details#rooturl" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td >
	Site root URL. 
	</td>
	<td >
	<?php echo $site_url ?>
	</td>
	
</tr>
<tr>
	<td>
	<a href="<?php echo HELP_URL?>/system_details#proxy" target="_blank"><img src="./extra/yellow_help_off.png" alt="Help" title="Help"  border="0" /></a>
	</td>
	<td >
	Proxy Settings. 
	</td>
	<td >
	<?php echo $proxy ?>
	</td>
	
</tr>
<?php $contentPath ?>
<?php $relContentPath ?>
</tbody>
</table>
