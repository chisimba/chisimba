<?php

/**
 * This file contains the calendar class used to render an html calendar
 * for date selection within forms
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
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */



// Include the HTML interface class
require_once("ifhtml_class_inc.php");

/**
 * The calendar class is used to render a text input
 * field for date selections, along with a button
 * which when clicked provides a popup graphical
 * calendar for more user friendly date selection
 * within HTML forms.
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $id:$
 * @link      http://avoir.uwc.ac.za
 */
class calendar implements ifhtml {

    /**
     * The name of the text input
     *
     * @var    string $name
     * @access public
     */
	public $name;

    /**
     * variable used to store the css
     * class of the element
     *
     * @var    string $css
     * @access public
     */
	public $css;

    /**
     * The initial value of the text
     * field
     *
     * @var    string $value
     * @access public
     */
	public $value;

    /**
     * The name of the calendar window
     * which will pop up
     *
     * @var    string $windowName
     * @access public
     */
	public $windowName;

    /**
     * Initialization method to set default values
	 * @param string $name (optional) sets the name of the text input
	 * @param string $value (optional) sets the default value of the input
     */
	public function caledar($name=null,$value=null){
		$this->name=$name;
		$this->value=$value;
		$this->css='textdisabled';
	}

	/**
	* Method to set the css class
	*
	* @param string $css The desired css class
	*/
	public function setCss($css)
	{
		$this->css=$css;
	}

	/*function to set the date for calendar
	* @param int $mth :the month
	* @param int $day :the day
	* @param int $year :the year
	*/

    /**
     * Function to set the date within the calendar popup
     *
     * @param  string $mth  The month
     * @param  string $day  The day
     * @param  string $year The year
     * @return void
     * @access public
     */
	public function setDate($mth,$day,$year)
	{
		if(checkdate($mth,$day,$year))
		{
			$this->value=$mth.'/'.$day.'/'.$year;
		}
	}

	/**
	* Method to render the textinput and popup window
	*
	* @return string The html of the textinput with onclick calendar popup
	*/
	public function show(){
		$this->windowName = "win";
		$str='<input type="text" value="'.$this->value.'" id="caltext"';
		$str.=' name="'.$this->name.'"';
		$str.=' class="'.$this->css.'"';
		$str.=' />';
		$str.="<a href=\"#\" onclick=\"window.open('core_modules/htmlelements/classes/cal.php','win','width=350,height=200'); return false\"><img src=\"core_modules/htmlelements/resources/images/schedule_ico.gif\" /></a>";
		return $str;
	}

 }

?>