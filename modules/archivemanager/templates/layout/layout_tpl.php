<?php

    $cssLayout =& $this->newObject('csslayout', 'htmlelements');

    $cssLayout->setNumColumns(3);
    $cssLayout->setLeftColumnContent('Left Content');
    $cssLayout->setMiddleColumnContent($this->getContent());
    $cssLayout->setRightColumnContent('Right Content');

    $cssLayout->show();

?>
