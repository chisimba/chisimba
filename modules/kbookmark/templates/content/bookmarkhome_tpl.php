<?php
    $objSysConfig  = $this->getObject('altconfig','config');
    $objExtJS = $this->getObject('extjs','ext');
    $objExtJS->show();

    $this->appendArrayVar('headerParams', '
            <script type="text/javascript">
            var baseuri = "'.$objSysConfig->getsiteRoot().'index.php";
			var defId = "root'.$userId.'";
			var button = false;
            </script>');
	
	$ext = '<link rel="stylesheet" href="'.$this->getResourceUri('iconcss.css', 'kbookmark').'" type="text/css" />';
    $ext .= $this->getJavaScriptFile('bookmark.js', 'kbookmark');
	$ext .= $this->getJavaScriptFile('mainpanel.js', 'kbookmark');
	
	        
    $ext .= "
	<style>
	#mainpanel{
	margin-left: auto ;
    margin-right: auto ;
	border:1px solid #c3daf9;
	overflow:auto;
	}
	
	html, body {
	font:normal 12px verdana;
	margin:0;
	padding:0;
	border:0 none;
	overflow:hidden;
	height:100%;
	}
	</style>";
	
	$this->appendArrayVar('headerParams', $ext);

    echo '<br/><div id="mainpanel"></div>';
?>
