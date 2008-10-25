<?php
/**
 * datepicker_class_inc.php
 *
 * This file contains the datepicker class which is used to generate
 * a datepicker to allow users to easily select dates in HTML forms
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
 */

/**
 * Date Picker
 *
 * This class generates a date picker, and is based on the script found at:
 * http://www.dynamicdrive.com/dynamicindex7/jasoncalendar.htm
 *
 * The date picker generates a hidden form input which contains the actual value.
 * The drop downs are merely a visual input to the hidden form element.
 *
 * It is therefore crucial that the name you give this object matches the one you want in your form.
 * It does NOT generate a time input
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @link      http://www.dynamicdrive.com/dynamicindex7/jasoncalendar.htm
 */
class datepicker extends object
{

    /**
    * Constructor
    */
    function init()
    {
        $this->dateFormat = 'YYYY-MM-DD';
        $this->name = 'calendardate';
        $this->defaultDate = date('Y-m-d');
    }

    /**
    * Method to set the name of the hidden form input
    *
    * @param string $name Name of Hidden Input
    */
    function setName($name)
    {
        $this->name = $name;
    }

    /**
    * Method to set the default date
    *
    * @param string $date Default Date
    */
    function setDefaultDate($date)
    {
        $this->defaultDate = $date;
    }

    /**
    * Method to set the format of the date
    *
    * Possible Options are:
    * YYYYMMDD
    * YYYY-MM-DD
    * YYYY-DD-MM
    * YYYY/MM/DD
    * YYYY/DD/MM
    * YYYY-DD-MON
    * YYYY-MON-DD
    * MM-DD-YYYY
    * DD-MM-YYYY
    * MM/DD/YYYY
    * DD/MM/YYYY
    * DD-MON-YYYY
    * MON-DD-YYYY
    *
    * It will only change the date format if it appears in the specified format.
    *
    * @param string $format Format of the Date
    */
    function setDateFormat($format)
    {
        $possibleOptions = array ('YYYYMMDD', 'YYYY-MM-DD', 'YYYY-DD-MM', 'YYYY/MM/DD', 'YYYY/DD/MM', 'YYYY-DD-MON', 'YYYY-MON-DD', 'MM-DD-YYYY', 'DD-MM-YYYY', 'MM/DD/YYYY', 'DD/MM/YYYY', 'DD-MON-YYYY', 'MON-DD-YYYY');

        if (in_array($format, $possibleOptions)) {
            $this->dateFormat = $format;
        }
    }

    /**
    * Method to display the date picker
    * It automatically adds the JavaScript to the header
    *
    * @return string The rendered HTML code of the datepicker
    */
    function show()
    {
        $script = '<script type="text/javascript" src="core_modules/htmlelements/resources/datepicker/calendarDateInput.js">

/***********************************************
* Jason\'s Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
* Script featured on and available at http://www.dynamicdrive.com
* Keep this notice intact for use.
***********************************************/

</script>';

        $this->appendArrayVar('headerParams', $script);

		$this->setVar('pageSuppressXML', TRUE);

        return "<script type=\"text/javascript\">
//<![CDATA[
DateInput('".$this->name."', true, '".$this->dateFormat."', '".$this->defaultDate."')
//]]>
</script>";
    }

}
?>