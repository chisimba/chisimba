<?php
$script = '<script>
<!--

/*
Auto Refresh Page with Time script
By JavaScript Kit (javascriptkit.com)
Over 200+ free scripts here!
*/

//enter refresh time in "minutes:seconds" Minutes should range from 0 to inifinity. Seconds should range from 0 to 59
var limit="0:30"

if (document.images){
var parselimit=limit.split(":")
parselimit=parselimit[0]*60+parselimit[1]*1
}
function beginrefresh(){
if (!document.images)
return
if (parselimit==1)
window.location.reload()
else{ 
parselimit-=1
curmin=Math.floor(parselimit/60)
cursec=parselimit%60
if (curmin!=0)
curtime=curmin+" minutes and "+cursec+" seconds left until page refresh!"
else
curtime=cursec+" seconds left until page refresh!"
window.status=curtime
setTimeout("beginrefresh()",1000)
}
}

window.onload=beginrefresh
//-->
</script>';
// $this->appendArrayVar('headerParams', $script);

header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 3 );

// get the sidebar object
$this->leftMenu = $this->newObject ( 'usermenu', 'toolbar' );
$this->loadClass ( 'htmlheading', 'htmlelements' );
$this->objOps = $this->getObject ( 'tweetops' );
$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
$objWashout = $this->getObject ( 'washout', 'utilities' );

$this->objDia = $this->getObject('jqdialogue', 'htmlelements');

$middleColumn = NULL;
$middleColumn .= $this->objOps->renderTopBoxen();

$leftColumn = NULL;
$rightColumn = NULL;

$objPagination = $this->newObject ( 'pagination', 'navigation' );
$objPagination->module = 'twitterizer';
$objPagination->action = 'viewallajax';
$objPagination->id = 'twitterizer';
$objPagination->numPageLinks = $pages;
$objPagination->currentPage = $pages - 1;

$middleColumn .= '<br/>' . $objPagination->show ();

$userid = $this->objUser->userid();
if (! $this->objUser->isLoggedIn ()) {

   // $leftColumn .= $objImView->showUserMenu ();

} else {
    //$leftColumn .= $this->leftMenu->show ();
}

$leftColumn .= $this->objOps->renderLeftBoxen();
$rightColumn .= $this->objOps->renderRightBoxen();

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setRightColumnContent ( $rightColumn );

echo $cssLayout->show ();
