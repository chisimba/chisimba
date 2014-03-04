<?php
header("Content-Type: text/html;charset=utf-8");
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
$refreshLink = $this->newObject('link', 'htmlelements');
$refreshIcon = $this->newObject('geticon', 'htmlelements');
$configLink = $this->newObject('link', 'htmlelements');
$configIcon = $this->newObject('geticon', 'htmlelements');
$loadIcon = $this->newObject('geticon', 'htmlelements');
$this->objFeatureBox = $this->getObject('featurebox', 'navigation');
$objWashout = $this->getObject('washout', 'utilities');
$this->objImOps = $this->getObject('dasops', 'das');
$objImView = $this->getObject('viewrender', 'das');

$scripts = $this->getJavaScriptFile('jquery/jquery-ui-personalized-1.6rc6/jquery-1.3.1.js', 'jquery');
$scripts .= $this->getJavaScriptFile('jquery/jquery-ui-personalized-1.6rc6/jquery-ui-personalized-1.6rc6.js', 'jquery');
$scripts .= '<link type="text/css" href="'.$this->getResourceUri('jquery/jquery-ui-personalized-1.6rc6/theme/ui.all.css', 'jquery').'" rel="Stylesheet" />';
$scripts .= '<script type="text/javascript">
	function update()
	{
	    $.post("index.php?module=das&action=getchatcontent", {}, function(data){ $("#screen").html(data);}); 
	 
	    setTimeout(\'update()\', 15000);
	}
 
	$(document).ready(
 
		function()
    	{
     		update();
 
     		$("#button").click(   
      			function()
      			{        
       				$.post("index.php?module=das&action=addchatmessage",
    					{ message: $("#message").val()},
    						function(data){ 
    							$("#screen").val(data);
    							$("#message").val("");
    						}
    					);
      			}
     		);
    	}
    );
 
		</script>';

//$this->appendArrayVar('headerParams', $scripts);

$script = $this->getJavaScriptFile('das.js');
$this->appendArrayVar('headerParams', $script);
$this->appendArrayVar('bodyOnLoad', "loadConversations()");

$loadIcon->setIcon('loader');

$middleColumn = NULL;
$leftColumn = NULL;
$rightColumn = NULL;

// Add in a heading
$header = new htmlHeading();
$header->str = 'My Conversations' ;$this->objLanguage->languageText('mod_im_recentmessages', 'im');
$header->type = 3;

$refreshLink->href = $this->uri(null, 'das');
$refreshLink->extra = ' onclick="showLoading(\'conversations\'); loadConversations()" ';
$refreshIcon->setIcon('refresh_blue24x24','png');
$refreshIcon->alt="Check for new messages";
$refreshIcon->title="Check for new messages";
$refreshLink->link = $refreshIcon->show();

if($this->objUser->inAdminGroup($this->objUser->userId()))
{
    $cid = $this->objUser->userId();
    
	$configIcon->setIcon('admin');
	$configIcon->title="Configuration";
	$configIcon->alt="Configuration";
	$configLink->href = $this->uri(array('action' => 'viewcounsilors', 'das'));
	$configLink->link = $configIcon->show();
	$config = $configLink->show();
}else{
    $cid = $this->objUser->userId();
    
	$config = "";
}
$outof = '/'.$this->objDbImPres->numOfUserAssigned ($cid);
$msgs = $this->objDbIm->getMessagesByActiveUser ($cid);

$num = count($msgs);
$str = "$num$outof users";


$middleColumn .= $header->show().'<br/>'.$config.'  '.$refreshLink->show().'<br/>'.$str;
//$middleColumn .= '<div id="conversations"><span class="subdued"><i><h3>Loading....'.$loadIcon->show().'</h3></i></span></div>'; 
$middleColumn .=$objImView->renderOutputForBrowser($msgs).'<br/>'.$refreshLink->show();


if (!$this->objUser->isLoggedIn()) {
    $leftColumn .= $this->objImOps->loginBox(TRUE);
} else {
   
    $rightColumn .= $objImView->renderLinkList($msgs);
	$rightColumn .= $objImView->getStatsBox();
	$leftColumn .= $this->leftMenu->show();
    if($this->objUser->inAdminGroup($this->objUser->userId()))
    {
       $leftColumn .= $this->objImOps->massMessage();//(TRUE, TRUE);
    }
}

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
$cssLayout->setRightColumnContent($rightColumn);
//echo $cssLayout->show();
echo $middleColumn;
?>
