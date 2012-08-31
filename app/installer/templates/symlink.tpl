<script language="JavaScript">
function chroot_check()
{
	var elem = document.getElementById('normal_link');
	if (elem.style.display == 'block') {
		elem.style.display = 'none';
	} else {
		elem.style.display = 'block';
	}

	elem = document.getElementById('chroot_link');
	if (elem.style.display == 'block') {
		elem.style.display = 'none';
	} else {
		elem.style.display = 'block';
	}
}
</script>

<table class="install-table">
	<tr valign="top">
		<td>
			<a href="<?php echo HELP_URL?>/create_symlinks#directory" target="_blank">
				<img src="./extra/yellow_help_off.png" border="0" alt="Help" title="Help"  />
			</a>
		</td>
		<td >
			Directory to symbolically link as 5ive web root.
		</td>
		<td >
			<?php echo $symlinkbox; ?>

		</td>

	</tr>
	<tr valign="top">
		<td>

		</td>
		<td>
			Check this box if you are on a shared host that uses chroot jails.
			The htaccess file generated needs extra options for chrooted user
			directories. This is not guaranteed to work, however it may help
			in some cases. If you are try to install on a shared host and
			encountering some problems, please visit the
			<a href="http://fsiu.uwc.ac.za/">Chisimba Forums</a> and
			look through the current threads on shared hosting installation.
		</td>
		<td><?php echo $shared_host; ?></td>
	</tr>
</table>