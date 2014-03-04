<?php
	
	// The URL of the service document
	$testurl = "http://client.swordapp.org/client/servicedocument";
	
	// The user (if required)
	$testuser = "sword-user";
	
	// The password of the user (if required)
	$testpw = "sword-user";
	
	// The on-behalf-of user (if required)
	//$testobo = "user@swordapp.com";

	// The URL of the example deposit collection
	$testdepositurl = "http://client.swordapp.org/client/deposit/sword-user";

	// The test file to deposit
	$testfile = "test-files/sword-article.zip";

	// The content type of the test file
	$testcontenttype = "application/zip";

	// The packaing format of the test fifle
	$testformat = "http://purl.org/net/sword-types/METSDSpaceSIP";
	
	require("swordappclient.php");
	$testsac = new SWORDAPPClient();

	if (true) {
		print "About to request servicedocument from " . $testurl . "\n";
		if (empty($testuser)) { print "As: anonymous\n"; }
		else { print "As: " . $testuser . "\n"; }
		$testsdr = $testsac->servicedocument(
			       $testurl, $testuser, $testpw, $testobo);
		print "Received HTTP status code: " . $testsdr->sac_status . 
		      " (" . $testsdr->sac_statusmessage . ")\n";

		if ($testsdr->sac_status == 200) {
			print " - Version: " . $testsdr->sac_version . "\n";
			print " - Supports Verbose: " . $testsdr->sac_verbose . "\n";
			print " - Supports NoOp: " . $testsdr->sac_noop . "\n";
			print " - Maximum uplaod size: ";
			if (!empty($testsdr->sac_maxuploadsize)) {
				print $testsdr->sac_maxuploadsize . " kB\n";
			} else {
				print "undefined\n";
			}
		
			$workspaces = $testsdr->sac_workspaces;
			foreach ($testsdr->sac_workspaces as $workspace) {
				$wstitle = $workspace->sac_workspacetitle;
				echo "   - Workspace: ".$wstitle."\n";
				$collections = $workspace->sac_collections;
				foreach ($collections as $collection) {
					$ctitle = $collection->sac_colltitle;
					echo "     - Collection: " . $ctitle . " (" . $collection->sac_href . ")\n";
					if (count($collection->sac_accept) > 0) {
	        	        	        foreach ($collection->sac_accept as $accept) {
		        	        	        echo "        - Accepts: " . $accept . "\n";
		                	        }		
					}
					if (count($collection->sac_acceptpackaging) > 0) {
	        	        	        foreach ($collection->sac_acceptpackaging as $acceptpackaging => $q) {
		        	        	        echo "        - Accepted packaging format: " . 
							     $acceptpackaging . " (q=" . $q . ")\n";
		                	        }		
					}
					if (!empty($collection->sac_collpolicy)) {
						echo "        - Collection Policy: " . $collection->sac_collpolicy . "\n";
					}
					echo "        - Collection abstract: " . $collection->sac_abstract . "\n";
					$mediation = "false";
					if ($collection->sac_mediation == true) { $mediation = "true"; }
					echo "        - Mediation: " . $mediation . "\n";
					if (!empty($collection->sac_service)) {
						echo "        - Service document: " . $collection->sac_service . "\n";
					}
				}	
			}
		}
	}

	print "\n\n";
	
	if (true) {
		print "About to deposit file (" . $testfile . ") to " . $testdepositurl . "\n";
		if (empty($testuser)) { print "As: anonymous\n"; }
		else { print "As: " . $testuser . "\n"; }
		$testdr = $testsac->deposit(
		              $testdepositurl, $testuser, $testpw, $testobo, $testfile, $testformat, $testcontenttype);
		print "Received HTTP status code: " . $testdr->sac_status . 
		      " (" . $testdr->sac_statusmessage . ")\n";
		
		if ($testdr->sac_status == 201) {
			print " - ID: " . $testdr->sac_id . "\n";
			print " - Title: " . $testdr->sac_title . "\n";
			print " - Content: " . $testdr->sac_content_src . 
			      " (" . $testdr->sac_content_type . ")\n";
			foreach ($testdr->sac_authors as $author) {
				print "  - Author: " . $author . "\n";
			}
			foreach ($testdr->sac_contributors as $contributor) {
				print "  - Contributor: " . $contributor . "\n";
			}
			foreach ($testdr->sac_links as $link) {
				print "  - Link: " . $link . "\n";
			}
			print " - Summary: " . $testdr->sac_summary . "\n";
			print " - Updated: " . $testdr->sac_updated . "\n";
			print " - Rights: " . $testdr->sac_rights . "\n";
			print " - Packaging: " . $testdr->sac_packaging . "\n";
			print " - Generator: " . $testdr->sac_generator . 
			      " (" . $testdr->sac_generator_uri . ")\n";
			print " - User agent: " . $testdr->sac_useragent . "\n";
			if (!empty($testdr->sac_noOp)) { print " - noOp: " . $testdr->sac_noOp . "\n"; }
		}
	}
	
	print "\n\n";
	
	if (false) {
		$testdepositURL = "http://client.swordapp.org/client/deposit/s-2";
		$testobo = "fail";
		print "About to deposit file (" . $testfile . ") to " . $testdepositurl . "\n";
		print "This deposit should fail!\n";
		if (empty($testuser)) { print "As: anonymous\n"; }
		else { print "As: " . $testuser . "\n"; }
		$testdr = $testsac->deposit(
		              $testdepositurl, $testuser, $testpw, $testobo, $testfile, "METS/SWAP");
		print "Received HTTP status code: " . $testdr->sac_status . 
		      " (" . $testdr->sac_statusmessage . ")\n";
		
		if ($testdr->sac_status == 201) {
			print " - ID: " . $testdr->sac_id . "\n";
			print " - Title: " . $testdr->sac_title . "\n";
			print " - Content: " . $testdr->sac_content_src . 
			      " (" . $testdr->sac_content_type . ")\n";
			foreach ($testdr->sac_authors as $author) {
				print "  - Author: " . $author . "\n";
			}
			foreach ($testdr->sac_contributors as $contributor) {
				print "  - Contributor: " . $contributor . "\n";
			}
			foreach ($testdr->sac_links as $link) {
				print "  - Link: " . $link . "\n";
			}
			print " - Summary: " . $testdr->sac_summary . "\n";
			print " - Updated: " . $testdr->sac_updated . "\n";
			print " - Rights: " . $testdr->sac_rights . "\n";
			print " - Packaging: " . $testdr->sac_packaging . "\n";
			print " - Generator: " . $testdr->sac_generator . 
			      " (" . $testdr->sac_generator_uri . ")\n";
			print " - User agent: " . $testdr->sac_useragent . "\n";
			if (!empty($testdr->sac_noOp)) { print " - noOp: " . $testdr->sac_noOp . "\n"; }
		} else {
			// $testdr should be an instance of SWORDAPPErrorDocument
			print " - Error URI: " . $testdr->sac_erroruri . "\n";
			if (!empty($testdr->sac_generator)) {
				print "  - Generator: " . $testdr->sac_generator . 
			              " (" . $testdr->sac_generator_uri . ")\n";
			}
			print "  - Title: " . $testdr->sac_title . "\n";
			print "  - Summary: " . $testdr->sac_summary . "\n";
		}
	}
?>
