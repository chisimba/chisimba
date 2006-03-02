<?
/* -------------------- marquee class extends object ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* Marquee class acts as base class
* for the scrolling objects
* extends controller class since it requires the getObject method
*
* @version marquee_class_inc.php,v 1.0 2005/01/27 15:30hrs, Elijah Omwenga and Otim Samuel
* @copyright 2005
**/


class marquee extends object 
{
	/**
	* variable declaration
	* these are the attributes to be used in the marquee class
	*/
	
	var $behavior;
	var $align;
	var $direction;
	var $height;
	var $scrollAmount;
	var $scrollDelay;
	var $onMouseOver;
	var $onMouseOut;
	
	// array variable to hold the elements of the marquee
	var $elements;
	
	//dbMarquee object class to use when adding content to the marquee
	var $objDbMarquee;
	
	//user/administrator authentication
	var $isAdmin;
	
	//link management
	var $objHref;
	
	//icon object
	var $objGetIcon;
	
	//language object
	var $objLanguage;
	
	//marquee elements
	var $elements;
	
	//number of marquee elements
	var $numElements;
	
	//this class also requires the name of your module
	var $moduleName;
	
	/**
	* constructor to set default values
	*/
	
	function init()
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

	function setNumElements($numElements)
	{
		$this->numElements=$numElements;
	}
	
	function setModuleName($moduleName)
	{
		$this->moduleName=$moduleName;
	}
	
	function setElements($elements)
	{
		$this->elements=$elements;
	}
	
	function setBehavior($behavior)
	{
		$this->behavior=$behavior;
	}

	function setAlign($align)
	{
		$this->align=$align;
	}

	function setDirection($direction)
	{
		$this->direction=$direction;
	}
	
	function setHeight($height)
	{
		$this->height=$height;
	}
	
	function setScrollAmount($scrollAmount)
	{
		$this->scrollAmount=$scrollAmount;
	}
	
	function setScrollDelay($scrollDelay)
	{
		$this->scrollDelay=$scrollDelay;
	}
	
	function setOnMouseOver($onMouseOver)
	{
		$this->onMouseOver=$onMouseOver;
	}

	function setOnMouseOut($onMouseOut)
	{
		$this->onMouseOut=$onMouseOut;
	}

	/**
	* GET METHODS
	*/

	function getNumElements()
	{
		return $this->numElements;
	}
	
	function getModuleName()
	{
		return $this->moduleName;
	}
	
	function getElements()
	{
		return $this->elements;
	}
	
	function getBehavior()
	{
		return $this->behavior;
	}

	function getAlign()
	{
		return $this->align;
	}

	function getDirection()
	{
		return $this->direction;
	}

	function getHeight()
	{
		return $this->height;
	}

	function getScrollAMount()
	{
		return $this->scrollAmount;
	}

	function getScrollDelay()
	{
		return $this->scrollDelay;
	}

	function getOnMouseOver()
	{
		return $this->onMouseOver;
	}

	function getOnMouseOut()
	{
		return $this->onMouseOut;
	}

	/**
	* show function to display the marquee elements
	*/

	function show()
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
