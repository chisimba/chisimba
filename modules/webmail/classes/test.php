<?php

require_once("imap_class_inc.php");
if(extension_loaded("imap"))
{
	echo "party time";
}
try {
	//$dsn = 'imap://kcyster:81141@itsnw.uwc.ac.za:143/INBOX';
	$dsn = 'imap4rev1://pmbekwa:prince@itsnw.uwc.ac.za:143/INBOX';
	//$dsn = 'imap://fsiu:fsiu@itsnw.uwc.ac.za:143/INBOX';
	//$dsn = 'imap://pscott:scott@itsnw.uwc.ac.za:143/INBOX';

	//$dsn = 'pop://fsiu:fsiu@itsnw.uwc.ac.za:110/INBOX';
	//$dsn = array(
	//	'imapserver' => 'itsnw.uwc.ac.za',
	//	'imapuser' => 'pmbekwa',
	//	'imappass' => 'prince',
	//	'imapport' => 143,
	//	'imapprotocol' => 'imap',
	//	'imapmailbox' => 'INBOX',
	//	);
	$m = new imap;
	$m->factory($dsn);
	//echo $m->setAddress('pscott', 'uwc.ac.za', 'Paul Scott');
	//var_dump($m->checkMailboxStatus());
	//$m->pingServer();
	//$m->listMailBoxes();
	//$acl = $m->getACL();
	//print_r($acl);

	//$stats = $m->checkMailboxStatus();
	//print_r($stats);
	//$m->getQuotas();
	//$m->setconn("itsnw.uwc.ac.za","fsiu","fsiu");
	//$heads = $m->getHeaders();
	//$nummails = $m->numMails();
	//$thebox = $m->checkMbox();
	//var_dump($thebox);
	//$theheads = $m->getHeaderInfo(21);
	//var_dump($theheads);
	$themess = $m->getMessage(648);
	var_dump($themess);
	//header('Content-type: image/jpg');
	//echo base64_decode($themess[1][0]['filedata']);
	//echo "<h1>$nummails</h1>";
}
catch (Exception $e)
{
	echo $e;
	die();
}
//var_dump($m);