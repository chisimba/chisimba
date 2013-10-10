<?php

$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$leftSideColumn = $postLoginMenu->show();
$cssLayout->setLeftColumnContent($leftSideColumn);

$rightSideColumn='<div id="gtx"></div><div style="padding:10px;">';



$this->loadClass('form', 'htmlelements');
$this->loadClass("textarea", "htmlelements");
$this->loadclass('htmltable', 'htmlelements');

//$essayId=$this->getParam('essayid');
//$essayTitle = $this->getParam('title');
$essaycontent=$this->essays->getEssayContent($storyid);

$essaycontent=trim($essaycontent[0]['content'], "</p>");
//print_r($essaycontent);
$essaypreview= new textarea(null, $essaycontent, 35, 120);


//Add the table to the centered layer
$rightSideColumn .=$essaypreview->show();
$rightSideColumn.= '</div>';

$cssLayout->setMiddleColumnContent($rightSideColumn);
echo $cssLayout->show();
echo $essayTitle;
?>
