<?php

    $this->objLanguage=&$this->getObject('language','language');
    $this->setErrorMessage($this->objLanguage->languageText('mod_importuser_error1','userimport'));
    print $this->objLanguage->languageText('mod_importuser_error1','userimport');
?>
