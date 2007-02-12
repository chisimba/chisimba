<?php
/*
 * Created on Jan 28, 2007
 */
echo $str;
$hideBanner=$this->getParam("mode", FALSE);
if (!$hideBanner == "TRUE") {
	$showLinkFrame = $this->getParam('showLinkFrame', FALSE);
	if ($showLinkFrame == "TRUE") {
	    $showHide = "Hide region to display linked content";
	    $link = "<a href=\"" . $this->uri(array(), "timeline") . "\">"
	      . $showHide . "</a>";
	} else {
	    $showHide = "Show region to display linked content";
	    $link = "<a href=\"" . $this->uri(array('showLinkFrame' => 'TRUE'), "timeline") . "\">"
	      . $showHide . "</a>";
	} 
	echo "<br />" . $link;
}
?>  
