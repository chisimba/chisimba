<?php
/**
*
* Class for providing the output for the makesingle template for the timeline module
*
* @package timeline
* @author Derek Keats
*
*/
class makesingle extends object {
    
    /**
    * @var $objLanguage String object property for holding the 
    * language object
    * @access public
    */
    public $objLanguage;


    /**
    * @var $objH String object property for holding the 
    * heading object from htmlelements
    */
    public $objH;
    
    /**
    * @var $objGetIcon String Object A string to hold the geticon object from
    *   htmlelements
    * @access private
    */
    private $objGetIcon;
    

    /**
    *
    * Constructor method to define the table
    *
    */
    function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
    }
    
    /**
     * 
     * Show method to build the menu return it for rendering
     * @access public
     * @return The formatted menu
     * 
     */
    public function show()
    {
 		$ret = $this->__getStyle();
 		$ret .= $this->__getForm();
        return  $ret;
    }

    
    /**
    * 
    * Return the style for the form
    * @return string The style as a string
    * @access private
    * 
    */
    private function __getStyle() {
        return "<style type=\"text/css\">\n"
		  . ".label {\n"
		  . "	text-align:right\n"
		  . "}\n</style>";
    }

	private function __getForm() {
	    $ret = "<p>" . $this->objLanguage->languageText("mod_timeline_createinstr", "timeline")
		  . "</p><form id=\"eventdetails\">\n"
	      . "<table>\n<tr><td class=\"label\"><label>"
		  . $this->objLanguage->languageText("mod_timeline_eventtitle", "timeline")
		  . "</label></td><td><input size=\"50\" id=\"etitle\" /></td>\n"
		  . "</tr>\n<tr><td class=\"label\"><label>"
		  . $this->objLanguage->languageText("mod_timeline_eventstart", "timeline")
		  . "*</label></td><td><input  size=\"50\" id=\"estart\" value=\"2007-01-01\" /></td>"
		  . "</tr>\n<tr><td class=\"label\"><label>"
		  . $this->objLanguage->languageText("mod_timeline_eventend", "timeline")
		  . "</label></td><td><input size=\"50\" id=\"eend\" /></td>"
		  . "</tr>\n<tr><td class=\"label\"><label>"
		  . $this->objLanguage->languageText("mod_timeline_eventlink", "timeline")
		  . "</label></td><td><input size=\"50\" id=\"elink\" /></td></tr>\n"
		  . "<tr><td class=\"label\"><label>"
		  . $this->objLanguage->languageText("mod_timeline_eventimgurl", "timeline")
		  . "</label></td><td><input size=\"50\" id=\"eimg\" /></td></tr>\n"
		  . "<td class=\"label\"><label>"
		  . $this->objLanguage->languageText("mod_timeline_eventdesc", "timeline")
		  . "</label></td><td><textarea rows=\"5\" cols=\"50\" id=\"edesc\" >\n"
		  . "</textarea></td></tr>\n</table>\n\n"
		  . "<a href=\"javascript:generateEventXML()\">"
		  . $this->objLanguage->languageText("mod_timeline_generatexml", "timeline")
		  . "</a><br /><br />\n<textarea name=\"results\" id=\"results\" rows=\"10\" cols=\"80\">"
		  . "</textarea></form>";
		return $ret;
	}
  
}  #end of class
?>