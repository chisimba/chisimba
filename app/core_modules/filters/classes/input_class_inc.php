<?php

/**
 * Input class that makes use of the filter class to sanitize user inputs
 * 
 * Input class that makes use of the filter class to sanitize user inputs
 * provides simple facilities that promote a structured and rigid approach to input filtering.
 * Its purpose is multifaceted, because it caters to the needs of three different groups of people:
 *
 * Developers:
 * Although filtering input can never be as easy as doing nothing,
 * developers need to ensure the integrity of their data without adding unnecessary complexity to their code.
 * Filter Input offers simple methods for the most common use cases,
 * extensibility for edge cases, and a strict naming convention that promotes code clarity.
 *
 * Managers
 * Managers of all types who need to maintain control over a large group of developers can enforce a structured approach to input filtering
 * by restricting or eliminating access to raw input.
 *
 * Auditors
 * Those who audit an application's code need to quickly and reliably identify when and where raw input is used by a developer.
 * The characteristics that promote code clarity also aid auditors by providing a clear distinction among the different approaches to input filtering.
 * There are a variety of approaches to input filtering,
 * and there are also a variety of facilities that PHP developers can use.
 * Whitelist filtering, blacklist filtering, regular expressions, conditional statements, and native PHP functions
 * are just a few examples of the input filtering potpourri.
 * Filter Input combines all of these facilities into a single API with consistent behavior and strict naming conventions.
 * All of the methods abide by a simple rule - if the data is valid, it is returned, otherwise FALSE is returned.
 * Extreme simplicity.
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
 * @package   filters
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * Input class that makes use of the filter class to sanitize user inputs
 * provides simple facilities that promote a structured and rigid approach to input filtering.
 * Its purpose is multifaceted, because it caters to the needs of three different groups of people:
 *
 * Developers:
 * Although filtering input can never be as easy as doing nothing,
 * developers need to ensure the integrity of their data without adding unnecessary complexity to their code.
 * Filter Input offers simple methods for the most common use cases,
 * extensibility for edge cases, and a strict naming convention that promotes code clarity.
 *
 * Managers
 * Managers of all types who need to maintain control over a large group of developers can enforce a structured approach to input filtering
 * by restricting or eliminating access to raw input.
 *
 * Auditors
 * Those who audit an application's code need to quickly and reliably identify when and where raw input is used by a developer.
 * The characteristics that promote code clarity also aid auditors by providing a clear distinction among the different approaches to input filtering.
 * There are a variety of approaches to input filtering,
 * and there are also a variety of facilities that PHP developers can use.
 * Whitelist filtering, blacklist filtering, regular expressions, conditional statements, and native PHP functions
 * are just a few examples of the input filtering potpourri.
 * Filter Input combines all of these facilities into a single API with consistent behavior and strict naming conventions.
 * All of the methods abide by a simple rule - if the data is valid, it is returned, otherwise FALSE is returned.
 * Extreme simplicity.
 *
 * @access     public
 * @author     Paul Scott based on the Zend Framework methods
 * @copyright  AVOIR
 * @filesource
 */
class input //extends object
{
    /**
     * The input source
     *
     * @var mixed
     */
    protected $_source = NULL;

    /**
     * The filter object inherited from the filter class
     *
     * @var object
     */
    public $_filter;

    /**
     * Constructor
     *
     * @param mixed   $source
     * @param boolean $strict
     */
    public function __construct(&$source = NULL, $strict = TRUE)
    {
        $this->_filter = $this->getObject('filter');
    	$this->_source = $source;

        if ($strict) {
            $source = NULL;
        }
    }

    /**
     * Returns only the alphabetic characters in value.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function getAlpha($key)
    {
        return $this->_filter->getAlpha($this->_source[$key]);
    }

    /**
     * Returns only the alphabetic characters and digits in value.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function getAlnum($key)
    {
        return $this->_filter->getAlnum($this->_source[$key]);
    }


    /**
     * Returns only the digits in value. This differs from getInt().
     *
     * @param  mixed $key
     * @return mixed
     */
    public function getDigits($key)
    {
        return $this->_filter->getDigits($this->_source[$key]);
    }


    /**
     * Returns dirname(value).
     *
     * @param  mixed $key
     * @return mixed
     */
    public function getDir($key)
    {
        return $this->_filter->getDir($this->_source[$key]);
    }


    /**
     * Returns (int) value.
     *
     * @param  mixed $key
     * @return int  
     */
    public function getInt($key)
    {
        return $this->_filter->getInt($this->_source[$key]);
    }


    /**
     * Returns realpath(value).
     *
     * @param  mixed $key
     * @return mixed
     */
    public function getPath($key)
    {
        return $this->_filter->getPath($this->_source[$key]);
    }


    /**
     * Returns value.
     *
     * @param  string $key
     * @return mixed 
     */
    public function getRaw($key)
    {
        return $this->_source[$key];
    }


    /**
     * Returns value if every character is alphabetic or a digit,
     * FALSE otherwise.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testAlnum($key)
    {
        if ($this->_filter->isAlnum($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if every character is alphabetic, FALSE
     * otherwise.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testAlpha($key)
    {
        if ($this->_filter->isAlpha($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is greater than or equal to $min and less
     * than or equal to $max, FALSE otherwise. If $inc is set to
     * FALSE, then the value must be strictly greater than $min and
     * strictly less than $max.
     *
     * @param  mixed   $key      
     * @param  mixed   $min      
     * @param  mixed   $max      
     * @param  boolean $inclusive
     * @return mixed  
     */
    public function testBetween($key, $min, $max, $inc = TRUE)
    {
        if ($this->_filter->isBetween($this->_source[$key], $min, $max, $inc)) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is a valid credit card number format. The
     * optional second argument allows developers to indicate the
     * type.
     *
     * @param  mixed $key 
     * @param  mixed $type
     * @return mixed
     */
    public function testCcnum($key, $type = NULL)
    {
        if ($this->_filter->isCcnum($this->_source[$key], $type)) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns $value if it is a valid date, FALSE otherwise. The
     * date is required to be in ISO 8601 format.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testDate($key)
    {
        if ($this->_filter->isDate($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if every character is a digit, FALSE otherwise.
     * This is just like isInt(), except there is no upper limit.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testDigits($key)
    {
        if ($this->_filter->isDigits($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is a valid email format, FALSE otherwise.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testEmail($key)
    {
        if ($this->_filter->isEmail($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is a valid float value, FALSE otherwise.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testFloat($key)
    {
        if ($this->_filter->isFloat($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is greater than $min, FALSE otherwise.
     *
     * @param  mixed $key
     * @param  mixed $min
     * @return mixed
     */
    public function testGreaterThan($key, $min = NULL)
    {
        if ($this->_filter->isGreaterThan($this->_source[$key], $min)) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is a valid hexadecimal format, FALSE
     * otherwise.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testHex($key)
    {
        if ($this->_filter->isHex($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is a valid hostname, FALSE otherwise.
     * Depending upon the value of $allow, Internet domain names, IP
     * addresses, and/or local network names are considered valid.
     * The default is HOST_ALLOW_ALL, which considers all of the
     * above to be valid.
     *
     * @param  mixed   $key  
     * @param  integer $allow bitfield for HOST_ALLOW_DNS, HOST_ALLOW_IP, HOST_ALLOW_LOCAL
     * @return mixed  
     */
    public function testHostname($key, $allow="HOST_ALLOW_ALL")
    {
        if ($this->_filter->isHostname($this->_source[$key], $allow)) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is a valid integer value, FALSE otherwise.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testInt($key)
    {
        if ($this->_filter->isInt($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is a valid IP format, FALSE otherwise.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testIp($key)
    {
        if ($this->_filter->isIp($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is less than $max, FALSE otherwise.
     *
     * @param  mixed $key
     * @param  mixed $max
     * @return mixed
     */
    public function testLessThan($key, $max = NULL)
    {
        if ($this->_filter->isLessThan($this->_source[$key], $max)) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is a valid format for a person's name,
     * FALSE otherwise.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testName($key)
    {
        if ($this->_filter->isName($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is one of $allowed, FALSE otherwise.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testOneOf($key, $allowed = NULL)
    {
        if ($this->_filter->isOneOf($this->_source[$key], $allowed)) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is a valid phone number format, FALSE
     * otherwise. The optional second argument indicates the country.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testPhone($key, $country = 'US')
    {
        if ($this->_filter->isPhone($this->_source[$key], $country)) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it matches $pattern, FALSE otherwise. Uses
     * preg_match() for the matching.
     *
     * @param  mixed $key    
     * @param  mixed $pattern
     * @return mixed
     */
    public function testRegex($key, $pattern = NULL)
    {
        if ($this->_filter->isRegex($this->_source[$key], $pattern)) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $key Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public 
     */
    public function testUri($key)
    {
        if ($this->_filter->isUri($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value if it is a valid US ZIP, FALSE otherwise.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function testZip($key)
    {
        if ($this->_filter->isZip($this->_source[$key])) {
            return $this->_source[$key];
        }

        return FALSE;
    }


    /**
     * Returns value with all tags removed.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function noTags($key)
    {
        return $this->_filter->noTags($this->_source[$key]);
    }


    /**
     * Returns basename(value).
     *
     * @param  mixed $key
     * @return mixed
     */
    public function noPath($key)
    {
        return $this->_filter->noPath($this->_source[$key]);
    }
}
?>