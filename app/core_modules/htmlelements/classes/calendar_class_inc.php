<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

/**
 * Text Input class controls buttons 
 * @author    Wesley Nitsckie
 * @version   $Id$
 * @copyright 2003
 *            */
 
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class calendar implements ifhtml {

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $name;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $css;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $value;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $windowName;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $location;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $width;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $height;
    
    /**
     * Initialization method to set default values
	 * @param string $name optional :sets the name of the text input
     */
	public function caledar($name=null,$value=null){
		$this->name=$name;
		$this->value=$value;
		$this->css='textdisabled';
	}
	
	/**
	* Method to set the css class
	* @param string $css
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
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  string $mth  Parameter description (if any) ...
     * @param  string $day  Parameter description (if any) ...
     * @param  string $year Parameter description (if any) ...
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
	* Method to show the button
	*/
	public function show(){
		$this->windowName = "win";
		$str='<input type="text" value="'.$this->value.'" id="caltext"';
		$str.=' name="'.$this->name.'"';
		$str.=' class="'.$this->css.'"';
		$str.=' />';
		//$str.="<a href=\"#\" onclick=\"window.open('modules/htmlelements/classes/cal.php','win','width=350,height=200'); return false\"><img src=\"modules/htmlelements/resources/images/schedule_ico.gif\"></a>";
		$str.="<a href=\"#\" onclick=\"window.open('core_modules/htmlelements/classes/cal.php','win','width=350,height=200'); return false\"><img src=\"core_modules/htmlelements/resources/images/schedule_ico.gif\" /></a>";
		return $str;
	}

 }

?>