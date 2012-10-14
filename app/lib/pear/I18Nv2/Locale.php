<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Locale                                             |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is available at http://www.php.net/license/3_0.txt              |
// | If you did not receive a copy of the PHP license and are unable      |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Michael Wallner <mike@iworks.at>                  |
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * I18Nv2::Locale
 * 
 * @package      I18Nv2
 * @category     Internationalisation
 */

/**#@+ Constants **/
define('I18Nv2_NUMBER',                     'number');
define('I18Nv2_CURRENCY',                   'currency');
define('I18Nv2_DATE',                       'date');
define('I18Nv2_TIME',                       'time');
define('I18Nv2_DATETIME',                   'datetime');

define('I18Nv2_NUMBER_FLOAT' ,              'float');
define('I18Nv2_NUMBER_INTEGER' ,            'integer');

define('I18Nv2_CURRENCY_LOCAL',             'local');
define('I18Nv2_CURRENCY_INTERNATIONAL',     'international');

define('I18Nv2_DATETIME_SHORT',             'short');
define('I18Nv2_DATETIME_DEFAULT',           'default');
define('I18Nv2_DATETIME_MEDIUM',            'medium');
define('I18Nv2_DATETIME_LONG',              'long');
define('I18Nv2_DATETIME_FULL',              'full');
/**#@-*/

require_once 'PEAR.php';
require_once 'I18Nv2.php';

/** 
 * I18Nv2_Locale
 *
 * @author       Michael Wallner <mike@php.net>
 * @version      $Revision$
 * @access       public
 * @package      I18Nv2
 */
class I18Nv2_Locale
{
    /**
     * Initialized Locale
     * 
     * @access  protected
     * @var     string
     */
    var $initialized = '';
    
    /**
     * Full day names
     * 
     * @access  protected
     * @var     array
     */
    var $days = array();
    
    /**
     * Full month names
     * 
     * @access  protected
     * @var     array
     */
    var $months = array();
    
    /**
     * Abbreviated day names
     * 
     * @access  protected
     * @var     array
     */
    var $abbrDays = array();
    
    /**
     * Abbreviated month names
     * 
     * @access  protected
     * @var     array
     */
    var $abbrMonths = array();
    
    /**
     * Registered date formats
     * 
     * @access  protected
     * @var     array
     */
    var $dateFormats = array();

    /**
     * Registered time formats
     * 
     * @access  protected
     * @var     array
     */
    var $timeFormats = array();

    /**
     * Registered datetime formats
     * 
     * @access  protected
     * @var     array
     */
    var $dateTimeFormats = array();

    /**
     * Registered number formats
     * 
     * @access  protected
     * @var     array
     */
    var $numberFormats = array();

    /**
     * Registered currency formats
     * 
     * @access  protected
     * @var     array
     */
    var $currencyFormats = array();
    
    /**
     * Current time format
     * 
     * @access  protected
     * @var     mixed
     */
    var $currentTimeFormat = null;

    /**
     * Current date format
     * 
     * @access  protected
     * @var     mixed
     */
    var $currentDateFormat = null;

    /**
     * Current datetime format
     * 
     * @access  protected
     * @var     mixed
     */
    var $currentDateTimeFormat = null;

    /**
     * Current number format
     * 
     * @access  protected
     * @var     mixed
     */
    var $currentNumberFormat = null;

    /**
     * Current currency format
     * 
     * @access  protected
     * @var     mixed
     */
    var $currentCurrencyFormat = null;
    
    /**
     * Custom formats
     * 
     * @access  protected
     * @var     array
     */
    var $customFormats = array();
    
    /**
     * Locale Data Cache
     * 
     * @access  protected
     * @var     array
     */
    var $cache = array();
    
    /**
     * Whether to reset the global locale after each call
     * 
     * @access  protected
     * @var     bool
     */
    var $paranoid = false;
    
    /**
     * Store system locale for paranoid mode
     * 
     * @access  protected
     * @var     string
     */
    var $usedLocale = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string  $locale
     */
    function I18Nv2_Locale($locale = null, $paranoid = false)
    {
        $locale or $locale = I18Nv2::lastLocale(0, 'locale');
        $this->setLocale($locale);
        $this->setParanoid($paranoid);
    }
    
    /**
     * Set locale
     * 
     * This automatically calls I18Nv2_Locale::initialize()
     *
     * @access  public
     * @return  string  used system locale
     * @param   string  $locale
     * @param   bool    $force
     */
    function setLocale($locale, $force = false)
    {
        if (!$force && $this->initialized == $locale) {
            $last = I18Nv2::lastLocale(0, true);
            if (is_array($last)) {
                if (    $locale == $last['syslocale']   || 
                        $locale == $last['locale']      ||
                        $locale == $last['language']) {
                    return $last['syslocale'];
                }
            } elseif ($last == $locale) {
                return $last;
            }
        }
        
        return $this->initialize($locale);
    }
    
    /**
     * Initialize
     *
     * @access  public
     * @return  void
     */
    function initialize($locale)
    {
        $this->initialized = $locale;
        $this->usedLocale = I18Nv2::setLocale($locale);
        
        $jan = $mon = mktime(1,1,1,1,1,1990);
        $feb = $tue = mktime(1,1,1,2,6,1990);
        $mar = $wed = mktime(1,1,1,3,7,1990);
        $apr = $thu = mktime(1,1,1,4,5,1990);
        $may = $fri = mktime(1,1,1,5,4,1990);
        $jun = $sat = mktime(1,1,1,6,2,1990);
        $jul = $sun = mktime(1,1,1,7,1,1990);
        $aug = mktime(1,1,1,8,1,1990);
        $sep = mktime(1,1,1,9,1,1990);
        $oct = mktime(1,1,1,10,1,1990);
        $nov = mktime(1,1,1,11,1,1990);
        $dec = mktime(1,1,1,12,1,1990);
        
        if (!$this->loadCache($this->usedLocale)) {
            $this->days = array(
                strftime('%A', $sun),
                strftime('%A', $mon),
                strftime('%A', $tue),
                strftime('%A', $wed),
                strftime('%A', $thu),
                strftime('%A', $fri),
                strftime('%A', $sat),
            );
            
            $this->abbrDays = array(
                strftime('%a', $sun),
                strftime('%a', $mon),
                strftime('%a', $tue),
                strftime('%a', $wed),
                strftime('%a', $thu),
                strftime('%a', $fri),
                strftime('%a', $sat),
            );
    
            $this->months = array(
                strftime('%B', $jan),
                strftime('%B', $feb),
                strftime('%B', $mar),
                strftime('%B', $apr),
                strftime('%B', $may),
                strftime('%B', $jun),
                strftime('%B', $jul),
                strftime('%B', $aug),
                strftime('%B', $sep),
                strftime('%B', $oct),
                strftime('%B', $nov),
                strftime('%B', $dec),
            );
            
            $this->abbrMonths = array(
                strftime('%b', $jan),
                strftime('%b', $feb),
                strftime('%b', $mar),
                strftime('%b', $apr),
                strftime('%b', $may),
                strftime('%b', $jun),
                strftime('%b', $jul),
                strftime('%b', $aug),
                strftime('%b', $sep),
                strftime('%b', $oct),
                strftime('%b', $nov),
                strftime('%b', $dec),
            );
            
            $info = I18Nv2::getInfo();
            
            /*
             * The currency symbol is old shit on Win2k, though.
             * Some get extended/overwritten with other local conventions.
             */
            $this->currencyFormats = array(
                I18Nv2_CURRENCY_LOCAL => array(
                    $info['currency_symbol'],
                    $info['int_frac_digits'],
                    $info['mon_decimal_point'],
                    $info['mon_thousands_sep'],
                    $info['negative_sign'],
                    $info['positive_sign'],
                    $info['n_cs_precedes'],
                    $info['p_cs_precedes'],
                    $info['n_sep_by_space'],
                    $info['p_sep_by_space'],
                    $info['n_sign_posn'],
                    $info['p_sign_posn'],
                ),
                I18Nv2_CURRENCY_INTERNATIONAL => array(
                    $info['int_curr_symbol'],
                    $info['int_frac_digits'],
                    $info['mon_decimal_point'],
                    $info['mon_thousands_sep'],
                    $info['negative_sign'],
                    $info['positive_sign'],
                    $info['n_cs_precedes'],
                    $info['p_cs_precedes'],
                    true,
                    true,
                    $info['n_sign_posn'],
                    $info['p_sign_posn'],
                ),
            );
            
            $this->numberFormats = array(
                I18Nv2_NUMBER_FLOAT => array(
                    $info['frac_digits'],
                    $info['decimal_point'],
                    $info['thousands_sep']
                ),
                I18Nv2_NUMBER_INTEGER => array(
                    '0',
                    $info['decimal_point'],
                    $info['thousands_sep']
                ),
    
            );
            
            $this->loadExtension();
    
            if (!count($this->dateTimeFormats)) {
                $this->dateTimeFormats = array(
                    I18Nv2_DATETIME_SHORT   => 
                        $this->dateFormats[I18Nv2_DATETIME_SHORT]
                        . ', ' .
                        $this->timeFormats[I18Nv2_DATETIME_SHORT],
                    I18Nv2_DATETIME_MEDIUM   => 
                        $this->dateFormats[I18Nv2_DATETIME_MEDIUM]
                        . ', ' .
                        $this->timeFormats[I18Nv2_DATETIME_MEDIUM],
                    I18Nv2_DATETIME_DEFAULT   => 
                        $this->dateFormats[I18Nv2_DATETIME_DEFAULT]
                        . ', ' .
                        $this->timeFormats[I18Nv2_DATETIME_DEFAULT],
                    I18Nv2_DATETIME_LONG   => 
                        $this->dateFormats[I18Nv2_DATETIME_LONG]
                        . ', ' .
                        $this->timeFormats[I18Nv2_DATETIME_LONG],
                    I18Nv2_DATETIME_FULL   => 
                        $this->dateFormats[I18Nv2_DATETIME_FULL]
                        . ', ' .
                        $this->timeFormats[I18Nv2_DATETIME_FULL],
                );
            }
            $this->updateCache($this->usedLocale);
        }
        
        $this->setDefaults();
        
        if ($this->paranoid) {
            setlocale(LC_ALL, 'C');
        }
        
        return $this->usedLocale;
    }
    
    /**
     * Update Cache
     * 
     * @access  protected
     * @return  void
     * @param   string  $locale
     */
    function updateCache($locale)
    {
        if (!isset($this->cache[$locale])) {
            $cache = get_object_vars($this);
            $cvars = preg_grep(
                '/^(init|current|custom|cache)/', 
                array_keys($cache), 
                PREG_GREP_INVERT
            );
            foreach ($cvars as $var) {
                $this->cache[$locale][$var] = $cache[$var];
            }
        }
    }
    
    /**
     * Load Cache
     * 
     * @access  protected
     * @return  bool
     * @param   string  $locale
     */
    function loadCache($locale)
    {
        if (!isset($this->cache[$locale])) {
            return false;
        }
        foreach ($this->cache[$locale] as $var => $value) {
            $this->$var = $value;
        }
        return true;
    }
    
    /**
     * Loads corresponding locale extension
     *
     * @access  public
     * @return  void
     */
    function loadExtension()
    {
        $locale = I18Nv2::lastLocale(0, true);
        if (isset($locale)) {
            $dir = dirname(__FILE__);
            foreach (array($locale['language'], $locale['locale']) as $lc) {
                if (is_file($dir . '/Locale/' . $lc . '.php')) {
                    include $dir . '/Locale/' . $lc . '.php';
                }
            }
        }
    }
    
    /**
     * Set defaults
     *
     * @access  public
     * @return  void
     */
    function setDefaults()
    {
        $this->currentTimeFormat        = $this->timeFormats[I18Nv2_DATETIME_DEFAULT];
        $this->currentDateFormat        = $this->dateFormats[I18Nv2_DATETIME_DEFAULT];
        $this->currentDateTimeFormat    = $this->dateTimeFormats[I18Nv2_DATETIME_DEFAULT];
        $this->currentNumberFormat      = $this->numberFormats[I18Nv2_NUMBER_FLOAT];
        $this->currentCurrencyFormat    = $this->currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL];
    }
    
    /**
     * Set Paranoid Mode
     * 
     * Whether to reset to the C-locale after every call.
     * 
     * @access  public
     * @return  void
     * @param   bool    $paranoid Whether to enable paranoid mode.
     */
    function setParanoid($paranoid = false)
    {
        $this->paranoid = (bool) $paranoid;
        $paranoid and setLocale(LC_ALL, 'C');
    }
    
    /**
     * Set currency format
     *
     * @access  public
     * @return  mixed   Returns &true; on success or <classname>PEAR_Error</classname> on failure.
     * @param   int     $format     a I18Nv2_CURRENCY constant
     * @param   bool    $custom     whether to use a defined custom format
     */
    function setCurrencyFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->customFormats[$format])) {
                return PEAR::raiseError('Custom currency format "'.$format.'" doesn\'t exist.');
            }
            $this->currentCurrencyFormat = $this->customFormats[$format];
        } else {
            if (!isset($this->currencyFormats[$format])) {
                return PEAR::raiseError('Currency format "'.$format.'" doesn\'t exist.');
            }
            $this->currentCurrencyFormat = $this->currencyFormats[$format];
        }
        return true;
    }
    
    /**
     * Set number format
     *
     * @access  public
     * @return  mixed   Returns &true; on success or <classname>PEAR_Error</classname> on failure.
     * @param   int     $format     a I18Nv2_NUMBER constant
     * @param   bool    $custom     whether to use a defined custom format
     */
    function setNumberFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->customFormats[$format])) {
                return PEAR::raiseError('Custom number format "'.$format.'" doesn\'t exist.');
            }
            $this->currentNumberFormat = $this->customFormats[$format];
        } else {
            if (!isset($this->numberFormats[$format])) {
                return PEAR::raiseError('Number format "'.$format.'" doesn\'t exist.');
            }
            $this->currentNumberFormat = $this->numberFormats[$format];
        }
        return true;
    }
    
    /**
     * Set date format
     *
     * @access  public
     * @return  mixed   Returns &true; on success or <classname>PEAR_Error</classname> on failure.
     * @param   int     $format     a I18Nv2_DATETIME constant
     * @param   bool    $custom     whether to use a defined custom format
     */
    function setDateFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->customFormats[$format])) {
                return PEAR::raiseError('Custom date fromat "'.$format.'" doesn\'t exist.');
            }
            $this->currentDateFormat = $this->customFormats[$format];
        } else {
            if (!isset($this->dateFormats[$format])) {
                return PEAR::raiseError('Date format "'.$format.'" doesn\'t exist.');
            }
            $this->currentDateFormat = $this->dateFormats[$format];
        }
        return true;
    }
    
    /**
     * Set time format
     *
     * @access  public
     * @return  mixed
     * @param   int     $format     a I18Nv2_DATETIME constant
     * @param   bool    $custom     whether to use a defined custom format
     */
    function setTimeFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->customFormats[$format])) {
                return PEAR::raiseError('Custom time format "'.$format.'" doesn\'t exist.');
            }
            $this->currentTimeFormat = $this->customFormats[$format];
        } else {
            if (!isset($this->timeFormats[$format])) {
                return PEAR::raiseError('Time format "'.$format.'" doesn\'t exist.');
            }
            $this->currentTimeFormat = $this->timeFormats[$format];
        }
        return true;
    }
    
    /**
     * Set datetime format
     *
     * @access  public
     * @return  mixed
     * @param   int     $format     a I18Nv2_DATETIME constant
     * @param   bool    $custom     whether to use a defined custom format
     */
    function setDateTimeFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->customFormats[$format])) {
                return PEAR::raiseError('Custom datetime format "'.$format.'" doesn\'t exist.');
            }
            $this->currentDateTimeFormat = $this->customFormats[$format];
        } else {
            if (!isset($this->dateTimeFormats[$format])) {
                return PEAR::raiseError('Datetime format "'.$format.'" doesn\'t exist.');
            }
            $this->currentDateTimeFormat = $this->dateTimeFormats[$format];
        }
        return true;
    }
    
    /**
     * Set custom format
     *
     * If <var>$format</var> is omitted, the custom format for <var>$type</var>
     * will be dsicarded - if both vars are omitted all custom formats will
     * be discarded.
     * 
     * @access  public
     * @return  void
     * @param   mixed   $type
     * @param   mixed   $format
     */
    function setCustomFormat($type = null, $format = null)
    {
        if (!isset($format)) {
            if (!isset($type)) {
                $this->customFormats = array();
            } else {
                unset($this->customFormats[$type]);
            }
        } else {
            $this->customFormats[$type] = $format;
        }
    }
    
    /**
     * Format currency
     *
     * @access  public
     * @return  string
     * @param   numeric $value
     * @param   int     $overrideFormat
     * @param   string  $overrideSymbol
     */
    function formatCurrency($value, $overrideFormat = null, $overrideSymbol = null)
    {
        @list(
            $symbol, 
            $digits, 
            $decpoint, 
            $thseparator, 
            $sign['-'], 
            $sign['+'], 
            $precedes['-'], 
            $precedes['+'], 
            $separate['-'], 
            $separate['+'],
            $position['-'],
            $position['+']
        ) = isset($overrideFormat) ? 
            $this->currencyFormats[$overrideFormat] :
            $this->currentCurrencyFormat;

        if (isset($overrideSymbol)) {
            $symbol = $overrideSymbol;
        }
        
        // number_format the absolute value
        $amount = number_format(abs($value), $digits, $decpoint, $thseparator);
        
        $S = $value < 0 ? '-' : '+';
        
        // check posittion of the positive/negative sign(s)
        switch ($position[$S])
        {
            case 0: $amount  = '('. $amount .')';   break;
            case 1: $amount  = $sign[$S] . $amount; break;
            case 2: $amount .= $sign[$S];           break;
            case 3: $symbol  = $sign[$S] . $symbol; break;
            case 4: $symbol .= $sign[$S];           break;
        }
        
        // currency symbol precedes amount
        if ($precedes[$S]) {
            $amount = $symbol . ($separate[$S] ? ' ':'') . $amount;
        }
        // currency symbol succedes amount
        else {
            $amount .= ($separate[$S] ? ' ':'') . $symbol;
        }
        
        return $amount;
    }
    
    /**
     * Format a number
     *
     * @access  public
     * @return  string
     * @param   numeric $value
     * @param   int     $overrideFormat
     */
    function formatNumber($value, $overrideFormat = null)
    {
        list($dig, $dec, $sep) = isset($overrideFormat) ?
            $this->numberFormats[$overrideFormat] :
            $this->currentNumberFormat;
        return number_format($value, $dig, $dec, $sep);
    }
    
    /**
     * Format a date
     *
     * @access  public
     * @return  string
     * @param   int     $timestamp
     * @param   int     $overrideFormat
     */
    function formatDate($timestamp = null, $overrideFormat = null)
    {
        $format = isset($overrideFormat) ? 
            $this->dateFormats[$overrideFormat] : $this->currentDateFormat;
        $this->paranoid and setLocale(LC_ALL, $this->usedLocale);
        $date = strftime($format, isset($timestamp) ? $timestamp : time());
        $this->paranoid and setLocale(LC_ALL, 'C');
        return $date;
    }
    
    /**
     * Format a time
     *
     * @access  public
     * @return  string
     * @param   int     $timestamp
     * @param   int     $overrideFormat
     */
    function formatTime($timestamp = null, $overrideFormat = null)
    {
        $format = isset($overrideFormat) ? 
            $this->timeFormats[$overrideFormat] : $this->currentTimeFormat;
        $this->paranoid and setLocale(LC_ALL, $this->usedLocale);
        $time = strftime($format, isset($timestamp) ? $timestamp : time());
        $this->paranoid and setLocale(LC_ALL, 'C');
        return $time;
    }

    /**
     * Format a datetime
     *
     * @access  public
     * @return  string
     * @param   int     $timestamp
     * @param   int     $overrideFormat
     */
    function formatDateTime($timestamp = null, $overrideFormat = null)
    {
        $format = isset($overrideFormat) ?
            $this->dateTimeFormats[$overrideFormat] : 
            $this->currentDateTimeFormat;
        $this->paranoid and setLocale(LC_ALL, $this->usedLocale);
        $date = strftime($format, isset($timestamp) ? $timestamp : time());
        $this->paranoid and setLocale(LC_ALL, 'C');
        return $date;
    }
    
    /**
     * Locale time
     *
     * @access  public
     * @return  string
     * @param   int     $timestamp
     */
    function time($timestamp = null)
    {
        $this->paranoid and setLocale(LC_ALL, $this->usedLocale);
        $time = strftime('%X', isset($timestamp) ? $timestamp : time());
        $this->paranoid and setLocale(LC_ALL, 'C');
        return $time;
    }
    
    /**
     * Locale date
     *
     * @access  public
     * @return  string
     * @param   int     $timestamp
     */
    function date($timestamp = null)
    {
        $this->paranoid and setLocale(LC_ALL, $this->usedLocale);
        $date = strftime('%x', isset($timestamp) ? $timestamp : time());
        $this->paranoid and setLocale(LC_ALL, 'C');
        return $date;
    }
    
    /**
     * Day name
     *
     * @access  public
     * @return  mixed   Returns &type.string; name of weekday on success or
     *                  <classname>PEAR_Error</classname> on failure.
     * @param   int     $weekday    numerical representation of weekday
     *                              (0 = Sunday, 1 = Monday, ...)
     * @param   bool    $short  whether to return the abbreviation
     */
    function dayName($weekday, $short = false)
    {
        if ($short) {
            if (!isset($this->abbrDays[$weekday])) {
                return PEAR::raiseError('Weekday "'.$weekday.'" is out of range.');
            }
            return $this->abbrDays[$weekday];
        } else {
            if (!isset($this->days[$weekday])) {
                return PEAR::raiseError('Weekday "'.$weekday.'" is out of range.');
            }
            return $this->days[$weekday];
        }
    }
    
    /**
     * Month name
     *
     * @access  public
     * @return  mixed   Returns &type.string; name of month on success or
     *                  <classname>PEAR_Error</classname> on failure.
     * @param   int     $month  numerical representation of month
     *                          (0 = January, 1 = February, ...)
     * @param   bool    $short  whether to return the abbreviation
     */
    function monthName($month, $short = false)
    {
        if ($short) {
            if (!isset($this->abbrMonths[$month])) {
                return PEAR::raiseError('Month "'.$month.'" is out of range.');
            }
            return $this->abbrMonths[$month];
        } else {
            if (!isset($this->months[$month])) {
                return PEAR::raiseError('Month "'.$month.'" is out of range.');
            }
            return $this->months[$month];
        }
    }
}
?>
