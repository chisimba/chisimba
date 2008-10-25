<?php

/**
 * Description for require_once
 * 
 * PHP version 3
 * 
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML interface class
require_once("ifhtml_class_inc.php");

class frames
{
   //var $rows = array();
	//var $cols = array();
 	//var $url = array(); 


    /**
     * A method to display the chosen url
     * 
     * @return the frame of the url
     * @access public 
     */
	function show()
    {
		return $this->Frame($url);
    }

	 // the framesetCols tag defines a new set of frames sorted by column


    /**
     * A method to set the frame columns
     * 
     * @param  array  $cols 
     * @return string Return frameset columns
     * @access public
     */
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


    /**
     * A method to set the frame columns
     * 
     * @param  array  $rows
     * @return string Return frameset rows
     * @access public
     */
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



    /**
     * A method to create the frame tag to define the page to load
     * 
     * 
     * @param  unknown $url
     * @param  unknown $name
     * @param  unknown $noresize
     * @param  unknown $scrolling
     * @param  unknown $frameborder
     * @param  unknown $longdesc
     * @param  unknown $marginheight
     * @param  unknown $marginwidth
     * @return string  Return frame tag
     * @access public 
     */
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
                                                                                                                             
    $frame = "<FRAME SRC =\"$url\"" . $options . " />\n" ;
    return $frame;
	 }


    /**
     * A method to create the tag to close the frameset
     * 
     * @return string Return tag
     * @access public
     */
    function FrameSetEnd()
    {
     $ret = "</FRAMESET>\n" ;
		return $ret;
    }

    /**
     * A method to build the frame
     * 
     * @param  unknown $cols
     * @param  unknown $rows
     * @param  unknown $url
     * @return string  Return frame
     * @access private
     */
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