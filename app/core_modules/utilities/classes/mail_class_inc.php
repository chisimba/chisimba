<?php
require("phpmailer_class_inc.php");

class mailer extends phpmailer 
{
    // Set default variables for all new objects
    var $From; //    = "paul@example.com";
    var $FromName;// = "PHP Mass mailer";
    var $Host; //     = "mail.server.co.za";
    var $Mailer; //   = "sendmail";                         // Alternative to IsSMTP()
    var $WordWrap; // = 75;
    
    function mailer($From, $FromName, $Host, $Mailer, $WordWrap)
    {
    	$this->From = $From;
		$this->FromName = $FromName;
		$this->Host = $Host;
		$this->Mailer = $Mailer;
		$this->WordWrap = $WordWrap;
    }

    // Replace the default error_handler
    function error_handler($msg) {
        //print("Mail Error");
        //print("Description:");
        printf("%s", $msg);
        exit;
        
        echo $From,$FromName,$Host,$Mailer,$WordWrap;
    }

}
?>