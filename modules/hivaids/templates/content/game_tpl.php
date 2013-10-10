<?php
/**
* Template to display the flash game
* @access public
*/

$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
?>

<center>
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="800" height="600">

<param name="movie" value="<?php echo $skin; ?>resources/YourMoves.swf" /> 
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" />

<embed bgcolor="#ffffff" src="<?php echo $skin; ?>resources/YourMoves.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="800" height="600"></embed>

</object>
</center>

<?php

$objLanguage = $this->getObject('language', 'language');
$this->loadClass('link', 'htmlelements');
$back = $objLanguage->languageText('word_back');
$objLink = new link('#');
$objLink->extra = 'onclick="javascript: history.back();"';
$objLink->link = $back;

echo '<p align="center">'.$objLink->show().'</p>';
?>