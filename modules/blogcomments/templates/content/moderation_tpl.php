<?php
// comment moderation interface
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$leftMenu = $this->newObject('usermenu', 'toolbar');
$middleColumn = NULL;
$objFeatureBox = $this->getObject('featurebox', 'navigation');
$this->loadClass('href', 'htmlelements');
$cssLayout->setNumColumns(2);
$leftCol = NULL;
$leftCol = $leftMenu->show();



if(empty($mycomments))
{
	$middleColumn.= "<h1><em><center>" . $this->objLanguage->languageText("mod_blogcomments_commmadebymenocomms", "blogcomments") . "</center></em></h1>";
	$middleColumn.= "<br />";
	$mycomments = NULL;
}
else {
	$middleColumn.= "<h1><em><center>" . $this->objLanguage->languageText("mod_blogcomments_commmadebyme", "blogcomments") . "</center></em></h1>";
	$middleColumn.= "<br />";
	foreach($mycomments as $mecomms)
	{
		$fbheader = date('r', $mecomms['comment_date'])." ".ucwords($mecomms['comment_parentmod']);
		$fblink = new href($this->uri(array('postid' => $mecomms['comment_parentid'], 'action' => 'viewsingle', 'userid' => $mecomms['userid']), 
					   $mecomms['comment_parentmod']), $this->uri(array('postid' => $mecomms['comment_parentid'], 'action' => 'viewsingle', 'userid' => $mecomms['userid']), 
					   $mecomms['comment_parentmod']));
		$fbcontent = "<br />".$this->objLanguage->languageText("mod_blogcomments_viewincontext", "blogcomments").": ".$fblink->show();
	
		$updateuri = 'index.php'; //$this->uri(array('module' =>'blogcomments','action' => 'updatecomment'));
    	$commid = $mecomms['id'];
		$commcont = $mecomms['comment_content'];
		$commcont = str_replace("<p>", '', $commcont);
		$commcont = str_replace('</p>', '', $commcont);
		$script = '<p id="editme2">'.stripslashes(nl2br($commcont)).'</p>';
		$script .= '<script type="text/javascript">';
		$script .= "new Ajax.InPlaceEditor('editme2', '$updateuri', {rows:15,cols:40, callback: function(form, value) { return 'module=blogcomments&action=updatecomment&commid=' + escape('$commid') + '&newcomment=' +escape(value) }});";
		$script .= "</script>";
		//var_dump($script);
		$this->objIcon = $this->getObject('geticon', 'htmlelements');
		$delIcon = $this->objIcon->getDeleteIconWithConfirm($mecomms['id'], array(
                 'module' => 'blogcomments',
                 'action' => 'deletecomment',
                 'commentid' => $mecomms['id'],
                 'postid' => $mecomms['comment_parentid']
               ) , 'blogcomments');
	
	
	
		$mcfb[] .= $objFeatureBox->show($fbheader, $script."<br />".$delIcon." ".$fbcontent);
	}
}

if(empty($mcfb))
{
	$mcfb = NULL;
}
else {
	foreach($mcfb as $mcfbs)
	{
		$middleColumn .= $mcfbs;
	}
}

if(empty($comm4me))
{
	$middleColumn.= "<h1><em><center>" . $this->objLanguage->languageText("mod_blogcomments_comm4menocomms", "blogcomments") . "</center></em></h1>";
	$middleColumn.= "<br />";
	$mycomments = NULL;
}
else {
	$middleColumn.= "<h1><em><center>" . $this->objLanguage->languageText("mod_blogcomments_comm4me", "blogcomments") . "</center></em></h1>";
	foreach($comm4me as $c4m)
	{
		$ocheader = date('r', $c4m['comment_date'])." ".ucwords($c4m['comment_parentmod']);
		$oclink = new href($this->uri(array('postid' => $c4m['comment_parentid'], 'action' => 'viewsingle','userid' => $c4m['userid']), 
					   $c4m['comment_parentmod']), $this->uri(array('postid' => $c4m['comment_parentid'], 'action' => 'viewsingle','userid' => $c4m['userid']), 
					   $c4m['comment_parentmod']));
		$occontent = $c4m['comment_content']."<br />";
		$foot = $this->objLanguage->languageText("mod_blogcomments_viewincontext", "blogcomments").": ".$oclink->show();
		$this->objIcon = $this->getObject('geticon', 'htmlelements');
		$delIcon = $this->objIcon->getDeleteIconWithConfirm($c4m['id'], array(
                 'module' => 'blogcomments',
                 'action' => 'deletecomment',
                 'commentid' => $c4m['id'],
                 'postid' => $c4m['comment_parentid']
               ) , 'blogcomments');
		$ocfb[] .= $objFeatureBox->show($ocheader, $occontent."<br />".$delIcon."<br />".$foot);
	}
}

if(empty($ocfb))
{
	$ocfb = NULL;
}
else {
	foreach($ocfb as $ocfbs)
	{
		$middleColumn .= $ocfbs;
	}
}

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol);
echo $cssLayout->show();
?>