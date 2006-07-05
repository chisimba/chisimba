<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * Input filters class
 *
 * @author Paul Scott based on methods found in the Zend Framework 0.1.4 pre-release
 * @copyright AVOIR UWC GNU/GPL
 * @package utilities
 * @filesource
 */

class inputfilters extends object
{
	/**
	 * Class to handle inputs and filter user input
	 * This is a semi-validation class, but more of a sanitising object
	 * for user inputs from forms etc
	 *
	 * @access public
	 */

	/**
     * Options for isHostname() that specify which types of hostnames
     * to allow.
     *
     * HOST_ALLOW_DNS:   Allows Internet domain names (e.g.,
     *                   example.com).
     * HOST_ALLOW_IP:    Allows IP addresses.
     * HOST_ALLOW_LOCAL: Allows local network names (e.g., localhost,
     *                   www.localdomain) and Internet domain names.
     * HOST_ALLOW_ALL:   Allows all of the above types of hostnames.
     */
    const HOST_ALLOW_DNS   = 1;
    const HOST_ALLOW_IP    = 2;
    const HOST_ALLOW_LOCAL = 4;
    const HOST_ALLOW_ALL   = 7;

	/**
	 * Standard init function
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{

	}

	/**
     * Returns only the alphabetic characters in value.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function getAlpha($value)
    {
        return preg_replace('/[^[:alpha:]]/', '', $value);
    }

    /**
     * Returns only the alphabetic characters and digits in value.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function getAlnum($value)
    {
        return preg_replace('/[^[:alnum:]]/', '', $value);
    }

    /**
     * Returns only the digits in value. This differs from getInt().
     *
     * @param mixed $value
     * @return mixed
     */
    public static function getDigits($value)
    {
        return preg_replace('/[^\d]/', '', $value);
    }

    /**
     * Returns dirname(value).
     *
     * @param mixed $value
     * @return mixed
     */
    public static function getDir($value)
    {
        return dirname($value);
    }

    /**
     * Returns (int) value.
     *
     * @param mixed $value
     * @return int
     */
    public static function getInt($value)
    {
        return (int) $value;
    }

    /**
     * Returns realpath(value).
     *
     * @param mixed $value
     * @return mixed
     */
    public static function getPath($value)
    {
        return realpath($value);
    }

    /**
     * Returns value if every character is alphabetic or a digit,
     * FALSE otherwise.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isAlnum($value)
    {
        return ctype_alnum($value);
    }

    /**
     * Returns value if every character is alphabetic, FALSE
     * otherwise.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isAlpha($value)
    {
        return ctype_alpha($value);
    }

    /**
     * Returns value if it is greater than or equal to $min and less
     * than or equal to $max, FALSE otherwise. If $inc is set to
     * FALSE, then the value must be strictly greater than $min and
     * strictly less than $max.
     *
     * @param mixed $key
     * @param mixed $min
     * @param mixed $max
     * @param boolean $inclusive
     * @return mixed
     */
    public static function isBetween($value, $min, $max, $inc = TRUE)
    {
        if ($value > $min &&
            $value < $max) {
            return TRUE;
        }

        if ($value >= $min &&
            $value <= $max &&
            $inc) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Returns value if it is a valid credit card number format. The
     * optional second argument allows developers to indicate the
     * type.
     *
     * @param mixed $value
     * @param mixed $type
     * @return mixed
     */
    public static function isCcnum($value, $type = NULL)
    {
        /**
         * @todo Type-specific checks
         */

        $length = strlen($value);

        if ($length < 13 || $length > 19) {
            return FALSE;
        }

        $sum = 0;
        $weight = 2;

        for ($i = $length - 2; $i >= 0; $i--) {
            $digit = $weight * $value[$i];
            $sum += floor($digit / 10) + $digit % 10;
            $weight = $weight % 2 + 1;
        }

        $mod = (10 - $sum % 10) % 10;

        return ($mod == $value[$length - 1]);
    }

    /**
     * Returns $value if it is a valid date, FALSE otherwise. The
     * date is required to be in ISO 8601 format.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isDate($value)
    {
        list($year, $month, $day) = sscanf($value, '%d-%d-%d');

        return checkdate($month, $day, $year);
    }

    /**
     * Returns value if every character is a digit, FALSE otherwise.
     * This is just like isInt(), except there is no upper limit.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isDigits($value)
    {
        return ctype_digit($value);
    }

    /**
     * Returns value if it is a valid email format, FALSE otherwise.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isEmail($value)
    {
        /**
         * @todo RFC 2822 (http://www.ietf.org/rfc/rfc2822.txt)
         */
    }

    /**
     * Returns value if it is a valid float value, FALSE otherwise.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isFloat($value)
    {
        $locale = localeconv();

        $value = str_replace($locale['decimal_point'], '.', $value);
        $value = str_replace($locale['thousands_sep'], '', $value);

        return (strval(floatval($value)) == $value);
    }

    /**
     * Returns value if it is greater than $min, FALSE otherwise.
     *
     * @param mixed $value
     * @param mixed $min
     * @return mixed
     */
    public static function isGreaterThan($value, $min)
    {
        return ($value > $min);
    }

    /**
     * Returns value if it is a valid hexadecimal format, FALSE
     * otherwise.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isHex($value)
    {
        return ctype_xdigit($value);
    }

    /**
     * Returns value if it is a valid hostname, FALSE otherwise.
     * Depending upon the value of $allow, Internet domain names, IP
     * addresses, and/or local network names are considered valid.
     * The default is HOST_ALLOW_ALL, which considers all of the
     * above to be valid.
     *
     * @param mixed $value
     * @param integer $allow bitfield for HOST_ALLOW_DNS, HOST_ALLOW_IP, HOST_ALLOW_LOCAL
     * @throws Zend_Filter_Exception
     * @return mixed
     */
    public static function isHostname($value, $allow = self::HOST_ALLOW_ALL)
    {
        if (!is_numeric($allow) || !is_int($allow)) {
            throw new Zend_Filter_Exception('Illegal value for $allow; expected an integer');
        }

        if ($allow < self::HOST_ALLOW_DNS || self::HOST_ALLOW_ALL < $allow) {
            throw new customException('Illegal value for $allow; expected integer between ' .
                                            self::HOST_ALLOW_DNS . ' and ' . self::HOST_ALLOW_ALL);
        }

        // determine whether the input is formed as an IP address
        $status = self::isIp($value);

        // if the input looks like an IP address
        if ($status) {
            // if IP addresses are not allowed, then fail validation
            if (($allow & self::HOST_ALLOW_IP) == 0) {
                return FALSE;
            }

            // IP passed validation
            return TRUE;
        }

        // check input against domain name schema
        $status = @preg_match('/^(?:[^\W_](?:[^\W_]|-){0,61}[^\W_]\.)+[a-zA-Z]{2,6}\.?$/', $value);
        if ($status === false) {
            throw new customException('Internal error: DNS validation failed');
        }

        // if the input passes as an Internet domain name, and domain names are allowed, then the hostname
        // passes validation
        if ($status == 1 && ($allow & self::HOST_ALLOW_DNS) != 0) {
            return TRUE;
        }

        // if local network names are not allowed, then fail validation
        if (($allow & self::HOST_ALLOW_LOCAL) == 0) {
            return FALSE;
        }

        // check input against local network name schema; last chance to pass validation
        $status = @preg_match('/^(?:[^\W_](?:[^\W_]|-){0,61}[^\W_]\.)*(?:[^\W_](?:[^\W_]|-){0,61}[^\W_])\.?$/',
                              $value);
        if ($status === FALSE) {
            throw new customException('Internal error: local network name validation failed');
        }

        if ($status == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Returns value if it is a valid integer value, FALSE otherwise.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isInt($value)
    {
        $locale = localeconv();

        $value = str_replace($locale['decimal_point'], '.', $value);
        $value = str_replace($locale['thousands_sep'], '', $value);

        return (strval(intval($value)) == $value);
    }

    /**
     * Returns value if it is a valid IP format, FALSE otherwise.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isIp($value)
    {
        return (bool) ip2long($value);
    }

    /**
     * Returns value if it is less than $max, FALSE otherwise.
     *
     * @param mixed $value
     * @param mixed $max
     * @return mixed
     */
    public static function isLessThan($value, $max)
    {
        return ($value < $max);
    }

    /**
     * Returns value if it is a valid format for a person's name,
     * FALSE otherwise.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isName($value)
    {
        return (bool) !preg_match('/[^[:alpha:]\ \-\']/', $value);
    }

    /**
     * Returns value if it is one of $allowed, FALSE otherwise.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function isOneOf($value, $allowed = NULL)
    {
        /**
         * @todo: Consider allowing a string for $allowed, where each
         * character in the string is an allowed character in the
         * value.
         */

        return in_array($value, $allowed);
    }

    /**
     * Returns value if it matches $pattern, FALSE otherwise. Uses
     * preg_match() for the matching.
     *
     * @param mixed $value
     * @param mixed $pattern
     * @return mixed
     */
    public static function isRegex($value, $pattern = NULL)
    {
        return (bool) preg_match($pattern, $value);
    }

    /**
     * Returns value with all tags removed.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function noTags($value)
    {
        return strip_tags($value);
    }

    /**
     * Returns basename(value).
     *
     * @param mixed $value
     * @return mixed
     */
    public static function noPath($value)
    {
        return basename($value);
    }
}
?>