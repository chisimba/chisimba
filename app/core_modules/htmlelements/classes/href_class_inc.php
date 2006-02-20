<?
    /* Simple class for outputting '<a href' links
    * @author James Scoble
    * @param $link
    * @param $text
    * @param $other
    */

    class href
    {
        var $link;
        var $text;
        var $other;

        function href($link=Null,$text=Null,$other=Null)
        {
            $this->link=$link;
            $this->text=$text;
            $this->other=$other;
        }

        function show()
        {
            $href="<a href='".$this->link."' ".$this->other.">".$this->text."</a>\n";
            return $href;
        }

        function showlink($link,$text,$other=Null)
        {
            $href="<a href='".$link."' ".$other.">".$text."</a>\n";
            return $href;
        }
    } // end of class

?>
