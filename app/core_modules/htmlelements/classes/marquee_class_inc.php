<?php
/* -------------------- marquee class extends object ----------------*/
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
* Marquee class acts as base class
* for the scrolling objects
* extends controller class since it requires the getObject method
*
* @version   marquee_class_inc.php,v 1.0 2005/01/27 15:30hrs, Elijah Omwenga and Otim Samuel
* @copyright 2005
*            */


class marquee extends object implements ifhtml
{
	/**
	* variable declaration
	* these are the attributes to be used in the marquee class
	*/
	
	public $behavior;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $align;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $direction;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $height;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $scrollAmount;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $scrollDelay;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $onMouseOver;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $onMouseOut;
	
	// array variable to hold the elements of the marquee


    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $elements;
	
	//dbMarquee object class to use when adding content to the marquee


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $objDbMarquee;
	
	//user/administrator authentication


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $isAdmin;
	
	//link management


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $objHref;
	
	//icon object


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $objGetIcon;
	
	//language object


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $objLanguage;
	
	//marquee elements


    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $elements;
	
	//number of marquee elements


    /**
     * Description for public
     * @var    integer
     * @access public 
     */
	public $numElements;
	
	//this class also requires the name of your module


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $moduleName;
	
	/**
	* constructor to set default values
	*/
	
	public function init()
	{
		/**
		* establish the necessary routines for identifying administrators
		*/
        $this->objUser = $this->getObject('user', 'security');
        $this->isAdmin=$this->objUser->isAdmin();
		$this->objHref= $this->getObject('href','htmlelements');
		$this->objGetIcon = $this->getObject('geticon', 'htmlelements');	
		$this->objLanguage =  $this->getObject('language', 'language'); 
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
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $moduleName Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setModuleName($moduleName)
	{
		$this->moduleName=$moduleName;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $elements Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setElements($elements)
	{
		$this->elements=$elements;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $behavior Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setBehavior($behavior)
	{
		$this->behavior=$behavior;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $align Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setAlign($align)
	{
		$this->align=$align;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $direction Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setDirection($direction)
	{
		$this->direction=$direction;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $height Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setHeight($height)
	{
		$this->height=$height;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $scrollAmount Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setScrollAmount($scrollAmount)
	{
		$this->scrollAmount=$scrollAmount;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $scrollDelay Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setScrollDelay($scrollDelay)
	{
		$this->scrollDelay=$scrollDelay;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $onMouseOver Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setOnMouseOver($onMouseOver)
	{
		$this->onMouseOver=$onMouseOver;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $onMouseOut Parameter description (if any) ...
     * @return void   
     * @access public 
     */
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
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return unknown Return description (if any) ...
     * @access public 
     */
	public function getModuleName()
	{
		return $this->moduleName;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
	public function getElements()
	{
		return $this->elements;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
	public function getBehavior()
	{
		return $this->behavior;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
	public function getAlign()
	{
		return $this->align;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
	public function getDirection()
	{
		return $this->direction;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
	public function getHeight()
	{
		return $this->height;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
	public function getScrollAMount()
	{
		return $this->scrollAmount;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
	public function getScrollDelay()
	{
		return $this->scrollDelay;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
	public function getOnMouseOver()
	{
		return $this->onMouseOver;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
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