<?php
/**
 * This file contains the abhtmlbase class which is an
 * abstract class forming the base for most HTML elements
 *
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

/**
 *
 * Abstract HTML Base Class forms the base for
 * most html objects. It holds most of the
 * common methods and properties needed for
 * implementing an html object as a derived class.
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

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