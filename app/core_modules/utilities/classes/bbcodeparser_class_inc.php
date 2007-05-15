<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

class bbcodeparser extends object
{
	public function init()
	{
		if(!class_exists('HTML_BBCodeParser'))
		{
			@include_once('HTML/BBCodeParser.php');
		}
		//	throw new customException("Unable to locate PEAR::BBCodeParser, please install it with pear install --alldeps html_bbcodeparser!");
		
	}

	/**
	 * Method to take a text string, parse it for BBCode and return a XHTML compliant string with divs
	 *
	 * @param string $text
	 * @return string
	 */
	public function parse4bbcode($text)
	{
		
        if(class_exists('HTML_BBCodeParser')) {
            $parser = new HTML_BBCodeParser(parse_ini_file('BBCodeParser.ini'));
            //log_debug("set the parser");
            $parser->setText($text);
            $parser->parse();
            return $parser->getParsed();
        } else {
            return $text;
        }
	}
}
?>