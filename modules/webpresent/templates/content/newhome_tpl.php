<?php
// load the ext js and module specific scripts
$extbase = '<script language="javascript" src="'.$this->getResourceUri('ext-3.4.0/adapter/ext/ext-base.js','ext').'" type="text/javascript"></script>';
$extalljs = '<script language="javascript" src="'.$this->getResourceUri('ext-3.4.0/ext-all.js','ext').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.4.0/resources/css/ext-all.css','ext').'"/>';



$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);

$objLatestFiles = $this->getObject('viewerutils','webpresent');

$featured=
        "Ext.onReady(function(){
         Ext.QuickTips.init();

       // basic tabs 1, built from existing content
      new Ext.Panel({
        renderTo: 'sidebar',
        title: 'Featured',
        width:320,
        height:300,
        activeTab: 0,
        frame:true,
        defaults:{autoHeight: true},
        items:[
            {

            html: '".$objLatestFiles->getFeatured()."'

            }

        ]
    });


});
";

$this->loadClass('link', 'htmlelements');
$content='';
$content.='<div class="subcolumns" id="latestuploads">';//subcolumns starts
$content.='  <div class="latestuploads_container">';//latest container starts

$tagCloud = $this->objTags->getTagCloud();
$tagCloudLink = new link ($this->uri(array('action'=>'tagcloud')));
$tagCloudLink->link = 'View All Tags';
$tagCloudContent = '<span style="text-align:center">' . $tagCloud . '</span><br />'.$tagCloudLink->show();

//$content.= $tagCloudContent;
$content.='<div class="c85r">';
$content.='<div id="lu_wrapper">';
$content.='<div id="lu_inner">';
$content.='<div class="ec_item" id="lu1">';
//$content.=$objLatestFiles->getLatestUploads();
$content.='</div>';
$content.='</div>';
$content.='</div>';
$content.='</div>';

$content.='</div>';//latest uploads container ends
$content.='</div>';//subcolum ends
//start the content

$content.='<div id="contentwrapper" class="subcolumns">';
$content.='     <div id="homepagecontent" class="c85l">';
$content.='          <div class="subcolumns">';
$content.='            <div class="c59l" id="homepagestats">';
$content.=$objLatestFiles->getLatestUploads();
$content.='             </div>';
$content.=$objLatestFiles->getTagCloudContent($tagCloudContent);

$content.="<script type=\"text/javascript\">".$featured."</script>";
$content.='         </div>';
$content.='     </div>';
$content.=$objLatestFiles->getMostViewed();
$content.=$objLatestFiles->getMostDownloaded();

$content.='</div>';

echo $content;
?>
