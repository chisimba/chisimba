<?php

$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $title;

echo $header->show();

if ($this->getParam('message') != '' && $this->getSession('displayconfirmationmessage', FALSE)) {
    switch ($this->getParam('message'))
    {
        default:
            $message = '';
            break;
        case 'contextupdated':
            $message = $this->objLanguage->languageText('mod_contextadmin_successfullyupdated', 'contextadmin', 'has been successfully updated').'!';
            break;
    }
    
    if ($message != '' ) {
        
        $this->setSession('displayconfirmationmessage', FALSE);
        
        $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
        $timeoutMessage->setMessage($message);
        $timeoutMessage->timeout = 10000;
        
        echo '<p>'.$timeoutMessage->show().'</p>';
    }
}

if (isset($content)) {
    echo $content;
} else {
    $objDisplayContext = $this->getObject('displaycontext', 'context');
    
    foreach ($contexts as $context)
    {
        echo $objDisplayContext->formatContextDisplayBlock($context);
    }
}

?>