<?php

/**
 * Fast Mime Mail parser Class using PHP's MailParse Extension
 * @author gabe@fijiwebdesign.com
 * @url http://www.fijiwebdesign.com/
 * @license http://creativecommons.org/licenses/by-sa/3.0/us/
 * @version $Id$
 */
class MimeMailParser {
	
	/**
	 * PHP MimeParser Resource ID
	 */
	public $resource;
	
	/**
	 * A file pointer to email
	 */
	public $stream;
	
	/**
	 * A text of an email
	 */
	public $data;
	
	/**
	 * Stream Resources for Attachments
	 */
	public $attachment_streams;
	
	/**
	 * Inialize some stuff
	 * @return 
	 */
	public function __construct() {
		$this->attachment_streams = array();
	}
	
	/**
	 * Free the held resouces
	 * @return void
	 */
	public function __destruct() {
		// clear the email file resource
		if (is_resource($this->stream)) {
			fclose($this->stream);
		}
		// clear the MailParse resource
		if (is_resource($this->resource)) {
			mailparse_msg_free($this->resource);
		}
		// remove attachment resources
		foreach($this->attachment_streams as $stream) {
			fclose($stream);
		}
	}
	
	/**
	 * Set the file path we use to get the email text
	 * @return Object MimeMailParser Instance
	 * @param $mail_path Object
	 */
	public function setPath($path) {
		// should parse message incrementally from file
		$this->resource = mailparse_msg_parse_file($path);
		$this->stream = fopen($path, 'r');
		$this->parse();
		return $this;
	}
	
	/**
	 * Set the Stream resource we use to get the email text
	 * @return Object MimeMailParser Instance
	 * @param $stream Resource
	 */
	public function setStream($stream) {

		// streams have to be cached to file first
		if (get_resource_type($stream) == 'stream') {
			$tmp_fp = tmpfile();
			if ($tmp_fp) {
				while(!feof($stream)) {
					fwrite($tmp_fp, fread($stream, 2028));
				}
				fseek($tmp_fp, 0);
				$this->stream =& $tmp_fp;
			} else {
				throw new Exception('Could not create temporary files for attachments. Your tmp directory may be unwritable by PHP.');
				return false;
			}
			fclose($stream);
		} else {
			$this->stream = $stream;
		}
		
		$this->resource = mailparse_msg_create();
		// parses the message incrementally low memory usage but slower
		while(!feof($this->stream)) {
			mailparse_msg_parse($this->resource, fread($this->stream, 2082));
		}
		$this->parse();
		return $this;
	}
	
	/**
	 * Set the email text
	 * @return Object MimeMailParser Instance 
	 * @param $data String
	 */
	public function setText($data) {
		$this->resource = mailparse_msg_create();
		// does not parse incrementally, fast memory hog might explode
		mailparse_msg_parse($this->resource, $data);
		$this->data = $data;
		$this->parse();
		return $this;
	}
	
	/**
	 * Parse the Message into parts
	 * @return void
	 * @private
	 */
	private function parse() {
		$structure = mailparse_msg_get_structure($this->resource);
		$this->parts = array();
		foreach($structure as $part_id) {
			$part = mailparse_msg_get_part($this->resource, $part_id);
			$this->parts[$part_id] = mailparse_msg_get_part_data($part);
		}
	}
	
	/**
	 * Retrieve the Email Headers
	 * @return Array
	 */
	public function getHeaders() {
		if (isset($this->parts[1])) {
			return $this->getPartHeaders($this->parts[1]);
		} else {
			throw new Exception('MimeMailParser::setPath() or MimeMailParser::setText() must be called before retrieving email headers.');
		}
		return false;
	}
	
	/**
	 * Retrieve a specific Email Header
	 * @return String
	 * @param $name String Header name
	 */
	public function getHeader($name) {
		if (isset($this->parts[1])) {
			$headers = $this->getPartHeaders($this->parts[1]);
			if (isset($headers[$name])) {
				return $headers[$name];
			}
		} else {
			throw new Exception('MimeMailParser::setPath() or MimeMailParser::setText() must be called before retrieving email headers.');
		}
		return false;
	}
	
	/**
	 * Returns the email message body in the specified format
	 * @return Mixed String Body or False if not found
	 * @param $type Object[optional]
	 */
	public function getMessageBody($type = 'text') {
		$body = false;
		$mime_types = array(
			'text'=> 'text/plain',
			'html'=> 'text/html'
		);
		if (in_array($type, array_keys($mime_types))) {
			foreach($this->parts as $part) {
				if ($this->getPartContentType($part) == $mime_types[$type]) {
					$body = $this->getPartBody($part);
				}
			}
		} else {
			throw new Exception('Invalid type specified for MimeMailParser::getMessageBody. "type" can either be text or html.');
		}
		return $body;
	}
	
	/**
	 * Returns the attachments
	 * @return Array
	 * @param $type Object[optional]
	 */
	public function getAttachments() {
		$attachments = array();
		$disposition = 'attachment';
		foreach($this->parts as $part) {
			if ($this->getPartContentDisposition($part) == $disposition) {
				$attachments[] = base64_decode($this->getPartBody($part));
			}
		}
		return $attachments;
	}
	
	/**
	 * Returns the attachments as stream resources (file pointers)
	 * @return Array
	 * @param $type Object[optional]
	 */
	public function getAttachmentsAsStreams() {
		$attachments = array();
		$disposition = 'attachment';
		foreach($this->parts as $part) {
			if ($this->getPartContentDisposition($part) == $disposition) {
				$attachments[] = $this->getAttachmentStream($part);
			}
		}
		array_merge($this->attachment_streams, $attachments);
		return $attachments;
	}
	
	/**
	 * Return the Headers for a MIME part
	 * @return Array
	 * @param $part Array
	 */
	private function getPartHeaders($part) {
		if (isset($part['headers'])) {
			return $part['headers'];
		}
		return false;
	}
	
	/**
	 * Return a Specific Header for a MIME part
	 * @return Array
	 * @param $part Array
	 * @param $header String Header Name
	 */
	private function getPartHeader($part, $header) {
		if (isset($part['headers'][$header])) {
			return $part['headers'][$header];
		}
		return false;
	}
	
	/**
	 * Return the ContentType of the MIME part
	 * @return String
	 * @param $part Array
	 */
	private function getPartContentType($part) {
		if (isset($part['content-type'])) {
			return $part['content-type'];
		}
		return false;
	}
	
	/**
	 * Return the Content Disposition
	 * @return String
	 * @param $part Array
	 */
	private function getPartContentDisposition($part) {
		if (isset($part['content-disposition'])) {
			return $part['content-disposition'];
		}
		return false;
	}
	
	/**
	 * Retrieve the Body of a MIME part
	 * @return String
	 * @param $part Object
	 */
	private function getPartBody(&$part) {
		$body = '';
		if ($this->stream) {
			$body = $this->getPartBodyFromFile($part);
		} else if ($this->data) {
			$body = $this->getPartBodyFromText($part);
		} else {
			throw new Exception('MimeMailParser::setPath() or MimeMailParser::setText() must be called before retrieving email parts.');
		}
		return $body;
	}
	
	/**
	 * Retrieve the Body from a MIME part from file
	 * @return String Mime Body Part
	 * @param $part Array
	 */
	private function getPartBodyFromFile(&$part) {
		$start = $part['starting-pos-body'];
		$end = $part['ending-pos-body'];
		fseek($this->stream, $start, SEEK_SET);
		$body = fread($this->stream, $end-$start);
		return $body;
	}
	
	/**
	 * Retrieve the Body from a MIME part from text
	 * @return String Mime Body Part
	 * @param $part Array
	 */
	private function getPartBodyFromText(&$part) {
		$start = $part['starting-pos-body'];
		$end = $part['ending-pos-body'];
		$body = substr($this->data, $start, $end-$start);
		return $body;
	}
	
	/**
	 * Read the attachment Body and save temporary file resource
	 * @return String Mime Body Part
	 * @param $part Array
	 */
	private function getAttachmentStream(&$part) {
		$temp_fp = tmpfile();
		if ($temp_fp) {
			if ($this->stream) {
				$start = $part['starting-pos-body'];
				$end = $part['ending-pos-body'];
				fseek($this->stream, $start, SEEK_SET);
				$len = $end-$start;
				$written = 0;
				$write = 2028;
				$body = '';
				while($written < $len) {
					if (($written+$write < $len )) {
						$write = $len - $written;
					}
					$part = fread($this->stream, $write);
					fwrite($temp_fp, base64_decode($part));
					$written += $write;
				}
				fseek($temp_fp, 0, SEEK_SET);
			} else if ($this->text) {
				$attachment = base64_decode($this->getPartBodyFromText($part));
				fwrite($temp_fp, $attachment, strlen($attachment));
			}
		} else {
			throw new Exception('Could not create temporary files for attachments. Your tmp directory may be unwritable by PHP.');
			return false;
		}
		return $temp_fp;
	}
	
}


?>