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
    *
    * @var string $id: the ID tag from the CSS
    * @access public
    */
    public $id;

    /**
    * Method to construct the class.
    **/
    public function init()
    {
        $this->id = '';
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

  		$sidebar = '<div class="featurebox"';
  		if($this->id != NULL){
            $sidebar .= ' id="'.$this->id.'">';
        }else{
            $sidebar .= '>';
        }
		$sidebar .= '	<h5 class="featureboxheader">'.$title.'</h5>';
		$sidebar .= '<div class="featureboxcontent"><small>'.$content.'</small></div>';


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
		$contentbox .= '	<h3>'.$title.'</h3>';
		$contentbox .= '<small>'.$content.'</small>';
		$contentbox .= '</div>';

  		return $contentbox;

    }
    
    /**
     * Method to show a comment featurebox
     *
     * @param string $title
     * @param string $content
     * @return string
     */
    public function showComment($title = null, $content = null)
    {

  		$contentbox = '<div class="contentfeaturebox">';
		$contentbox .= '	<h3>'.$title.'</h3>';
		$contentbox .= $content;
		$contentbox .= '</div>';

  		return $contentbox;

    }
}
?>