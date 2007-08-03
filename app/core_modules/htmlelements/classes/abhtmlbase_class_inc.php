<?php

/**
 * 
 * Abstract HTML Base Class forms the base for 
 * most html objects. It hold most of the 
 * common methods and properties needed for 
 * implementing an html object as a derived class.
 *
 * @version   $Id$
 * @package   htmlbase
 * @category  HTML Controls
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license   GNU GPL
 * @author    Wesley Nitsckie
 * @author    Derek Keats converted to PHP5 abstract class
 *            
 *            */
abstract class abhtmlbase extends object
{

    /**
    * @var string $cssId: A unique id for the element, often corresponding to a 
    *             #id tag in a stylesheet. In KEWL.NextGen it is the CSS ID used from 
    *             the skin stylesheet
    *             
    *             Not valid in base, head, html, meta, param, script, style, and title elements.
    *             
    */
    public $cssId;
    /**
    * 
    * @var string $cssClass: The class of the element, in KEWL.NextGen it is the CSS 
    *             Class from the skin stylesheet
    *             
    *             Not valid in base, head, html, meta, param, script, style, and title elements.
    *             
    */
    public $cssClass;
    /**
    * 
    * @var string $title: The title of the anchor. 
    *             Note: Not allowed if DTD is strict, only in transitional and frameset DTDs 
    *             Optional
    */
    public $title;
    /**
    * @var string $style: An inline style definition 
    */
    public $style;
    /**
    * @var string $dir: Sets the text direction ltr | rtl
    */
    public $dir;
    /**
    * @var string $lang: Sets the language code 
    */
    public $lang;
    /**
    * @var string $tabindex: Sets the tab order of an element
    */
    public $tabindex;
    /**
    * @var string $accesskey: Sets a keyboard shortcut to access an element
    */
    public $accesskey;
    /**
    * 
    * @var string $name The name of the element
    */
    public $name;
    /**
    * 
    * @var string $extra: anything extra that you want to add, such
    *             as an additional style
    */
    public $extra;  
	
	/**
	* Constructor
	*/
	public function __construct($name)
	{
		//set the name of the element
		$this->name=$name;
	}
	
  /**
  *  Function to set the value of an element
  *	 @param $value the new value of the element
  */
  public function setValue($value)
  {
  	$this->value=$value;
  }
}

?>