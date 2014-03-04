<?php

	// Get the configuration settings
	require_once('configuration.php');

	// Load the SWORD library and packager
	require_once($swordlibrarylocation . 'swordappclient.php');
	require_once($swordlibrarylocation . 'swordappentry.php');
	require_once($swordlibrarylocation . 'packager_mets_swap.php');
	require_once('includes/MimeMailParser.class.php');

	// Connect to the IMAP server
	$inbox = imap_open($imaphost, $mailuser, $mailpassword)
		or die("can't connect: " . imap_last_error());

	// Get the number of messages to process
	$connection = imap_check($inbox);
	$messages = imap_fetch_overview($inbox, "1:{$connection->Nmsgs}", 0);
	$counter = 0;
	foreach ($messages as $message) {
		if (!$message->seen) $counter++;
	}
	echo $connection->Nmsgs . " message(s) in the inbox, " .
	     $counter . " unread and to process\n\n";

	// Process each email
	foreach ($messages as $message) {
		// Only process emails that are not marked as seen
		if (!$message->seen) {
			// Get the metadata
			$author = $message->from;
			$author = trim(preg_replace('/\<.*\>/', '', $author));
			echo " - Author: {$author}\n"; 
			$title = trim($message->subject);
			echo " - Title: {$title}\n";
			$abstract = trim(strip_tags(imap_fetchbody($inbox, $message->msgno, 1)));
			echo " - Message: $abstract\n";
			$info = imap_fetchstructure($inbox, $message->msgno);

			// Create the package
			$uid = imap_uid($inbox, $message->msgno);
			$packagefilename = 'packages/packager-' . $uid . '.zip';
			@mkdir('tmp/attachments/' . $uid);
			$package = new PackagerMetsSwap('tmp', 'attachments/' . $uid, 
			                                'tmp', $packagefilename);
			$package->addCreator($author);
			$package->setTitle($title);
			$package->setAbstract($abstract);
			
			// Get the attachments / files
	   		$type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", 
			              "AUDIO","IMAGE", "VIDEO", "OTHER");
			$counter = 1;
			while ((count($info->parts) > 1) && (count($info->parts) > $counter)) {
				if ($info->parts[$counter]->ifparameters == 1) {
					$filename = $info->parts[$counter]->parameters[0]->value;
				} else if ($info->parts[$counter]->ifdparameters == 1) {
					$filename = $info->parts[$counter]->dparameters[0]->value;
				}
				$mimetype = $type[(int)$info->parts[$counter]->type] . '/' . 
				            $info->parts[$counter]->subtype;
				$mimetype = strtolower($mimetype);
				echo " - File: $filename ($mimetype)... ";
				$fullfilename = 'tmp/attachments/' . $uid . '/' . $filename;
				$file = 'tmp/attachments/' . $uid . '/mail.txt';
				imap_savebody($inbox, $file, $message->msgno);
				$parser = new MimeMailParser();
				$parser->setPath($file);
				$attachments = $parser->getAttachmentsAsStreams();
				file_put_contents($fullfilename, $attachments[$counter - 1]);
				echo "done\n";
				$package->addFile($filename, $mimetype);
				$counter++;
			}

			// Deposit the package
			$package->create();
			$client = new SWORDAPPClient();
			$response = $client->deposit($swordurl, $sworduser, $swordpassword, '', 
			                             'tmp/' . $packagefilename, 
						     'http://purl.org/net/sword-types/METSDSpaceSIP',
						     'application/zip', false, true);
			// print_r($response);
			$id = $response->sac_id;
			$id = str_replace("http://hdl.handle.net/", "http://dspace.swordapp.org/jspui/handle/", $id);
			echo "Deposited at " . $id . "\n\n";
			$to = $message->from;
			$from = "From: " . $mailuser;
			$subject = "Deposit successful: " . $id;
			$contents = "Thanks you for your deposit. It can be viewed at " . $id;
			mail($to, $subject, $contents, $from);

			// Mark the message as read
			imap_setflag_full($inbox, $message->msgno, "\\Seen");
		}
	}

?>
