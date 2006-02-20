<?php 
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 

/**
* Class to create and display headings using the <Hn> tag where n=1 to 6
* This can be used to insert text into the appropriate heading, and can also
* specify the cssClass to use. It defaults to <H3>.
* 
* The main purpose for creating this class was  to provide a simple example
* of how to extend the framework with header classes, and demonstrate how to
* use these extensions in writing code.
* 
* @author Derek Keats 
*/

class htmlHeading 
{
    /**
    * 
    * @var int $type The type of header to produce (H1, H2, etc)
    */
    var $type = 3;

    /**
    * 
    * @var string $id The CSS ID of the header if used
    */
    var $id;

    /**
    * 
    * @var string $cssClass The CSS class of the header if used
    */
    var $cssClass;

    /**
    * 
    * @var string $str The text to place between the header tags
    */
    var $str;

    /**
    * @var string $align How the header should align on the page
    * Added 2005-04-07 by James Scoble
    */
    var $align;
    
    /**
    * Method to show the heading
    * 
    * @return The heading complete as a string
    */
    function show()
    {
        $ret = "<h" . $this->type;
        if ($this->id) {
            $ret .= " id=\"" . $this->id . "\"";
        } 
        if ($this->cssClass) {
            $ret .= " class=\"" . $this->cssClass . "\"";
        } 
        if ($this->align) {
            $ret .= " align=\"" . $this->align . "\"";
        } 
        $ret .= ">" . $this->str . "</h" . $this->type . ">";
        return $ret;
    } 
} 

?>
