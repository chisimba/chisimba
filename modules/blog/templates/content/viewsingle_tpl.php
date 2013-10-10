<script type="text/javascript">
//<![CDATA[
function init () {
	$('input_redraw').onclick = function () {
		redraw();
	}
}
function redraw () {
	var url = 'index.php';
	var pars = 'module=security&action=generatenewcaptcha';
	var myAjax = new Ajax.Request( url, {method: 'get', parameters: pars, onComplete: showResponse} );
}
function showLoad () {
	$('load').style.display = 'block';
}
function showResponse (originalRequest) {
	var newData = originalRequest.responseText;
	$('captchaDiv').innerHTML = newData;
}
//]]>
</script>
<?php
$middleColumn = NULL;
if (!isset($cats)) {
    $cats = NULL;
}
if (isset($comment) && isset($useremail)) {
    //$middleColumn = "CAPTCHA was kakka";
    $comment = urldecode($comment);
    $useremail = urldecode($useremail);
} else {
    $comment = NULL;
    $useremail = NULL;
}
$cssLayout = $this->newObject('csslayout', 'htmlelements');
//show all the posts
$middleColumn.= $this->objblogPosts->showPosts($posts, TRUE);


// Show comments using the comment object
$objCmt = $this->getObject('dynamiccomment', 'blog');
$middleColumn.= $objCmt->show($postid);

/*
if ($this->commentsEnabled) {
    $middleColumn.= $this->objComments->showComments($postid);
}

 

$tracks = $this->objblogTrackbacks->showTrackbacks($postid);
$middleColumn.= $tracks;

if ($this->commentsEnabled) {
    if ($this->objUser->isLoggedIn() == TRUE) {
        $middleColumn.= $this->objblogPosts->addCommentForm($postid, $userid, $captcha = FALSE, $comment, $useremail);
    } else {
        $middleColumn.= $this->objblogPosts->addCommentForm($postid, $userid, $captcha = TRUE, $comment, $useremail);
    }
}
*/
// Added by Tohir - Standard layout for elearn
$layoutToUse = $this->objSysConfig->getValue('blog_layout', 'blog');

if ($layoutToUse == 'elearn') {
    $this->setLayoutTemplate('blogelearn_layout_tpl.php');
    echo $middleColumn;
} else {
    $objUi = $this->getObject('blogui');
    // left hand blocks
    $leftCol = $objUi->leftBlocks($userid);
    // right side blocks
    $rightSideColumn = $objUi->rightBlocks($userid, $cats);
    if ($leftCol == NULL || $rightSideColumn == NULL) {
        $cssLayout->setNumColumns(2);
    } else {
        $cssLayout->setNumColumns(3);
    }
    //dump the cssLayout to screen
    if ($leftCol == NULL) {
        $leftCol = $rightSideColumn;
        $cssLayout->setMiddleColumnContent($middleColumn);
        $cssLayout->setLeftColumnContent($leftCol);
        //$cssLayout->setRightColumnContent($rightSideColumn);
        echo $cssLayout->show();
    } elseif ($rightSideColumn == NULL) {
        $cssLayout->setMiddleColumnContent($middleColumn);
        $cssLayout->setLeftColumnContent($leftCol);
        echo $cssLayout->show();
    } else {
        $cssLayout->setMiddleColumnContent($middleColumn);
        $cssLayout->setLeftColumnContent($leftCol);
        $cssLayout->setRightColumnContent($rightSideColumn);
        echo $cssLayout->show();
    }
}
?>