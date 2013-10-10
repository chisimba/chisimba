<?php
/* ----------- xmltag_Eportfolio class extends object------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for creating XML tags
 * @author Paul Mungai
 * @copyright 2008 University of the Western Cape & University of Nairobi
 */
class xmltag_Eportfolio extends object
{
    //my variables
    var $cdata;
    var $attributes;
    var $name;
    var $tags;
    var $parent;
    var $curtag;
    /**
     *
     * Intialiser for the xmltag_Eportfolio controller
     * @access public
     *
     */
    public function init() 
    {
        $this->attributes = array();
        $this->cdata = '';
        $this->name = '';
        $this->tags = array();
    }
    public function XMLTag_Eportfolio(&$parent) 
    {
        if (is_object($parent)) {
            $this->parent = &$parent;
        }
        $this->init();
    }
    public function add_subtag($name, $attributes = 0) 
    {
        $tag = new XMLTag_Eportfolio($this);
        $tag->set_name($name);
        if (is_array($attributes)) {
            $tag->set_attributes($attributes);
        }
        $this->tags[] = &$tag;
        $this->curtag = &$tag;
    }
    public function find_subtags_by_name($name) 
    {
        $result = array();
        $found = false;
        for ($i = 0; $i < $this->num_subtags(); $i++) {
            if (strtolower($this->tags[$i]->name) == strtolower($name)) {
                $found = true;
                $array2return[] = &$this->tags[$i];
            }
        }
        if ($found) {
            return $array2return;
        } else {
            return false;
        }
    }
    public function clear_subtags() 
    {
        // Traverse the structure, removing the parent pointers
        $numtags = sizeof($this->tags);
        $keys = array_keys($this->tags);
        foreach($keys as $k) {
            $this->tags[$k]->clear_subtags();
            unset($this->tags[$k]->parent);
        }
        // Clear the tags array
        $this->tags = array();
        unset($this->curtag);
    }
    public function remove_subtag($index) 
    {
        if (is_object($this->tags[$index])) {
            unset($this->tags[$index]->parent);
            unset($this->tags[$index]);
        }
    }
    public function num_subtags() 
    {
        return sizeof($this->tags);
    }
    public function add_attribute($name, $val) 
    {
        $this->attributes[strtolower($name) ] = $val;
    }
    public function clear_attributes() 
    {
        $this->attributes = array();
    }
    public function set_name($name) 
    {
        $this->name = strtolower($name);
    }
    public function set_attributes($attributes) 
    {
        $this->attributes = (is_array($attributes)) ? $attributes : array();
    }
    public function add_cdata($data) 
    {
        $this->cdata.= $data;
    }
    public function clear_cdata() 
    {
        $this->cdata = "";
    }
    public function write_file_handle($fh, $prepend_str = '') 
    {
        // Get the attribute string
        $attrs = array();
        $attr_str = '';
        foreach($this->attributes as $key => $val) {
            $attrs[] = strtolower($key) . "=\"$val\"";
        }
        if ($attrs) {
            $attr_str = join(" ", $attrs);
        }
        // Write out the start element
        $tagstr = "$prepend_str<{$this->name}";
        if ($attr_str) {
            $tagstr.= " $attr_str";
        }
        $keys = array_keys($this->tags);
        $numtags = sizeof($keys);
        // If there are subtags and no data (only whitespace),
        // then go ahead and add a carriage
        // return.  Otherwise the tag should be of this form:
        // <tag>val</tag>
        // If there are no subtags and no data, then the tag should be
        // closed: <tag attrib="val"/>
        $trimmeddata = trim($this->cdata);
        if ($numtags && ($trimmeddata == "")) {
            $tagstr.= ">\n";
        } elseif (!$numtags && ($trimmeddata == "")) {
            $tagstr.= "/>\n";
        } else {
            $tagstr.= ">";
        }
        fwrite($fh, $tagstr);
        // Write out the data if it is not purely whitespace
        if ($trimmeddata != "") {
            fwrite($fh, $trimmeddata);
        }
        // Write out each subtag
        foreach($keys as $k) {
            $this->tags[$k]->write_file_handle($fh, "$prepend_str\t");
        }
        // Write out the end element if necessary
        if ($numtags || ($trimmeddata != "")) {
            $tagstr = "</{$this->name}>\n";
            if ($numtags) {
                $tagstr = "$prepend_str$tagstr";
            }
            fwrite($fh, $tagstr);
        }
    }
}
?>
