<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 3
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
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

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class frames
{
   //var $rows = array();
	//var $cols = array();
 	//var $url = array(); 


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return unknown Return description (if any) ...
     * @access public 
     */
	function show()
    {
		return $this->Frame($url);
    }

	 // the framesetCols tag defines a new set of frames sorted by column


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  array  $cols Parameter description (if any) ...
     * @return string Return description (if any) ...
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
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  array  $rows Parameter description (if any) ...
     * @return string Return description (if any) ...
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

    
	// the frame tag to define the page to load


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $url          Parameter description (if any) ...
     * @param  unknown $name         Parameter description (if any) ...
     * @param  unknown $noresize     Parameter description (if any) ...
     * @param  unknown $scrolling    Parameter description (if any) ...
     * @param  unknown $frameborder  Parameter description (if any) ...
     * @param  unknown $longdesc     Parameter description (if any) ...
     * @param  unknown $marginheight Parameter description (if any) ...
     * @param  unknown $marginwidth  Parameter description (if any) ...
     * @return string  Return description (if any) ...
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

    // the tag to close the frameset


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
    function FrameSetEnd()
    {
     $ret = "</FRAMESET>\n" ;
		return $ret;
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $cols Parameter description (if any) ...
     * @param  unknown $rows Parameter description (if any) ...
     * @param  unknown $url  Parameter description (if any) ...
     * @return string  Return description (if any) ...
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