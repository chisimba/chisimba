<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/*$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
$uxcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/ux/css/ColumnNodeUI.css','htmlelements').'"/>';
$uxjs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ux/ColumnNodeUI.js','htmlelements').'" type="text/javascript"></script>';
 *
 */
$tools = '<script language="JavaScript" src="'.$this->getResourceUri('contexttools.js').'" type="text/javascript"></script>';
/*$ckeditorbase = '<script language="JavaScript" src="'.$this->getResourceUri('ckeditor/ckeditor.js','htmlelements').'" type="text/javascript"></script>';
*/

$objExtjs=$this->getObject("extjs","ext");
$objExtjs->show();
$ckeditorbase = '<script language="JavaScript" src="'.$this->getResourceUri('ckeditor/ckeditor.js','ckeditor').'" type="text/javascript"></script>';


$initVars='
       <script type="text/javascript">
        var instancename=\''.$instancename.'\';
        var chapterurl=\''.str_replace("amp;", "", $chapterlisturl).'\';
        var insertUrl=\''.str_replace("amp;", "", $viewchapterurl).'\';
       </script>
      ';

$this->appendArrayVar('headerParams', $initVars);

/*
$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $uxcss);
$this->appendArrayVar('headerParams', $uxjs);*/
$this->appendArrayVar('headerParams', $tools);
$this->appendArrayVar('headerParams', $ckeditorbase);

$contextlisturl = $this->uri(array('action'=>'jsonusercontexts'));
$contexturl = $this->uri(array('action'=>'joincontext'),'context');
$filtersurl = $this->uri(array('action'=>'getfilters'));
$baseurl = $this->uri(array('action'=>'getfilterparams'));
$storyurl = $this->uri(array('action'=>'getstories'));
$inputurl = $this->uri(array('action'=>'getfilterinput'));
$extdiv='
        <div id="contexttools">  </div>
        <div id="contexttools-win"> </div>
        <div id="contextlist"  class="x-hide-display"></div>
        <div id="filterlist"  class="x-hide-display"></div>
        </div>
        <div id="inputwin"</div>
      ';

echo $extdiv;
$mainjs = "
                Ext.onReady(function(){
                   var url='".str_replace("amp;", "", $contextlisturl)."';
                   var contexturl='".str_replace("amp;", "", $contexturl)."';
                   var filtersurl='".str_replace("amp;", "", $filtersurl)."';
                   var baseurl='".str_replace("amp;", "", $baseurl)."';
                   var storyurl='".str_replace("amp;", "", $storyurl)."';
                   var inputurl='".str_replace("amp;", "", $inputurl)."';
                   initContextTools(url,contexturl,filtersurl,baseurl,storyurl,inputurl);
                 });

          ";
echo "<script type=\"text/javascript\">".$mainjs."</script>";
?>
