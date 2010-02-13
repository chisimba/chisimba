<?php
	$objSysConfig  = $this->getObject('altconfig','config');
	$objExtJS = $this->getObject('tabs','extjs');
	$objExtJS->getExtjsResource();

	$this->appendArrayVar('headerParams', '
        	<script type="text/javascript">
        	var baseuri = "'.$objSysConfig->getsiteRoot().'index.php";
        	</script>');

	$ext =$this->getJavaScriptFile('Ext.data.country.js', 'useradmin');
	$ext .=$this->getJavaScriptFile('CheckColumn.js', 'useradmin');
	$ext .=$this->getJavaScriptFile('Ext.ux.grid.Search.js', 'useradmin');
	$ext .=$this->getJavaScriptFile('edituser.js', 'useradmin');
	$ext .=$this->getJavaScriptFile('adduser.js', 'useradmin');
	$ext .=$this->getJavaScriptFile('useradmin.js', 'useradmin');	
	
    $this->appendArrayVar('headerParams', $ext);

	echo '<div id="user-grid"></div>	</p>';
?>
