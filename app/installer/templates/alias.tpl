<h4>
	These Alias directives should be copied into your httpd.conf file (if
	running virtual hosts, they need to be placed in the VirtualHost section
	of your httpd.conf file that corresponds to the website Chisimba
	will be running). Apache will need to be restarted before these settings
	take effect, however you should wait until installation is complete before
	restarting.
</h4>
<p>
	From the information given, the following appear to be the correct Alias directives for your server.

</p>
<pre class="sample-code">
Alias "<?php echo $web_path ?>/__data"        "<?php echo $system_root ?>/data/public"
Alias "<?php echo $web_path ?>/__lib"         "<?php echo $system_root ?>/core/lib"
Alias "<?php echo $web_path ?>/__fudge"       "<?php echo $system_root ?>/fudge"
Alias "<?php echo $web_root ?>"               "<?php echo $system_root ?>/core/web/index.php<?php if ($web_root=='/') { echo '/';} ?>"
</pre>

<p>
	These settings will redirect any request to "http://<?php echo $_SESSION['site_url'] ?>" to
	"<?php echo $system_root ?>/core/web/index.php". The installer will attempt to check these settings
	when you click next, however install can proceed without the check being
	done.
</p>



