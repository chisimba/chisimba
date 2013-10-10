<?php
//do the check to check if TII is accessable



$objSysConfig = $this->getObject('altconfig', 'config');
$objExtJS = $this->getObject('extjs', 'ext');
$objExtJS->show();
//$ext .=$this->getJavaScriptFile('ext-3.0-rc2/examples/ux/FileUploadField.js', 'htmlelements');
//$ext .= '<script type="text/javascript" src="http://extjs.com/deploy/dev/examples/ux/fileuploadfield/FileUploadField.js"></script>';

$this->appendArrayVar('headerParams', '
	        	<script type="text/javascript">


	        		var storeUri = "' . str_replace('&amp;', '&', $this->uri(array('action' => 'json_getstudentassessments', 'module' => 'jturnitin'))) . '";
	        		var baseUri = "' . $objSysConfig->getsiteRoot() . 'index.php";
	        	</script>');

//$ext .=$this->getJavaScriptFile('ext-3.0-rc2/examples/shared/code-display.js', 'htmlelements');
//$ext .=$this->getJavaScriptFile('ext-3.0-rc2/examples/ux/ColumnNodeUI.js', 'htmlelements');
//$ext .=$this->getJavaScriptFile('ext-3.0-rc2/examples/tree/column-tree.js', 'htmlelements');
//$ext .=$this->getJavaScriptFile('lecturers.js', 'turnitin');
$ext .=$this->getJavaScriptFile('students.js', 'jturnitin');
/* $ext .=$this->getJavaScriptFile('ext-3.0-rc2/examples/shared/examples.js', 'htmlelements');


  $ext .= '<link rel="stylesheet" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css', 'htmlelements').'" type="text/css" />';
  $ext .= '<link rel="stylesheet" href="'.$this->getResourceUri('ext-3.0-rc2/examples/grid/grid-example.css', 'htmlelements').'" type="text/css" />';
  $ext .= '<link rel="stylesheet" href="'.$this->getResourceUri('ext-3.0-rc2/examples/shared/examples.css', 'htmlelements').'" type="text/css" />';
  //$ext .= '<link rel="stylesheet" href=" <link rel="stylesheet" type="text/css" href="http://extjs.com/deploy/dev/examples/ux/fileuploadfield/css/fileuploadfield.css"/>" type="text/css" />';
 */
$ext .= '<style type="text/css">

       
        .empty .x-panel-body {
            padding-top:20px;
            text-align:center;
            font-style:italic;
            color: gray;
            font-size:11px;
        }
        
        .red {
        background-color:red;
        color: gray;
        padding:0px;
        }
        
        .yellow {
        background-color:yellow;
        color: gray;
        padding:0px;
        }
       .blue {
        background-color:#4499ee;
        color: gray;
        padding:0px;
        }
        .grey {
        background-color:lightgrey;
        color: gray;
        padding:0px;
        }
        
        .green {
        background-color:lightgreen;
        color: gray;
        padding:0px;
        }
        
 		.scoretable{
 			border-style:solid;
			border-width:1px;
			border-color:lightgrey;
			color:grey;
			font-size:small;
			padding:3px;
 		}

 		span.white	{ background: white; padding: 0 2px 0 2px; border-right: 1px solid #888; display: block; width: 28px; text-align: center; cursor: pointer;}
	span.gen	{ background: #eee; padding: 0 2px 0 2px; border-right: 1px solid #bbb; display: block; width: 28px; text-align: center; color: #888; cursor: pointer;}
	
 		a.red:link, a.red:visited	{ font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode; background: red; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #888; display: block; text-align: left; width: 38px; cursor: pointer;}
	a.red:active, a.red:hover, { font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode; background: #c00; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #666; display: block; text-align: left; width: 38px; cursor: pointer;}
	a.blue:link, a.blue:visited	{ font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode; background: #4499ee; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #888; display: block; text-align: left; width: 38px; cursor: pointer;}
	a.blue:active, a.blue:hover	{ font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode; background: #039; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #666; display: block; text-align: left; width: 38px; cursor: pointer;}
	a.yellow:link, a.yellow:visited	{ font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode; background: yellow; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #888; display: block; text-align: left; width: 38px; cursor: pointer;}
	a.yellow:active, a.yellow:hover	{ font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode; background: #cc0; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #666; display: block; text-align: left; width: 38px; cursor: pointer;}
	a.green:link, a.green:visited	{ font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode; background: #3c0; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #888; display: block; text-align: left; width: 38px; cursor: pointer;}
	a.green:active, a.green:hover	{ font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode; background: #090; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #666; display: block; text-align: left; width: 38px; cursor: pointer;}
	a.orange:link, a.orange:visited	{ font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode; background: orange; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #888; display: block; text-align: left; width: 38px; cursor: pointer;}
	a.orange:active, a.orange:hover	{ font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode; background: #c90; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #666; display: block; text-align: left; width: 38px; cursor: pointer;}
	a.pending	{ font-size: 10px; font-family: Lucida Grande, Lucida Sans Unicode, sans-serif; background: #ddd; padding: 0 12px 0 0; text-decoration: none; color: #000; border: 1px solid #bbb; display: block; text-align: left; width: 38px; filter: progid:DXImageTransform.Microsoft.BasicImage(opacity=.8); opacity: .8; cursor: pointer;}

    </style>';

$this->appendArrayVar('headerParams', $ext);

if ($errorMessage) {
    print '<h3>The following Turnitin Error was encountered</h3><span class="warning">' .
            $errorMessage . '</span><br/><br/>';
}
?>


<div id="topic-grid"></div>
<div id="hello-win" class="x-hidden">

    <div class="x-window-header">Hello Dialog</div>
    <div id="hello-tabs">
        <!-- Auto create tab 1 -->
        <div class="x-tab" title="Hello World 1">

        </div>
        <!-- Auto create tab 2 -->
        <div class="x-tab" title="Hello World 2">


        </div>
    </div>
</div>





