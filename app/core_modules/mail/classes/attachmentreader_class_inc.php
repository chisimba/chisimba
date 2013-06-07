<?

class AttachmentReader
{
	private $debug = FALSE;
	private $connected = FALSE;
	private $mbox;
	
	/**
	 * Constructor - Establishes Connection to Email Host
	 */
	function __construct($emailHost, $emailPort, $emailOptions, $emailLogin, $emailPassword, $catchAllBase)
	{
		$hostString = "{{$emailHost}:{$emailPort}/{$emailOptions}}";
		
		$this->mbox = imap_open ($hostString,  $emailLogin, $emailPassword, OP_SILENT) or die("can't connect: " . imap_last_error());
		
		// Remove all mail that may have been marked for deletion
		// via another process, E.g script that died
		imap_expunge($this->mbox);
		
		$this->connected = TRUE;
		$this->catchAllBase = $catchAllBase;
	}
	
	/**
	 * Method to rerieve the number of messages
	 * @return int Number of Messages
	 */
	function getNumMessages()
	{
		return imap_num_msg($this->mbox);
	}
	
	/**
	 * Method to get Details from the Email Header
	 * @param int $emailNum Email Number in Mailbox
	 * @return array Details of the Header
	 */
	function getEmailDetails($emailNum)
	{
        $headerInfo = imap_headerinfo($this->mbox, $emailNum);
        
        $sender = $headerInfo->from[0]->mailbox.'@'.$headerInfo->from[0]->host;
		
		if (isset($headerInfo->from[0]->personal)) {
			$senderName = $headerInfo->from[0]->personal;
		} else {
			$senderName = $sender;
		}
		
		$subject = $this->fixUTFText($headerInfo->subject);
		$size = $headerInfo->Size;
		$date = $headerInfo->date;
		
		$messageId = $headerInfo->message_id;
		
		if (isset($headerInfo->ccaddress)) {
			$cc = $headerInfo->ccaddress;
		} else {
			$cc = '';
		}
		
		
		// Message Body - Current Not Required
		$body = imap_fetchbody($this->mbox, $emailNum, "1.1");
		if ($body == "") {
			$body = imap_fetchbody($this->mbox, $emailNum, "1");
		}
		
		$body = quoted_printable_decode($body); 
		
		
		// Done this way, as email may be sent via To, CC, BCC
		// Also may not be first recipient
		$possibleFolderDestinations = array();
		
		if (isset($headerInfo->to)) {
			foreach($headerInfo->to as $address)
			{
					$possibleFolderDestinations[] = $address->mailbox.'@'.$address->host;
			}
		}
		
		if (isset($headerInfo->cc)) {
			foreach($headerInfo->cc as $address)
			{
					$possibleFolderDestinations[] = $address->mailbox.'@'.$address->host;
			}
		}
		
		if (isset($headerInfo->bcc)) {
			foreach($headerInfo->bcc as $address)
			{
					$possibleFolderDestinations[] = $address->mailbox.'@'.$address->host;
			}
		}
        
        $addXHeaders = array();
		
		$xForwardedTo = $this->getXForwardedTo(imap_fetchheader($this->mbox, $emailNum));
		if ($xForwardedTo != '') {
			$possibleFolderDestinations = array_merge($possibleFolderDestinations, $this->getEmailFromXHeaders($xForwardedTo));
			$addXHeaders = array_merge($addXHeaders, $this->getEmailFromXHeaders($xForwardedTo));
		}
        
        $xForwardedFor = $this->getXForwardedFor(imap_fetchheader($this->mbox, $emailNum));
		if ($xForwardedFor != '') {
			$possibleFolderDestinations = array_merge($possibleFolderDestinations, $this->getEmailFromXHeaders($xForwardedFor));
			$addXHeaders = array_merge($addXHeaders, $this->getEmailFromXHeaders($xForwardedFor));
		}
        
        $xEnvelopeTo = $this->getXEnvelopeTo(imap_fetchheader($this->mbox, $emailNum));
		if ($xEnvelopeTo != '') {
			$possibleFolderDestinations = array_merge($possibleFolderDestinations, $this->getEmailFromXHeaders($xEnvelopeTo));
			$addXHeaders = array_merge($addXHeaders, $this->getEmailFromXHeaders($xEnvelopeTo));
		}
        
        $envelopeTo = $this->getEnvelopeTo(imap_fetchheader($this->mbox, $emailNum));
        
		if ($envelopeTo != '') {
			$possibleFolderDestinations = array_merge($possibleFolderDestinations, $this->getEmailFromXHeaders($envelopeTo));
			$addXHeaders = array_merge($addXHeaders, $this->getEmailFromXHeaders($envelopeTo));
		}
        
        $xOrigTo = $this->getXOrigTo(imap_fetchheader($this->mbox, $emailNum));
		if ($xOrigTo != '') {
			$possibleFolderDestinations = array_merge($possibleFolderDestinations, $this->getEmailFromXHeaders($xOrigTo));
			$addXHeaders = array_merge($addXHeaders, $this->getEmailFromXHeaders($xOrigTo));
		}
        
        $xOriginalTo = $this->getXOriginalTo(imap_fetchheader($this->mbox, $emailNum));
		if ($xOriginalTo != '') {
			$possibleFolderDestinations = array_merge($possibleFolderDestinations, $this->getEmailFromXHeaders($xOriginalTo));
			$addXHeaders = array_merge($addXHeaders, $this->getEmailFromXHeaders($xOriginalTo));
		}
        
        $xResentFrom = $this->getXResentFrom(imap_fetchheader($this->mbox, $emailNum));
		if ($xResentFrom != '') {
			$possibleFolderDestinations = array_merge($possibleFolderDestinations, $this->getEmailFromXHeaders($xResentFrom));
			$addXHeaders = array_merge($addXHeaders, $this->getEmailFromXHeaders($xResentFrom));
		}
		
		return array(
			//'headerInfo'=>$headerInfo, // For Debugging
			
			'possibleDestinations' => array_unique($possibleFolderDestinations),
			//'addXHeaders' => $addXHeaders, // For Debugging - to check whether it picked up any
			
			'sender'=>$sender,
			
			'cc'=>$cc,
			'messageId'=>$messageId,
			
			'attachments'=>count($this->getSaveAttachments($emailNum, FALSE)),
			'sendername'=>$senderName,
			'subject'=>$subject,
			'size'=>$size,
			'senddate'=>$date,
			
			'messageBody'=>$body
		);
	}
    
    private function getEmailFromXHeaders($str)
    {
        $emails = array();
        $strParts = explode(' ', $str);
        
        if ($strParts > 0) {
            foreach ($strParts as $part)
            {
                $part = trim((string)$part);
                
                if (filter_var($part, FILTER_VALIDATE_EMAIL) != FALSE) {
                    $emails[] = $part;
                }
            }
        }
        
        return $emails;
        
    }
	
	/**
	 * Method to get the X-Forwarded-To header using Regular Expressions
	 * @param string $str Header from imap_fetchheader()
	 * @return string X-Forwarded-To Email Addresses
	 */
	private function getXForwardedTo($str)
	{
		if (preg_match('/^X-Forwarded-To: (.*)/im', $str, $regs)) {
			return $regs[1];
		} else {
			return "";
		}
	}
    
    /**
	 * Method to get the X-Forwarded-For header using Regular Expressions
	 * @param string $str Header from imap_fetchheader()
	 * @return string X-Forwarded-For Email Addresses
	 */
	private function getXForwardedFor($str)
	{
		if (preg_match('/^X-Forwarded-For: (.*)/im', $str, $regs)) {
			return $regs[1];
		} else {
			return "";
		}
	}
    
    /**
	 * Method to get the Envelope-To header using Regular Expressions
	 * @param string $str Header from imap_fetchheader()
	 * @return string Envelope-To Email Addresses
	 */
	private function getEnvelopeTo($str)
	{
		if (preg_match('/^Envelope-To: (.*)/im', $str, $regs)) {
			return $regs[1];
		} else {
			return "";
		}
	}
    
    /**
	 * Method to get the X-Envelope-To header using Regular Expressions
	 * @param string $str Header from imap_fetchheader()
	 * @return string X-Envelope-To Email Addresses
	 */
	private function getXEnvelopeTo($str)
	{
		if (preg_match('/^X-Envelope-To: (.*)/im', $str, $regs)) {
			return $regs[1];
		} else {
			return "";
		}
	}
    
    /**
	 * Method to get the X-Orig-To header using Regular Expressions
	 * @param string $str Header from imap_fetchheader()
	 * @return string X-Orig-To Email Addresses
	 */
	private function getXOrigTo($str)
	{
		if (preg_match('/^X-Orig-To: (.*)/im', $str, $regs)) {
			return $regs[1];
		} else {
			return "";
		}
	}
    
    /**
	 * Method to get the X-Original-To header using Regular Expressions
	 * @param string $str Header from imap_fetchheader()
	 * @return string X-Original-To Email Addresses
	 */
	private function getXOriginalTo($str)
	{
		if (preg_match('/^X-Original-To: (.*)/im', $str, $regs)) {
			return $regs[1];
		} else {
			return "";
		}
	}
    
    /**
	 * Method to get the Resent-From header using Regular Expressions
	 * @param string $str Header from imap_fetchheader()
	 * @return string Resent-From Email Addresses
	 */
	private function getXResentFrom($str)
	{
		if (preg_match('/^Resent-From: (.*)/im', $str, $regs)) {
			return $regs[1];
		} else {
			return "";
		}
	}
	
	/**
	 * Method to Save Email Attachments to a Folder
	 * @param int $emailNum Email Number in Mailbox
	 * @param boolean $doSave Should the attachments be saved to a location?
	 * @param string $saveDir Location to Save Files
	 * @return array Files Saved
	 */
	public function getSaveAttachments($emailNum, $doSave=FALSE, $saveDir='')
	{
		$message = array();
        $message["attachment"]["type"][0] = "text";
        $message["attachment"]["type"][1] = "multipart";
        $message["attachment"]["type"][2] = "message";
        $message["attachment"]["type"][3] = "application";
        $message["attachment"]["type"][4] = "audio";
        $message["attachment"]["type"][5] = "image";
        $message["attachment"]["type"][6] = "video";
        $message["attachment"]["type"][7] = "other";
		
		$structure = imap_fetchstructure($this->mbox, $emailNum , FT_UID);    
        $parts = $structure->parts;
        
        $listOfFiles = array();
        
		$fpos = 0;

        foreach ($parts as $part)
        {
			$fpos++;
            
            
            if (isset($part->parts)) {
                
                $fSubPos = 0;
                
                foreach ($part->parts as $subpart)
                {
                    $fSubPos++;
                    
                    if (isset($subpart->disposition) && strtoupper($subpart->disposition) == "ATTACHMENT") {
                        
                        $filename = '';
                        
						// Check against parameters
						if (isset($subpart->parameters)) {
							foreach ($subpart->parameters as $dParam)
							{
								if ($dParam->attribute == 'NAME') {
									$filename .= $dParam->value;
								}
							}
						}
                        
                        // If blank, check the old way - against dparameters
                        if ($filename == '' && isset($part->dparameters)) {
                            foreach ($part->dparameters as $dParam)
                            {
                                if ($dParam->attribute == 'FILENAME') {
                                    $filename .= $dParam->value;
                                }
                            }
                        }
                        
                        $filename = "$filename";
                        $filename = $this->fixUTFText($filename);
                        
                        // Needed, else Eror
                        $filename = str_replace('/', '', $filename);
                        
						// Only Add if Filename is Not Blank
						if ($filename != '') {
						
							if ($doSave) {
								$mege = imap_fetchbody($this->mbox,$emailNum, $fpos.'.'.$fSubPos);
								
								$data = $this->getdecodevalue($mege, $subpart->encoding);
								
								
								@unlink($saveDir.'/'.$filename);
								file_put_contents($saveDir.'/'.$filename, $data);
							}
							
							$listOfFiles[] = $filename;
						}
                    }
                    
                }
            }
            
            if(isset($part->disposition) && (strtoupper($part->disposition) == "ATTACHMENT" || strtoupper($part->disposition) == 'INLINE'))
            {
                
                $filename = '';
                
                /*
                
                Why is filename appended, not set?
                
                Answer: In the case of long filenames, they are broken up into multiple lines
                
                */
                
				// Check against parameters
				if (isset($part->parameters)) {
					foreach ($part->parameters as $dParam)
					{
						if ($dParam->attribute == 'NAME') {
							$filename .= $dParam->value;
						}
					}
				}
                
                // If blank, check the old way - against dparameters
                if ($filename == '' && isset($part->dparameters)) {
                    foreach ($part->dparameters as $dParam)
                    {
                        if ($dParam->attribute == 'FILENAME') {
                            $filename .= $dParam->value;
                        }
                    }
                }
                
                $filename = "$filename";
				$filename = $this->fixUTFText($filename);
				
				// Needed, else Eror
				$filename = str_replace('/', '', $filename);
                
				// Only Add if Filename is Not Blank
				if ($filename != '') {
					
					if ($doSave) {
						
						$mege = imap_fetchbody($this->mbox,$emailNum, $fpos);
						
						$data = $this->getdecodevalue($mege, $part->encoding);
						@unlink($saveDir.'/'.$filename);
						file_put_contents($saveDir.'/'.$filename, $data);
					}
					
					$listOfFiles[] = $filename;
				}
				
            }
        }
		
		return $listOfFiles;
	}
	
	private function fixUTFText($text)
	{
		$text = imap_utf8($text);
		
		// Detect for Japanese Encoding
		$has_ISO_2022_JP = substr_count($text, '=?ISO-2022-JP?');
		
		$textParts = imap_mime_header_decode($text);
		
		$text = '';
		
		foreach ($textParts as $textPart)
		{
			$text .= $textPart->text;
		}
		
		// For XSS
		$text = trim(preg_replace('%</?script.*?>%si', ' ', $text));
		
		// Apply Special Encoding for Japanese
		if ($has_ISO_2022_JP > 0) {
			$text = mb_convert_encoding($text,"UTF-8","JIS");
		}
		
		return $text;
	}
	
	/**
	 * Method to Delete an Eamil
	 * @param int $emailNum Email Number in Mailbox
	 *
	 * Email is Marked for Deletion. Deleted when expunged, on closing Email box
	 */
	public function deleteEmail($emailNum)
	{
		if (!$this->debug) {
            // Would love to expunge immediately after this, but messes up the email order
			return imap_delete($this->mbox, $emailNum);
		}
	}
	
	/**
	 * Close and Delete emails when unset.
	 */
	function __destruct()
	{
		if ($this->connected) {
			imap_expunge($this->mbox);
			imap_close($this->mbox);
		}
	}
	
	/**
	 * Method to Decode Attachment
	 */
	function getdecodevalue($message,$coding)
    {
		if ($coding == 0) 
		{ 
		   $message = imap_8bit($message); 
		} 
		elseif ($coding == 1) 
		{ 
		  $message = imap_8bit($message); 
		} 
		elseif ($coding == 2) 
		{ 
		   $message = imap_binary($message); 
		} 
		elseif ($coding == 3) 
		{ 
            $message=imap_base64($message); 
        } 
		elseif ($coding == 4) 
		{ 
		   $message = imap_qprint($message); 
		} 
		elseif ($coding == 5) 
		{ 
            $message = imap_base64($message); 
		} 
		return $message;
	}
	
	
}
