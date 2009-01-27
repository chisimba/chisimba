<?php
/**
 * dropdown_class_inc.php
 *
 * This file contains the dropdown class which is used to generate
 * HTML select boxes for use in HTML forms
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
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */



// Include the HTML base class
require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class
require_once("ifhtml_class_inc.php");


/**
 * Dropdown class for outputting dropdown menu.
 *
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @example
 *  $dd=&new dropdown('mydropdown');
 *  $dd->addOption()    will add a blank option
 *  $dd->addOption('1','Male')
 *  $dd->addOption('2','Female')
 *  $dd->show();
 *  OR use from a result array
 *  $objElement = new dropdown('user_dropdown');
 *  $objElement->addFromDB($this->objDBUser->getAll(),'username','userId',$this->objDBUser->userName());
 *  $objElement->label='User list';
 *  $objElement->show();
 */
class dropdown extends abhtmlbase implements ifhtml
{

  /**
  *
  * @var array $options: holds the options for the combo box
  */
  public $options=array();

  /**
  *
  * @var array $extras: holds the extra attributes for the combo box
  */
  public $extras=array();

  /**
  *
  * @var boolean $multiple: When set allows multiple entries to be selected
  */
  public $multiple = FALSE;

   /**
  *
  * @var array $multipleselected: When set allows multiple entries marked as selected
  */
  public $multipleselected = array();

  /**
  *
  * @var string $size: Defines the number of visible items in the dropdown list
  */
  public $size = 1;

  /**
  *
  * @var string $selected: The value that selected
  */
  public $selected;

  /**
  *
  * @var string $cssClass: CSS Class for the drop down
  */
  public $cssClass = 'WCHhider';

 /**
  * 
  *@ var string $onchangeScrip: holds the script
  * 
  */
  public $onchangeScript="";
  /**
  * Class Constructor
  *
  * @param string $name : The name of the dropdown
  */
  public function dropdown($name=NULL){
    if (!is_object($name)) {
          $this->name=$name;
        $this->cssId = 'input_'.$name;
    }
  }

  /**
  * Method that adds a options to
  * the radio group
  *
  * @param string $label : The label that goes with the option
  * @param string $value : The value for a give option
  * @access public
  * @return void
  */
  public function addOption($value=null,$label=null,$extra='')
  {
    if ($label==null) {
        $label = $value;
    }
      $this->options[$value] = $label;
    $this->extras[$value] = $extra;
  }

  /**
  * Method to set the selected value
  *
  * @param $value string : The value that you want selected
  * @access public
  * @return void
  */
  public function setSelected($value)
  {
    if(isset($this->options[$value]))
    {
        $this->selected=$value;
    }
  }

  /**
  * Method to set multiple selected values
  *
  * @param $valueArr Array : An Array of values that you want selected
  */
  public function setMultiSelected($valueArr)
  {
        $this->multipleselected=$valueArr;
  }


   /**
    * Method to set the css Id
    * @param string $cssId
    */
  public function setId($cssId)
    {
        $this->cssId = $cssId;
    }

  /**
  * Method to show the dropdown
  *
  * @return string : The dropdown html
  */
  public function show()
  {
    /*
    ob_start();
    echo '<pre>';
    print_r($this->extras);
    echo '</pre>';
      $str = ob_get_contents();
    ob_end_clean();
    return $str;
    */
    //
      if($this->multiple){
        $this->name = $this->name."[]";
    }
    $str = '<select name="'.$this->name.'"';
    if($this->cssClass){
        $str.=' class="'.$this->cssClass.'" ';
    }
    if ($this->cssId) {
            $str .= ' id="' . $this->cssId . '" ';
    }
    if($this->multiple){
        $str .= ' multiple="multiple"';
    }
    if($this->size > 1){
        $str .= ' size="'.$this->size.'"';
    }
    if ($this->extra) {
        $str .= ' '.$this->extra;
    }
    $str.="  ".$this->onchangeScript." >"."\n";
    foreach ($this->options as $opt => $lbl)
    {
        $str.='<option value="'.$opt.'"';

        if($this->multipleselected)
        {
            foreach($this->multipleselected as $mselect)
            {
                if($mselect==$opt)
                {
                    $str.=' selected="selected"';
                }
            }
        }
        else
        {
            if($this->selected==$opt){
                $str.=' selected="selected"';
            }
        }
        if ($this->extras[$opt] != '') {
            $str .= ' '.$this->extras[$opt];
        }
        $str.='>';
        $str.=$lbl;
        $str.='</option>'."\n";
    }
    $str .= '</select>'."\n";
    return $str;
  }

  /**
  * Method used to populate
  * the dropdown with a array
  * @param $resultset     array  : the result set
  * @param $labelField    string : the value that will be displayed in the dropdown
  * @param $valueField    string : the value the will go in the 'value' of the dropdown
  * @param @selectedValue string : the value that you want to have selected
  */
  public function addFromDB($array, $labelField=null, $valueField=null, $selectedValue=null)
  {
      if ($array)
    {
        //loop through the array
        foreach($array as $line)
        {
            //add an option
            $this->addOption($line[$valueField],$line[$labelField]);
        }
        if (!is_null($selectedValue)) {
               $this->setSelected($selectedValue);
        }
    }
  }
  /*
   *Function to add the onchange event to dropdown list box
   *By Emmanuel Natalis
   *  var $script - script to be called on the onchange effect
   */
function addOnchange($scrpt)
{
   $this->onchangeScript=" onchange=\"".$scrpt."\"";
}
}
?>
