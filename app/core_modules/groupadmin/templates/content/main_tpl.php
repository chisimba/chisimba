<?php  
//do the check to check if TII is accessable

	$objExtJS = $this->getObject('extjs','ext');
	$objExtJS->show();

	$fullUri = $this->uri(NULL);
    $fullUri = explode("?",$fullUri);
    $siteUri = $fullUri[0];

	$ext = $this->getJavaScriptFile('ColumnNodeUI.js', 'groupadmin');
	$ext .= $this->getJavaScriptFile('Ext.ux.grid.Search.js', 'groupadmin');
	$ext .= $this->getJavaScriptFile('users.js', 'groupadmin');
	$ext .= $this->getJavaScriptFile('interface.js', 'groupadmin');

	//setup the dynamicuri
	$this->appendArrayVar('headerParams', '
	        	<script type="text/javascript">	        		
	        		var baseUri = "'.$siteUri.'";
	        	</script>');

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
