<?php
//Get the CSS layout to make two column layout
$objListReplyOptions = $this->getObject('set_reply_options', 'hosportal');

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);
//Add some text to the left column
//$cssLayout->setLeftColumnContent("Place holder text");
//get the editform object and instantiate it
$objEditForm = $this->getObject('view_single_message_subject', 'hosportal');
$objEditForm->setNoOfDesiredMessagesPerPage($noOfMessages);
$objEditForm->setIdForSingleOriginalMessage($id);

$objEditForm->setPageNumber($pageNumber);
//$objEditForm->sortReplies($sortOptions);



//$objEditForm->setPageNumber($pageNumber);
//Add the form to the middle (right in two column layout) area
$cssLayout->setMiddleColumnContent($objEditForm->showTopForm().$objEditForm->showMiddleForm().$objEditForm->showBottomForm().$objEditForm->show());
//$cssLayout->setMiddleColumnContent($objEditForm->showMiddleForm());
$cssLayout->setRightColumnContent($objListReplyOptions->showBuiltSwitchMenu());
echo $cssLayout->show();

?>