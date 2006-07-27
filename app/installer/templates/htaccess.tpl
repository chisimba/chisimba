<p>
	Additionally, a .htaccess file will be created in the Chisimba webroot that
	directs all requests to the appropriate handlers. This requires
	that the webserver has mod_rewrite enabled. The .htaccess file contains the
	following directives :
</p>

<pre id="normal_link" class="sample-code" style="display: block;">
	Options +FollowSymLinks

	RewriteEngine On

	#Set the base uri
	RewriteBase <?php echo $symlink_dir ?>
	#Now do some rules for redirection
	#if index, don't redirect (again)
	RewriteRule index.php   - [L]
	#don't redirect double-underscore dirs
	RewriteRule __lib       - [L]
	RewriteRule __data      - [L]
	RewriteRule __fudge     - [L]
	#redirect everything else to index.php
	RewriteRule (.*)        index.php/$1 [L]
</pre>

<pre id="chroot_link" class="sample-code" style="display: none;">
	Options +SymlinksIfOwnerMatch

	RewriteEngine On

	#Set the base uri
	RewriteBase <?php echo $symlink_dir ?>
	#Now do some rules for redirection
	#if index, don't redirect (again)
	RewriteRule index.php   - [L]
	#don't redirect double-underscore dirs
	RewriteRule __lib       - [L]
	RewriteRule __data      - [L]
	RewriteRule __fudge     - [L]
	#redirect everything else to index.php
	RewriteRule (.*)        index.php/$1 [L]
</pre>
