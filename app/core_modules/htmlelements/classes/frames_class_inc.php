<?php
class frames
{
   //var $rows = array();
	//var $cols = array();
 	//var $url = array(); 

	function show()
    {
		return $this->Frame($url);
    }

	 // the framesetCols tag defines a new set of frames sorted by column
    function FrameSetCols($cols = array())
    {
     $options = "" ;
     if($cols != -1)
        {
        $ret = "<FRAMESET COLS =\"" ;
        for($i=0 ; $i<sizeof($cols) ; $i++)
                {
                 if($i == sizeof($cols)-1)
                        echo $cols[$i] ;
                 else
                        echo $cols[$i] . ", " ;
                }
         $ret .= "\">\n" ;
        }
		return $ret;
    }

    // the framesetrows tag defines a new set of frames sorted by rows
    function FrameSetRows($rows = array())
    {
     $options = "" ;
     if($rows != -1)
        {
        $row =  "<FRAMESET ROWS =\"" ;
        for($i=0 ; $i<sizeof($rows) ; $i++)
                {
                 if($i == sizeof($rows)-1)
                        echo $rows[$i] ;
                 else
                        echo $rows[$i] . ", " ;
                }
         $row .=  "\">\n" ;
        }
		return $row;
    }

    
	// the frame tag to define the page to load
	function Frame($url, $name = -1, $noresize = -1, $scrolling = -1, $frameborder = -1, $longdesc = -1,
    $marginheight = -1, $marginwidth = -1)
    {
     $options = "" ;
     if($name != -1)
        $options = " NAME =\"$name\"" ;
     if($noresize != -1)
        $options .= " NORESIZE" ;
     if($scrolling != -1)
        $options .= " SCROLLING =\"$scrolling\"" ;
     if($frameborder != -1)
        $options .= " FRAMEBORDER =\"$frameborder\"" ;
     if($longdesc != -1)
        $options .= " LONGDESC =\"$longdesc\"" ;
     if($marginheight != -1)
        $options .= " MARGINHEIGHT =\"$marginheight\"" ;
     if($marginwidth != -1)
        $options .= " MARGINWIDTH =\"$marginwidth\"" ;
                                                                                                                             
    $frame = "<FRAME SRC =\"$url\"" . $options . ">\n" ;
    return $frame;
	 }

    // the tag to close the frameset
    function FrameSetEnd()
    {
     $ret = "</FRAMESET>\n" ;
		return $ret;
    }

	function _buildFrame($cols,$rows,$url)
	{
		$ret = $this->FrameSetCols($cols) ;
		$ret .= $this->FrameSetRows($rows) ;
		//$ret .= $this->Frame($url) ;
		$ret .= $this->Frame($url) ;
		$ret .= $this->FrameSetEnd() ;
		return $ret;
	}

}//end class
?>