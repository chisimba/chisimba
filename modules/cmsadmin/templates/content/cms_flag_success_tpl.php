<?php
/**
 * This template is used when a user flags a content item
 */

if ($id == '') {
    $id = $this->getParam('id', '');
}

if ($result == '') {
    $result = FALSE;
}

$middleContent = "<span class='success'>The content item was flagged successfully</span>";

if (!$result) {
    $middleContent .= "\n<br/><span class='error'>The alert notifications couldn't be sent</span>";
}

$middleContent .= "<br/>To continue to the previous page click <a href='?module=cms&action=showfulltext&id=$id'>here</a>";

$this->setVar('middleContent', $middleContent);

?>
