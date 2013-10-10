<?php
/* ----------- XMLFile_Eportfolio class extends object------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for creating XML tags
 * @author Paul Mungai
 * @copyright 2008 University of the Western Cape & University of Nairobi
 */
class xmlfile_Eportfolio extends object
{
    var $parser;
    var $roottag;
    var $curtag;
    /**
     *
     * Intialiser for the XMLFile_Eportfolio controller
     * @access public
     *
     */
    public function init() 
    {
        $this->loadClass('xmltag_eportfolio', 'eportfolio');
        $this->roottag = "";
        $this->curtag = &$this->roottag;
    }
    // Until there is a suitable destructor mechanism, this needs to be
    // called when the file is no longer needed.  This calls the clear_subtags
    // method of the root node, which eliminates all circular references
    // in the xml tree.
    public function cleanup() 
    {
        if (is_object($this->roottag)) {
            $this->roottag->clear_subtags();
        }
    }
    public function create_root() 
    {
        $null = 0;
        $this->roottag = new XMLTag_Eportfolio($null);
        $this->curtag = &$this->roottag;
    }
    // read_xml_string
    // Same as read_file_handle, but you pass it a string.  Note that
    // depending on the size of the XML, this could be rather memory intensive.
    // Contributed July 06, 2001 by Kevin Howe
    public function read_xml_string($str) 
    {
        $this->init();
        $this->parser = xml_parser_create("UTF-8");
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, "_tag_open", "_tag_close");
        xml_set_character_data_handler($this->parser, "_cdata");
        xml_parse($this->parser, $str);
        xml_parser_free($this->parser);
    }
    public function read_file_handle($fh) 
    {
        $this->init();
        $this->parser = xml_parser_create("UTF-8");
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, "_tag_open", "_tag_close");
        xml_set_character_data_handler($this->parser, "_cdata");
        while ($data = fread($fh, 4096)) {
            if (!xml_parse($this->parser, $data, feof($fh))) {
                die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->parser)) , xml_get_current_line_number($this->parser)));
            }
        }
        xml_parser_free($this->parser);
    }
    public function write_file_handle($fh, $write_header = 1) 
    {
        if ($write_header) {
            fwrite($fh, "<?xml version='1.0' encoding='UTF-8'?>\n");
        }
        // Start at the root and write out all of the tags
        $this->roottag->write_file_handle($fh);
    }
    //##### UTIL #######
    public function _tag_open($parser, $tag, $attributes) 
    {
        //print "tag_open: $parser, $tag, $attributes\n";
        // If the current tag is not set, then we are at the root
        if (!is_object($this->curtag)) {
            $null = 0;
            $this->curtag = new XMLTag_Eportfolio($null);
            $this->curtag->set_name($tag);
            $this->curtag->set_attributes($attributes);
        } else { // otherwise, add it to the tag list and move curtag
            $this->curtag->add_subtag($tag, $attributes);
            $this->curtag = &$this->curtag->curtag;
        }
    }
    public function _tag_close($parser, $tag) 
    {
        // Move the current pointer up a level
        $this->curtag = &$this->curtag->parent;
    }
    public function _cdata($parser, $data) 
    {
        $this->curtag->add_cdata($data);
    }
}
?>
