<?php

$objSysConfig  = $this->getObject('altconfig','config');
$objExtJS = $this->getObject('extjs','ext');

$objExtJS->show();
$ext = "";
$this->appendArrayVar('headerParams', '
	        	<script type="text/javascript">
	        		var storeUri = "'.str_replace('&amp;','&',$this->uri(array('action' => 'json_getstudentassessments', 'module' => 'jturnitin'))).'";
	        		var baseuri = "'.$objSysConfig->getsiteRoot().'index.php";
                                var ainst="'.$instructions.'";
                                var title="'.$title.'";
                                var oldtitle="'.$title.'";
                                var dstart="'.$datestart.'";
                                var dend="'.$dateend.'";
                                var assignmentid="'.$id.'";
                                var sviewreports="'.$sviewreports.'";
                                var generate="'.$generate.'";
                                var repository="'.$repository.'";
                                var searchpapers="'.$searchpapers.'";
                                var searchinternet="'.$searchinternet.'";
                                var searchjournals="'.$searchjournals.'";
                                var searchinstitution="'.$searchinstitution.'";
                                var latesubmissions="'.$latesubmissions.'";

                                   
	        	</script>');

$ext .=$this->getJavaScriptFile('editassignment.js', 'jturnitin');
$ext .=$this->getJavaScriptFile('editadvancedassigoptions.js', 'jturnitin');
$ext .=$this->getJavaScriptFile('editadvancedassigoptions2.js', 'jturnitin');
$this->appendArrayVar('headerParams', $ext);
echo '<div id="surface"></div>';

?>
