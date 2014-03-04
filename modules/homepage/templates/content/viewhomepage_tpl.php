<?php
	//echo "<h1>Homepage for ".$objUser->fullname($userId)."</h3>";
	$links = '';
	if ($this->objUser->userId() == $userId) {
	    $icon = $this->getObject('geticon','htmlelements');
	    $edit = $icon->getEditIcon($this->uri(array('action'=>'edithomepage'),'homepage'));
	    $del = $icon->getDeleteIconWithConfirm(null,array('action'=>'deletehomepage'),'homepage');
	    $links = "$edit $del";
	}
	echo "<h1>" . $objLanguage->languageText('mod_homepage_heading', 'homepage') /*. " " . $this->objUser->fullName($userId)*/ . " $links</h1>";
	if (!$exists) {
	    echo $this->objLanguage->languageText('mod_homepage_nopage', 'homepage');
	}
	else {
		echo $contents;
	}
?>