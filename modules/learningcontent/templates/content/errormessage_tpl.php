<?php

$chapters = $this->objContextChapters->getContextChapters($this->contextCode);
$this->setVarByRef('chapters', $chapters);

$this->setLayoutTemplate('layout_firstpage_tpl.php');

if (isset($errorTitle)) {
    echo '<h1 class="error">'.$this->objLanguage->languageText('word_error', 'system', 'Error').': '.$errorTitle.'</h1>';
}

if (isset($errorMessage)) {
    echo $errorMessage;
}


?>