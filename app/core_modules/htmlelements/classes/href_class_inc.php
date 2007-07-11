<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML interface class
require_once("ifhtml_class_inc.php");

    /* Simple class for outputting '<a href' links
    * @author James Scoble
    * @param $link
    * @param $text
    * @param $other
    */
class href implements ifhtml
{
        public $link;
        public $text;
        public $other;

        public function href($link=Null,$text=Null,$other=Null)
        {
            $this->link=$link;
            $this->text=$text;
            $this->other=$other;
        }

        public function show()
        {
            $href="<a href='".$this->link."' ".$this->other.">".$this->text."</a>\n";
            return $href;
        }

        public function showlink($link,$text,$other=Null)
        {
            $href="<a href='".$link."' ".$other.">".$text."</a>\n";
            return $href;
        }
    } // end of class

?>