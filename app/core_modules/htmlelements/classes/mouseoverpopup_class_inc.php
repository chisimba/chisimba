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
 * @author    Wesley Nitsckie
 *            
 * @version   $Id$
 * @copyright 2003
 * @package   mouseoverpopup
 * @category  HTML Controls
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license   GNU GPL
 * @example  
 *            $mop = new mouseoverpopup('url with caption','Content with caption , this tooltip uses a nice fade in ','asdfdsaf');
 *            $mop->caption='this is a caption';
 *            $mop->show();
 *            */
 class mouseoverpopup extends abhtmlbase implements ifhtml
 {
 	
    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $urltext;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $caption;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $url;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $content;
	
    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $iframeUrl='';

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $iframeCaption;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $iframeWidth;

    /**
     * Description for public
     * @var    string
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
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
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