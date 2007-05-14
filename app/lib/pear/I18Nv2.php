<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2                                                       |
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
 * I18Nv2
 * 
 * @package     I18Nv2
 * @category    Internationalization
 */

define('I18Nv2_WIN', defined('OS_WINDOWS') ? OS_WINDOWS : (strToUpper(substr(PHP_OS, 0,3)) === 'WIN'));

/** 
 * I18Nv2 - Internationalization v2
 *
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @package     I18Nv2
 * @access      public
 * @static
 */
class I18Nv2
{
    /**
     * Set Locale
     * 
     * Example:
     * <code>
     * I18Nv2::setLocale('en_GB');
     * </code>
     * 
     * @static
     * @access  public
     * @return  mixed   &type.string; used locale or false on failure
     * @param   string  $locale     a valid locale like en_US or de_DE
     * @param   int     $cat        the locale category - usually LC_ALL
     */
    function setLocale($locale = null, $cat = LC_ALL)
    {
        if (!strlen($locale)) {
            return setLocale($cat, null);
        }
        
        $locales = I18Nv2::getStaticProperty('locales');
        
        // get complete standard locale code (en => en_US)
        if (isset($locales[$locale])) {
            $locale = $locales[$locale];
        }
        
        // get Win32 locale code (en_US => enu)
        if (I18Nv2_WIN) {
            $windows   = I18Nv2::getStaticProperty('windows');
            $setlocale = isset($windows[$locale]) ? $windows[$locale] : $locale;
        } else {
            $setlocale = $locale;
        }

        $syslocale = setLocale($cat, $setlocale);
        
        // if the locale is not recognized by the system, check if there 
        // is a fallback locale and try that, otherwise return false
        if (!$syslocale) {
            $fallbacks = &I18Nv2::getStaticProperty('fallbacks');
            if (isset($fallbacks[$locale])) {
                // avoid endless recursion with circular fallbacks
                $trylocale = $fallbacks[$locale];
                unset($fallbacks[$locale]);
                if ($retlocale = I18Nv2::setLocale($trylocale, $cat)) {
                    $fallbacks[$locale] = $trylocale;
                    return $retlocale;
                }
            }
            return false;
        }
        
        $language = substr($locale, 0,2);
        
        if (I18Nv2_WIN) {
            @putEnv('LANG='     . $language);
            @putEnv('LANGUAGE=' . $language);
        } else {
            @putEnv('LANG='     . $locale);
            @putEnv('LANGUAGE=' . $locale);
        }
        
        // unshift locale stack
        $last = &I18Nv2::getStaticProperty('last');
        array_unshift($last, 
            array(
                0           => $locale, 
                1           => $language, 
                2           => $syslocale,
                'locale'    => $locale,
                'language'  => $language,
                'syslocale' => $syslocale,
            )
        );
        
        // fetch locale specific information
        $info = &I18Nv2::getStaticProperty('info');
        $info = localeConv();
        
        return $syslocale;
    }
    
    /**
     * Get current/prior Locale
     *
     * @static
     * @access  public
     * @return  mixed   last locale as string or array
     * @param   int     $prior  if 0, the current otherwise n prior to current
     * @param   bool    $part   true|all|0=locale|1=language|2=syslocale
     */
    function lastLocale($prior = 0, $part = 0)
    {
        $last = I18Nv2::getStaticProperty('last');
        if (!isset($last)) {
            return I18Nv2::setLocale();
        }
        if (!isset($last[$prior])) {
            return null;
        }
        if ($part === true || $part == 'all') {
            return $last[$prior];
        }
        return $last[$prior][$part];
    }
    
    /**
     * Get several locale specific information
     * 
     * @see     http://www.php.net/localeconv
     * 
     * <code>
     * $locale = I18Nv2::setLocale('en_US');
     * $dollar = I18Nv2::getInfo('currency_symbol');
     * $point  = I18Nv2::getInfo('decimal_point');
     * </code>
     * 
     * @static
     * @access  public
     * @return  mixed
     * @param   string  $part
     */
    function getInfo($part = null)
    {
        $info = &I18Nv2::getStaticProperty('info');
        return isset($part, $info[$part]) ? $info[$part] : $info;
    }
    
    /**
     * Create a Locale object
     *
     * @static
     * @access  public
     * @return  object  I18Nv2_Locale
     * @param   string  $locale     The locale to use.
     * @param   bool    $paranoid   Whether to operate in paranoid mode.
     */
    function &createLocale($locale = null, $paranoid = false)
    {
        require_once 'I18Nv2/Locale.php';
        $obj = &new I18Nv2_Locale($locale, $paranoid);
        return $obj;
    }
    
    /**
     * Create a Negotiator object
     *
     * @static
     * @access  public
     * @return  object  I18Nv2_Negotiator
     * @param   string  $defLang        default language
     * @param   string  $defEnc         default encoding
     * @param   string  $defCtry        default country
     */
    function &createNegotiator($defLang = 'en', $defEnc = 'iso-8859-1', $defCtry = '')
    {
        require_once 'I18Nv2/Negotiator.php';
        $obj = &new I18Nv2_Negotiator($defLang, $defEnc, $defCtry);
        return $obj;
    }
    
    /**
     * Automatically transform output between encodings
     *
     * This method utilizes ob_iconv_handler(), so you should call it at the 
     * beginning of your script (prior to output).  If any output buffering has 
     * been started before, the contents will be fetched with ob_get_contents() 
     * and the buffers will be destroyed by ob_end_clean() if $refetchOB is set
     * to true.
     * 
     * <code>
     * require_once('I18Nv2.php');
     * I18Nv2::autoConv('CP1252');
     * print('...'); // some iso-8859-1 stuff gets converted to Windows-1252
     * // ...
     * </code>
     * 
     * @static
     * @access  public
     * @return  mixed   Returns &true; on success or 
     *                  <classname>PEAR_Error</classname> on failure.
     * @param   string  $oe             desired output encoding
     * @param   string  $ie             internal encoding
     * @param   bool    $decodeRequest  whether to decode request variables
     *                                  ($_GET and $_POST) from $oe to $ie
     * @param   bool    $refetchOB      whether contents of already active 
     *                                  output buffers should be fetched, the
     *                                  output buffer handlers destroyed and
     *                                  the fetched data be passed through
     *                                  ob_iconvhandler
     */
    function autoConv($oe = 'UTF-8', $ie = 'ISO-8859-1', $decodeRequest = true, $refetchOB = true)
    {
        if (!strcasecmp($oe, $ie)) {
            return true;
        }
        
        if (!extension_loaded('iconv')) {
            require_once 'PEAR.php';
            if (!PEAR::loadExtension('iconv')) {
                return PEAR::raiseError('Error: ext/iconv is not available');
            }
        }
        
        iconv_set_encoding('internal_encoding', $ie);
        iconv_set_encoding('output_encoding', $oe);
        iconv_set_encoding('input_encoding', $oe);
        
        $buffer = '';
        if ($refetchOB && $level = ob_get_level()) {
            while ($level--) {
                $buffer .= ob_get_contents();
                ob_end_clean();
            }
        }
        
        if (!ob_start('ob_iconv_handler')) {
            require_once 'PEAR.php';
            return PEAR::raiseError('Couldn\'t start output buffering');
        }
        echo $buffer;
        
        if ($decodeRequest) {
            I18Nv2::recursiveIconv($_GET, $oe, $ie);
            I18Nv2::recursiveIconv($_POST, $oe, $ie);
        }
        
        return true;
    }
    
    /**
     * Recursive Iconv
     * 
     * @static
     * @access  public
     * @return  void
     * @param   array   $value
     * @param   string  $from
     * @param   string  $to
     */
    function recursiveIconv(&$value, $from, $to)
    {
        foreach ($value as $key => $val) {
            if (is_array($val)) {
                I18Nv2::recursiveIconv($value[$key], $from, $to);
            } else {
                $value[$key] = iconv($from, $to .'//TRANSLIT', $val);
            }
        }
    }
    
    /**
     * Traverse locales to languages
     * 
     * Returns en-US, de-DE from en_US and de_DE
     *
     * @static
     * @access  public
     * @return  array
     * @param   array   $locales
     */
    function locales2langs($locales)
    {
        return array_map(array('I18Nv2','l2l'), (array) $locales);
    }
    
    /**
     * Traverse languages to locales
     *
     * Returns en_US, de_DE from en-US and de-DE
     *
     * @static
     * @access  public
     * @return  array
     * @param   array   $languages
     */
    function langs2locales($languages)
    {
        return array_map(array('I18Nv2','l2l'), (array) $languages);
    }
    
    /**
     * Locale to language or language to locale
     *
     * @static
     * @access  public
     * @return  string
     * @param   string  $localeOrLanguage
     */
    function l2l($localeOrLanguage)
    {
        return strtr($localeOrLanguage, '-_', '_-');
    }
    
    /**
     * Split locale code
     * 
     * Splits locale codes into its language and country part
     *
     * @static
     * @access  public
     * @return  array
     * @param   string  $locale
     */
    function splitLocale($locale)
    {
        @list($l, $c) = preg_split('/[_-]/', $locale, 2, PREG_SPLIT_NO_EMPTY);
        return array($l, $c);
    }
    
    /**
     * Get language code of locale
     *
     * @static
     * @access  public
     * @return  string
     * @patram  string  $locale
     */
    function languageOf($locale)
    {
        return current($a = I18Nv2::splitLocale($locale));
    }
    
    /**
     * Get country code of locale
     *
     * @static
     * @access  public
     * @return  string
     * @param   string  $locale
     */
    function countryOf($locale)
    {
        return end($a = I18Nv2::splitLocale($locale));
    }
    
    /**
     * Get access to static property
     * 
     * @static
     * @access  public
     * @return  mixed   Returns a reference to a static property
     * @param   string  $property   the static property
     */
    function &getStaticProperty($property)
    {
        static $properties;
        return $properties[$property];
    }
    
    /**
     * This one gets called automatically
     *
     * @ignore
     * @static
     * @internal
     * @access  private
     * @return  void
     */
    function _main()
    {
        // initialize the locale stack
        $last = &I18Nv2::getStaticProperty('last');
        $last = array();
        
        // map of "fully qualified locale" codes
        $locales = &I18Nv2::getStaticProperty('locales');
        $locales = array(
            'af' => 'af_ZA',
            'de' => 'de_DE',
            'en' => 'en_US',
            'fr' => 'fr_FR',
            'it' => 'it_IT',
            'es' => 'es_ES',
            'pt' => 'pt_PT',
            'sv' => 'sv_SE',
            'nb' => 'nb_NO',
            'nn' => 'nn_NO',
            'no' => 'no_NO',
            'fi' => 'fi_FI',
            'is' => 'is_IS',
            'da' => 'da_DK',
            'nl' => 'nl_NL',
            'pl' => 'pl_PL',
            'sl' => 'sl_SI',
            'hu' => 'hu_HU',
            'ru' => 'ru_RU',
            'cs' => 'cs_CZ',
        );
        
        // define locale fallbacks
        $fallbacks = &I18Nv2::getStaticProperty('fallbacks');
        $fallbacks = array(
            'no_NO' => 'nb_NO',
            'nb_NO' => 'no_NO',
        );
        
        // include Win32 locale codes
        if (I18Nv2_WIN) {
            include_once 'I18Nv2/Locale/Windows.php';
        }
    }
}

I18Nv2::_main();

?>