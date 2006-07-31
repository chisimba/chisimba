<?
/* -------------------- marquee class extends object ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

/**
* Marquee class acts as base class
* for the scrolling objects
* extends controller class since it requires the getObject method
*
* @version marquee_class_inc.php,v 1.0 2005/01/27 15:30hrs, Elijah Omwenga and Otim Samuel
* @copyright 2005
**/


class marquee extends object implements ifhtml
{
	/**
	* variable declaration
	* these are the attributes to be used in the marquee class
	*/
	
	public $behavior;
	public $align;
	public $direction;
	public $height;
	public $scrollAmount;
	public $scrollDelay;
	public $onMouseOver;
	public $onMouseOut;
	
	// array variable to hold the elements of the marquee
	public $elements;
	
	//dbMarquee object class to use when adding content to the marquee
	public $objDbMarquee;
	
	//user/administrator authentication
	public $isAdmin;
	
	//link management
	public $objHref;
	
	//icon object
	public $objGetIcon;
	
	//language object
	public $objLanguage;
	
	//marquee elements
	public $elements;
	
	//number of marquee elements
	public $numElements;
	
	//this class also requires the name of your module
	public $moduleName;
	
	/**
	* constructor to set default values
	*/
	
	public function init()
	{
		/**
		* establish the necessary routines for identifying administrators
		*/
        $this->objUser =& $this->getObject('user', 'security');
        $this->isAdmin=$this->objUser->isAdmin();
		$this->objHref=& $this->getObject('href','htmlelements');
		$this->objGetIcon =& $this->getObject('geticon', 'htmlelements');	
		$this->objLanguage = & $this->getObject('language', 'language'); 
		/**
		* defaulted to only 1
		* for the firefox browser esp, this shall ensure
		* no scrolling
		*/
		$this->numElements=1;
	}	
	
	/**
	* SET METHODS
	*/

	public function setNumElements($numElements)
	{
		$this->numElements=$numElements;
	}
	
	public function setModuleName($moduleName)
	{
		$this->moduleName=$moduleName;
	}
	
	public function setElements($elements)
	{
		$this->elements=$elements;
	}
	
	public function setBehavior($behavior)
	{
		$this->behavior=$behavior;
	}

	public function setAlign($align)
	{
		$this->align=$align;
	}

	public function setDirection($direction)
	{
		$this->direction=$direction;
	}
	
	public function setHeight($height)
	{
		$this->height=$height;
	}
	
	public function setScrollAmount($scrollAmount)
	{
		$this->scrollAmount=$scrollAmount;
	}
	
	public function setScrollDelay($scrollDelay)
	{
		$this->scrollDelay=$scrollDelay;
	}
	
	public function setOnMouseOver($onMouseOver)
	{
		$this->onMouseOver=$onMouseOver;
	}

	public function setOnMouseOut($onMouseOut)
	{
		$this->onMouseOut=$onMouseOut;
	}

	/**
	* GET METHODS
	*/

	public function getNumElements()
	{
		return $this->numElements;
	}
	
	public function getModuleName()
	{
		return $this->moduleName;
	}
	
	public function getElements()
	{
		return $this->elements;
	}
	
	public function getBehavior()
	{
		return $this->behavior;
	}

	public function getAlign()
	{
		return $this->align;
	}

	public function getDirection()
	{
		return $this->direction;
	}

	public function getHeight()
	{
		return $this->height;
	}

	public function getScrollAMount()
	{
		return $this->scrollAmount;
	}

	public function getScrollDelay()
	{
		return $this->scrollDelay;
	}

	public function getOnMouseOver()
	{
		return $this->onMouseOver;
	}

	public function getOnMouseOut()
	{
		return $this->onMouseOut;
	}

	/**
	* show function to display the marquee elements
	*/

	public function show()
	{
		if($this->getNumElements()>1) {
			$str = "<marquee 
				behavior='".$this->behavior."' 
				align='".$this->align."' 
				direction='".$this->direction."' 
				height='".$this->height."' 
				scrollAmount='".$this->scrollAmount."' 
				scrollDelay='".$this->scrollDelay."' 
				onMouseOver='".$this->onMouseOver."' 
				onMouseOut='".$this->onMouseOut."'
				>";
		
			//get the content to be displayec within the marquee
			$str.=$this->elements;
			$str.="</marquee><br />";
		} else {
			$str="";
			$str.=$this->elements;
			$str.="<br />";
		}
	
		/**
		* in accordance with framework standards, the add
		* link should only be displayed within the corresponding
		* administrative module
		*
        if ($this->isAdmin) {
            $paramArray = array('action' => 'add');
            $str .= $this->objGetIcon->getAddIcon($this->uri($paramArray, $this->moduleName));
        }
		*/
		return $str;
	}
}
?>
