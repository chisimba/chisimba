<?php
$this->loadclass('link','htmlelements');
$minjs = '<script src="'.$this->getResourceUri('jquery/1.3.2/jquery-1.3.2.min.js','jquery').'" type="text/javascript"></script>';

$mainjs = '<script src="'.$this->getResourceUri('js/jquery.cycle.js').'" type="text/javascript"></script>';
$imagetransjs = '<script src="'.$this->getResourceUri('js/imagetrans.js').'" type="text/javascript"></script>';
$this->appendArrayVar('headerParams', $minjs);
$this->appendArrayVar('headerParams', $mainjs);
$this->appendArrayVar('headerParams', $imagetransjs);
$style="
<style type=\"text/css\">
.slideshow {

width: 200px;
float: left;
}
.slideshow img { padding: 15px; background-color: transparent; }
</style>
";

$this->appendArrayVar('headerParams', $style);

$cycle="
<script type=\"text/javascript\">
jQuery(document).ready(function() {
    jQuery('.slideshow').cycle({
		fx: 'fade' // choose your transition type, ex: fade, scrollUp, shuffle, etc...
	});
});
</script>

";


$this->appendArrayVar('headerParams', $cycle);

$objLogin =  $this->getObject('speak4freeloginInterface');
$slideshow='
        <div class="login">'.$objLogin->renderLoginBox().'</div>
	<div class="slideshow">';
$storyids=array(
        '0'=> 'aboutus',
        '1'=> 'poems',
        '2'=> 'more',
        '3'=> 'talent',
        '4'=> 'poems');
$stickies=array(
        "bluesticky.png",
        "yellowsticky.png",
        "purplesticky.png",
        "pinksticky.png",
        "bluesticky.png",
);
for($i=1;$i<5;$i++) {
    $link=new link($this->uri(array('action'=>'viewstory','category'=>$storyids[$i-1])));
    $link->link='<img src="'.$this->getResourceUri("images/stickies/$stickies[$i]").'" width="300" height="300" fixPNG(this)"/>';
    $slideshow.=$link->show();
}



$socialbuttons='<div class="footer">';
$bloglink=new link($this->uri(array('action'=>'blog')));
$bloglink->link='<img src="'.$this->getResourceUri("images/social/blogbutton.png").'"/>';

$socialbuttons.=$bloglink->show();
$socialbuttons.="</div>";
$homecontent= $this->objViewerUtils->getTopic('homepage',false);
$slideshow.='
</div>
<div id="homepagecontent">
'.$homecontent.'
</div>
';

$latestuploadcontent=$this->objViewerUtils->getLatestUploads();
$latestuploads='<p style="color:#ea8338;">Featured</p>
                  <div id="editorschoice" class="subcolumns">

                    <div id="c85r">

                        <div id="ec_wrapper">
                            <div id="ec_inner">
';
$total=count($latestuploadcontent);
for($i=0;$i<$total;$i++){
 $latestuploads.=' <div id="ec'.($i+1).'" class="ec_item">
                                    <div id="ec_item_inner">
                                        '.$latestuploadcontent[$i].'
                                    </div>
                                </div>
';
}
 $latestuploads.='
                            </div>

                        </div>

                    </div>

                </div>
';
// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

$nav=$this->getObject('nav');
$leftColumnContent=$nav->show();
$cssLayout->setLeftColumnContent($leftColumnContent);
$rightSideColumn =  $slideshow.$latestuploads;
$cssLayout->setMiddleColumnContent($rightSideColumn);
echo $cssLayout->show();

?>