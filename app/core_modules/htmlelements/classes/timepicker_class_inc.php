<?php
/**
 * Time Picker - A Select Drop down to pick a time
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
 * @author Tohir Solomons <tsolomons@uwc.ac.za>
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

/**
* Time Picker - A Select Drop down to pick a time
*
* This class generates a drop down to enable users to select a time.
* Currently users an interval of 15 minutes between items
*
* @package   Time Picker
* @category  HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @version   $Id$;
* @author    Tohir Solomons
*/
class timepicker extends object
{

    /**
    * @var string $name Name of the form element
    */
    public $name = 'time';
    /**
    * @var string $value Value of the time picker
    */
    public $value;
    
    /**
    * Method to establish the default values
    */
    public function init()
    {
        $this->loadClass('dropdown');
    }
    
    /**
     * Method to set the Current Time as the Default
     */
    public function setSelectedNow()
    {
        $minute = date('i')-(date('i') % 15);
        
        if ($minute == 0) {
            $minute = '00';
        } else if (strlen($minute) == 1) {
            $minute = '0'.$minute;
        }
        
        $this->setSelected(date('G').':'.$minute);
    }
    
    /**
     * Method to set a default time:
     * @param string $time Default Time
     */
    public function setSelected($time)
    {
        $time = explode(':', $time);
        $hour = $time[0]*1;
        
        if (isset($time[1])) {
            $minutes = $time[1]-($time[1] % 15);
            
            if ($minutes === 0) {
                $minutes = '00';
            }
        } else {
            $minues = '00';
        }
        
        $this->value = $hour.':'.$minutes;
    }

    /**
    * Method to show the time picker
    * @return string The time picker
    */
    public function show()
    {
        $dropdown = new dropdown ($this->name);
        $dropdown->cssClass = 'timepicker';
        
        for ($hour=0; $hour<24; $hour++)
        {
            $time =  $hour.':00';
            $dropdown->addOption($time, $time);
            
            for ($minute=1; $minute<4;  $minute++)
            {
                $time =  $hour.':'.($minute*15);
                $dropdown->addOption($time, $time);
            }
        }
        
        $dropdown->setSelected($this->value);
        
        return $dropdown->show();
    }
}

?>