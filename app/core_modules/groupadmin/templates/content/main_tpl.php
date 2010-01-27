<?php  
//do the check to check if TII is accessable



	$objSysConfig  = $this->getObject('altconfig','config');
	$objExtJS = $this->getObject('extjs','ext');
	$objExtJS->show();
	//$ext =$this->getJavaScriptFile('ext-3.0-rc2/ext-all-debug.js', 'htmlelements');
	$ext =$this->getJavaScriptFile('ColumnNodeUI.js', 'groupadmin');
	$ext .=$this->getJavaScriptFile('Ext.ux.grid.Search.js', 'groupadmin');
	$ext .=$this->getJavaScriptFile('users.js', 'groupadmin');
	$ext .=$this->getJavaScriptFile('interface.js', 'groupadmin');


	//$ext .=$this->getJavaScriptFile('ext-3.0-rc2/examples/shared/examples.js', 'htmlelements');
	
	//setup the dynamicuri
	$this->appendArrayVar('headerParams', '
	        	<script type="text/javascript">	        		
	        		var baseUri = "'.$objSysConfig->getsiteRoot().'index.php";
	        	</script>');

/*			$extbase_js = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
		
			$extall_js = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js', 'htmlelements').'" type="text/javascript"></script>';
		
			$extall_css = '<link rel="stylesheet" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css', 'htmlelements').'" type="text/css" />';
		
			$this->appendArrayVar('headerParams', $extbase_js);
			$this->appendArrayVar('headerParams', $extall_js);
			$this->appendArrayVar('headerParams', $extall_css);
	
	$ext .= '<link rel="stylesheet" href="'.$this->getResourceUri('ColumnNodeUI.css', 'groupadmin').'" type="text/css" />';
	$ext .= '<link rel="stylesheet" href="skins/_common/css/extjs/silk/silk.css" type="text/css" />';
	$ext .= '<link rel="stylesheet" href="skins/_common/css/extjs/menus.css" type="text/css" />';
	$ext .= '<link rel="stylesheet" href="skins/_common/css/extjs/buttons.css" type="text/css" />';
	$ext .= '<link rel="stylesheet" href="skins/_common/css/extjs/DarkGrayTheme/css/xtheme-darkgray.css" type="text/css" />';
	*/
	$ext .= "<style>
	
			#main-interface{
				padding:10px;
				margin:10px;
			}
			pre {
   				font-size:11px; 
			}
			
			.x-tab-panel-body .x-panel-body {
			    padding:10px;
			}
			
			/* default loading indicator for ajax calls */
			.loading-indicator {
				font-size:8pt;
				background-image:url('../../resources/images/default/grid/loading.gif');
				background-repeat: no-repeat;
				background-position: left;
				padding-left:20px;
			}
			
			.new-tab {
			    background-image:url(../feed-viewer/images/new_tab.gif) !important;
			}
			
			
			.tabs {
			    background-image:url( ../desktop/images/tabs.gif ) !important;
			}
			
			p { width:650px; }

			
			</style>";
 $this->appendArrayVar('headerParams', $ext);


 
?>

<center><div id="mainPanel"></div></center>
<div id="combo"></div>
