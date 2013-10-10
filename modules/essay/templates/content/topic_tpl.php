<?php
$ret ="";
if (!$objUser->isCourseAdmin($this->contextcode)) {
    $ret .= $content;
} else {
    $this->loadclass('link','htmlelements');
    $link = new link ($this->uri(array(), 'essayadmin'));
    $link->link = $this->objLanguage->languageText('mod_essayadmin_name', 'essayadmin', 'Essay Management');
    $ret .= $link->show();
}

echo "<div class='essay_main'>$ret</div>";
?>