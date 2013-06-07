<?php

require_once('attachmentreader.class.inc.php');

define('EMAIL_HOST', 'mail.digitalfactory.co.za');
define('EMAIL_POST', '110');

// See: http://www.php.net/manual/en/function.imap-open.php -> Optional flags for names
define('EMAIL_OPTIONS', 'pop3/novalidate-cert');


define('EMAIL_LOGIN', 'chisimba@digitalfactory.co.za');
define('EMAIL_PASSWORD', 'ch1s1mba');

//This will be the [domain] part of the @ in [catchall]@[domain]
// forum_topic_2@chisimba.tohir.co.za
define('CATCH_ALL_BASE', 'chisimba.tohir.co.za');



$emailBox = new AttachmentReader(EMAIL_HOST, EMAIL_POST, EMAIL_OPTIONS, EMAIL_LOGIN, EMAIL_PASSWORD, CATCH_ALL_BASE);

$numMessages = $emailBox->getNumMessages();

if ($numMessages > 0) {
    for ($emailNum = 1; $emailNum <= $numMessages; $emailNum++)
    {
        
        // Retrieve Basic Details of Email From the Headers
        $emailDetails = $emailBox->getEmailDetails($emailNum);
        
        echo '<pre>';
        print_r($emailDetails);
        
        echo '</pre><hr>';
        
        // Mark Email for deletion
        //$emailBox->deleteEmail($emailNum);
    }
    
    // Expunge Deleted Mail
    unset($emailBox);
}