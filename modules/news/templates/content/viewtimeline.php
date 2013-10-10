<?php
$this->loadClass('htmlheading', 'htmlelements');

echo $this->objNewsMenu->toolbar('timeline');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_news_newsintimelineformat', 'news', 'News in Timeline Format');



$int = 'WEEK';
		$fdate = "June 1 2007 00:00:00";
        $fdate = $this->objNewsStories->getLastNewsStoryDate();
		$timeline = $this->uri(array('action'=>'generatetimeline'));
		$timeline = str_replace('&amp;', '&', $timeline);
		$objIframe = $this->getObject('iframe', 'htmlelements');
    	$objIframe->width = "100%";
    	$objIframe->height="310";
     	$ret = $this->uri(array("mode" => "plain",
	          "action" => "viewtimeline", 
			  "timeLine" => ($timeline),
			  //"timeLine" => urlencode($timeline),
			  "intervalUnit" => $int,
			  "focusDate" => $fdate,
			  "tlHeight" => '300'), "timeline");
    	$objIframe->src=$ret;
    	$objIframe->frameborder=0;
    	$objIframe->id='timelineiframe';
    	$objIframe->scrolling='no';
    	//$objIframe->extra=' onload="resizeIframe(\'timelineiframe\');" ';
        echo $objIframe->show();


?>
