<div id="licencewrap">
<div id="licence" style="width: 100%; height: 200px; overflow: auto; border-bottom: 1px solid Black;">
<pre style="font-family: monospace; font-size: 11px;">
<?php
echo file_get_contents(INSTALL_DIR.'/LICENCE');
?>
</pre>

</div>

<p>Yes I have read the terms in the licence agreement <?php echo $licence_check ?> </p>
<p><em>Note that you can use ALT+S as a shortcut for the "Next" button throughout
the install process</em></p>  
</div>
