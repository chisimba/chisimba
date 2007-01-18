<?php


// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for building a feature box for KEWL.nextgen.
*
* The class builds a css style feature box
*
* @author Wesley Nitsckie
* @copyright (c)2004 UWC
* @package featurebox
* @version 0.1
*/

class featurebox extends object
{
    /**
    * Method to construct the class.
    **/
    public function init()
    {

    }

    /**
     * Method to show the sidebar
     *
     * @param null
     * @access publc
     * @return string
     */
    public function show($title = null, $content = null)
    {

  		$sidebar = '<div class="featurebox">';
		$sidebar .= '	<h5>'.$title.'</h5>';
		$sidebar .= '<small>'.$content.'</small>';


		$sidebar .= '</div>';
  		return $sidebar;

    }

    /**
     * Method to show a content featurebox
     *
     * @param string $title
     * @param string $content
     * @return string
     */
    public function showContent($title = null, $content = null)
    {

  		$contentbox = '<div class="contentfeaturebox">';
		$contentbox .= '	<h5>'.$title.'</h5>';
		$contentbox .= '<small>'.$content.'</small>';
		$contentbox .= '</div>';

  		return $contentbox;

    }
}
?>