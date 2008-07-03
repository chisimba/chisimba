<?php
/**
 * Class textarea extends abhtmlbase implements ifhtml
 *
 * Textarea class to use to make textarea inputs
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
 * @author Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */
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
* textarea class to use to make textarea inputs.
*
* @package   htmlTextarea
* @category  HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @version   $Id:
* @author    Wesley Nitsckie
* @author    Megan Watson
* @author    Tohir Solomons
* @example
* @todo      -c HTML Editor that will extend this object
*/
 class textarea extends abhtmlbase implements ifhtml
 {
 	/**
    *
    * @var string $cols: The number of columns the textare will have
    */
	public $cols;
	/**
    *
    * @var string $rows: The number of rows the textare will have
    */
	public $rows;
    /**
    *
    * @var string $autoGrow Whether or not to autogrow the textarea
    *  using jQuery
    */
    private $autoGrow=FALSE;


	/**
    * Method to establish the default values
    */
	public function textarea($name=null,$value='',$rows=4,$cols=50)
 	{
		$this->name=$name;
		$this->value=$value;
		$this->rows=$rows;
		$this->cols=$cols;
		$this->css='textarea';
		$this->cssId = 'input_'.$name;
	}

	/**
    * function to set the value of one of the properties of this class
    *
    * @var string $name: The name of the textare
    */
	public function setName($name)
	{
		$this->name=$name;
	}

    /**
    *
    * Method to set the css class of the textarea
    * @param string $cssClass the CSS class for the text area
    * @return VOID
    *
    */
    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
    }

	/*
	* Method to set the cssId class
	* @param string $cssId
	*/

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $cssId Parameter description (if any) ...
     * @return void
     * @access public
     */
    public function setId($cssId)
    {
        $this->cssId = $cssId;
    }

	/**
    * function to set the amount of rows
    * @var string $Rows: The number of rows of the textare
    *
    */
	public function setRows($rows)
	{
		$this->rows=$rows;
	}
	/**
    * function to set the amount of cols
    * @var string $cols: The number of cols of the textare
    *
    */
	public function setColumns($cols)
	{
		$this->cols=$cols;
	}

	/**
    * function to set the content
    * @var string $content: The content of the textare
    */
	public function setContent($value)
	{
		$this->value=$value;
	}

    /**
    * Method to set the autogrow function
    */
    public function setAutoGrow($value=FALSE)
    {
        $this->autoGrow=$value;
    }

 	/**
    * Method to show the textarea
    * @return string The formatted link
    */
	public function show()
	{
		$str = '<textarea name="'.$this->name.'"';

		if($this->cssClass){
			$str.=' class="'.$this->cssClass.'"';
		}
		if ($this->cssId) {
            $str .= ' id="' . $this->cssId . '"';
        }

        if($this->rows){
            $str.=' rows="'.$this->rows.'"';
        }
        if($this->cols){
            $str.=' cols="'.$this->cols.'"';
        }

		if ($this->extra) {
            $str .= ' '.$this->extra;
        }
		$str.='>';
		$str.=$this->value;
		$str.='</textarea>';
		return $str;
	}
 }

?>
