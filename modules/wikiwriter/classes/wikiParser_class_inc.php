<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run'])
{
	    die("You cannot view this page directly");
} 
// end security check

/**
* WikiParser Object 
*
* This class will parse html pages taken from selected wikis and return
* a DOM Document fragment of just the content. 
*
* @author Ryan Whitney, ryan@greenlikeme.org 
*/
class wikiParser extends object
{

	// Personal debugger - TODO: REMOVE BEFORE COMMITTING FOR PRODUCTION!
	public function dbg($sErr){
		$handle = fopen('error_log/my_debug.log', 'a');
		fwrite($handle, "[" . strftime("%b %d %Y %H:%M:%S") ."]/classes/wwPage :: " . $sErr . "\n");
		fclose($handle);
	}

	/**
	 * Constructor
 	 */ 
	public function init()
	{
	}

	/*
	 * Takes in a DOMDocument object by reference and returns the wiki content
	 *
	 * @access public
	 * @params DOMDocument $dom The wiki page
	 * @return DOMElement Just the wiki content
	 */
	public function parse(& $dom)
	{
		//Figure out which type of wiki we're dealing with
		$pType = $this->getType($dom->saveHTML());

		// Return the appropriate DOMElement 
		switch($pType){
			case "chisimba":
				return $dom->getElementById('contentcontent');
			break;
			case "mediawiki":
				// First we grab the content
				$tmpNode = $dom->getElementById('content');

				// Check to see if siteNotice div is there, if so, remove
				if($dom->getElementById('siteNotice'))
					$tmpNode->removeChild($dom->getElementById('siteNotice'));
				
				return $tmpNode;
			break;
			// TODO: return an error to the user that one of the pages isn't supported - should highlight as well
		}
	}

	/*
     * Returns the type of wiki from the DOMDocument passed in
	 * 
	 * @access private
 	 * @params string $html String of HTML content 
 	 * @return string wiki type identifier (chisimba, mediawiki, etc)
	 **************************************************/
	private function getType($html)
	{
		if(preg_match('/<div id="leftcontent">\s+<h1>Wiki/', $html))
		{
			$this->dbg('found chisimba!');
			return 'chisimba';
		} 
		else if(preg_match('/MediaWiki/', $html))
		{
			$this->dbg('found mediawiki!');
			return 'mediawiki';	
		}
		return 0;
	}
}
?>
