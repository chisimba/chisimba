<?php
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

// Include the HTML base class

/**
 * Description for require_once
 */
require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
 * Mouse Over Popup Class
 * Used to create links with a Tooltip 
 * This object mis need to display the glossary items
 * 
 * @category  Chisimba
 * @author    Wesley Nitsckie
 * @package   htmlelements 
 * @version   $Id$
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @example  
 *            $mop = new mouseoverpopup('url with caption','Content with caption , this tooltip uses a nice fade in ','asdfdsaf');
 *            $mop->caption='this is a caption';
 *            $mop->show();
 *
 * Release: @package_version@
 */

 class mouseoverpopup extends abhtmlbase implements ifhtml
 {
 	
    /**
     * Holds the text for the url
     * @var    string $urltext
     * @access public
     */
	public $urltext;

    /**
     * Holds the caption for the tooltip
     * @var    string $caption
     * @access public
     */
	public $caption;

    /**
     * Holds the url
     * @var    string $url
     * @access public
     */
	public $url;

    /**
     * Holds the content for the tooltip
     * @var    string $content
     * @access public
     */
	public $content;
	
    /**
     * Holds the iframe url
     * @var    string $iframeUrl
     * @access public
     */
	public $iframeUrl='';

    /**
     * Holds the iframe caption
     * @var    string $iframeCaption
     * @access public
     */
	public $iframeCaption;

    /**
     * Holds the iframe width
     * @var    string $iframeWidth
     * @access public
     */
	public $iframeWidth;

    /**
     * Holds the iframe height
     * @var    string $iframeHeight
     * @access public
     */
	public $iframeHeight;
	
	/**
	* Initializer Method
	* @param string $urltext : the text of the link
	* @param string $content : The text that will be displayed in the toolitp
	* @param string $caption : The tooltip Caption
	* @param string $url : the url of the link
	*/
	public function mouseoverpopup($urltext=null,$content=null,$caption=null,$url=null){
		$this->urltext=$urltext;
		$this->caption=$caption;
		$this->content=$content;
		$this->url=$url;
	}
	
    /**
     * This method is used to display a tooltip
     * 
     * @return string Return string for tooltip
     * @access public
     */
	public function show()
	{
		$str='<script language="javascript" src="core_modules/htmlelements/resources/domLib.js"></script>
    		  <script language="javascript" src="core_modules/htmlelements/resources/alphaAPI.js"></script>
    	      <script language="javascript" src="core_modules/htmlelements/resources/domTT.js"></script>
    	      <script language="javascript" src="core_modules/htmlelements/resources/domTT_drag.js"></script>';
		$str.="<div class='mouseoverpopup'><a href=\"";
		if ($this->url) {
		    $str.=$this->url;
		}else{
			$str.="javascript:void(0);";
		}
		$str.="\"" ;
		$str.=" onmouseover=\"domTT_activate(this, event,";
		if($this->caption){
			$str.= "'caption', '".$this->caption."',";
		}
		$str.= " 'content', '".$this->content ."', 'trail', true, 'fade', 'in');\"";
		$str.= " onmouseout=\"domTT_mouseout(this, event);\"";
		
		if ($this->iframeCaption && $this->iframeUrl && $this->iframeWidth && $this->iframeHeight) {
			$str.= " onclick=\"return makeFalse(domTT_activate(this, event, 'caption', '".$this->iframeCaption."',";
			$str.= "'content', '&lt;iframe src=&quot;".$this->iframeUrl."&quot; ";
			$str.= "style=&quot;width: ".$this->iframeWidth."px; height:".$this->iframeHeight."px;";
			$str.= "&quot;&gt;&lt;/iframe&gt;', 'type', 'sticky', 'closeLink'));\"";
		}			
		
		$str.= ">";
		$str.=$this->urltext;
		$str.="</a></div>";
		return $str;
	}
 
 }
?>