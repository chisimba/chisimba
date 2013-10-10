<?php
//rss add/edit template

$middleColumn = null;
	if(!empty($rdata))
	{
		$middleColumn .= $this->_objUtils->rssEditor(FALSE, $rdata);
	}
	else {
		$middleColumn .= $this->_objUtils->rssEditor(FALSE);
	}


echo $middleColumn;
?>