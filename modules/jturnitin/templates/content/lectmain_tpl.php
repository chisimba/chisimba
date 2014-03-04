<?php  
//do the check to check if TII is accessable



$objSysConfig  = $this->getObject('altconfig','config');
$objExtJS = $this->getObject('extjs','ext');
$objExtJS->show();
$ext = "";
$this->appendArrayVar('headerParams', '
	        	<script type="text/javascript">
	        		var storeUri = "'.str_replace('&amp;','&',$this->uri(array('action' => 'json_getstudentassessments', 'module' => 'jturnitin'))).'";
	        		var baseuri = "'.$objSysConfig->getsiteRoot().'index.php";
	        	</script>');

$ext .=$this->getJavaScriptFile('lecturers.js', 'jturnitin');
$ext .=$this->getJavaScriptFile('advancedassigoptions.js', 'jturnitin');
$ext .=$this->getJavaScriptFile('advancedassigoptions2.js', 'jturnitin');
$ext .=$this->getJavaScriptFile('studentsubmit.js', 'jturnitin');
$ext .= '<style type="text/css">

       
        .empty .x-panel-body {
            padding-top:20px;
            text-align:center;
            font-style:italic;
            color: gray;
            font-size:11px;
        }
         .blue {
        background-color:#4499ee;
        color: gray;
        padding:0px;
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

if($errorMessage) {
    print '<h3>The following Turnitin Error was encountered</h3><span class="warning">'.
            $errorMessage.'</span><br/><br/>';
}

?>

<div id="topic-grid"></div>




