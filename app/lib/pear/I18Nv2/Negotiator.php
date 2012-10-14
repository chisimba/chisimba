<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Negotiator                                         |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is available at http://www.php.net/license/3_0.txt              |
// | If you did not receive a copy of the PHP license and are unable      |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 The Authors                                       |
// +----------------------------------------------------------------------+
// | Authors:   Naoki Shima <murahachibu@php.net>                         |
// |            Wolfram Kriesing <wk@visionp.de>                          |
// |            Michael Wallner <mike@iworks.at>                          |
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * I18Nv2::Negotiator
 *
 * @package      I18Nv2
 * @category     Internationalization
 */

/**
 * I18Nv2_Negotiator
 * 
 * @author      Naoki Shima <murahachibu@php.net>
 * @author      Wolfram Kriesing <wk@visionp.de>
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @access      public
 * @package     I18Nv2
 */
class I18Nv2_Negotiator
{
    /**
     * I18Nv2_Language
     * 
     * @var     object
     * @access  public
     */
    var $I18NLang = null;
    
    /**
     * I18Nv2_Country
     * 
     * @var     object
     * @access  public
     */
    var $I18NCountry = null;
    
    /**
     * Save default country code.
     *
     * @var     string
     * @access  private
     */
    var $_defaultCountry;

    /**
     * Save default language code.
     *
     * @var     string
     * @access  private
     */
    var $_defaultLanguage;

    /**
     * Save default encoding code.
     *
     * @var     string
     * @access  private
     */
    var $_defaultEncoding;

    /**
     * HTTP_ACCEPT_CHARSET
     * 
     * @var     array
     * @access  private
     */
    var $_acceptEncoding = array();
    
    /**
     * HTTP_ACCEPT_LANGUAGE
     * 
     * @var     array
     * @access  private
     */
    var $_acceptLanguage = array();
    
    /**
     * Language variations
     * 
     * @var     array
     * @access  private
     */
    var $_langVariation = array();
    
    /**
     * Countries
     * 
     * @var     array
     * @access  private
     */
    var $_country = array();
    
    /**
     * Constructor
     * 
     * Find language code, country code, encoding code, and dialect or variant
     * of Locale setting in HTTP request headers.
     *
     * @access  public
     * @param   string  $defaultLanguage    Default Language
     * @param   string  $defaultEncoding    Default Encoding
     * @param   string  $defaultCountry     Default Country
     */
    function I18Nv2_Negotiator($defaultLanguage = 'en', $defaultEncoding = 'iso-8859-1', $defaultCountry = '')
    {
        $this->__construct($defaultLanguage, $defaultEncoding, $defaultCountry);
    }
    
    /**
     * ZE2 Constructor
     * @ignore
     */
    function __construct($defaultLanguage = 'en', $defaultEncoding = 'iso-8859-1', $defaultCountry = '')
    {
        $this->_defaultCountry  = $defaultCountry;
        $this->_defaultLanguage = $defaultLanguage;
        $this->_defaultEncoding  = $defaultEncoding;
        
        $this->_negotiateLanguage();
        $this->_negotiateEncoding();
    }
    
    /**
     * Negotiate Language
     *
     * @access  private
     * @return  void
     */
    function _negotiateLanguage()
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return;
        }
        foreach(explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $lang) {
            // Cut off any q-value that might come after a semi-colon
            if ($pos = strpos($lang, ';')) {
                $lang = trim(substr($lang, 0, $pos));
            }
            if (strstr($lang, '-')) {
                list($pri, $sub) = explode('-', $lang);
                if ($pri == 'i') {
                    /**
                    * Language not listed in ISO 639 that are not variants
                    * of any listed language, which can be registerd with the
                    * i-prefix, such as i-cherokee
                    */
                    $lang = $sub;
                } else {
                    $lang = $pri;
                    $this->singleI18NCountry();
                    if ($this->I18NCountry->isValidCode($sub)) {
                        $this->_country[$lang][] = strToUpper($sub);
                    } else { 
                        $this->_langVariation[$lang][] = $sub;
                    }
                }
            }
            $this->_acceptLanguage[] = $lang;
        }
    }
    
    /**
     * Negotiate Encoding
     *
     * @access  private
     * @return  void
     */
    function _negotiateEncoding()
    {
        if (!isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
            return;
        }
        foreach (explode(',', $_SERVER['HTTP_ACCEPT_CHARSET']) as $encoding) {
            if (!empty($encoding)) {
                $this->_acceptEncoding[] = preg_replace('/;.*/', '', $encoding);
            }
        }
    }
    
    /**
     * Find Country Match
     *
     * @access  public
     * @return  array
     * @param   string  $lang
     * @param   array   $countries
     */
    function getCountryMatch($lang, $countries = null)
    {
        return $this->_getMatch(
            $countries,
            @$this->_country[$lang],
            $this->_defaultCountry
        );
    }
 
    /**
     * Return variant info for passed parameter.
     *
     * @access  public
     * @return  string
     * @param   string  $lang
     */
    function getVariantInfo($lang)
    {
        return isset($this->_langVariation[$lang]) ? $this->_langVariation[$lang] : null;
    }

    /**
     * Find Encoding match
     * 
     * @deprecated
     * @access  public
     * @return  string
     * @param   array   $encodings
     */
    function getCharsetMatch($encodings = null)
    {
        return $this->_getMatch(
            $encodings, 
            $this->_acceptEncoding, 
            $this->_defaultEncoding
        );
    }

    /**
     * Find Encoding match
     * 
     * @access  public
     * @return  string
     * @param   array   $encodings
     */
    function getEncodingMatch($encodings = null)
    {
        return $this->_getMatch(
            $encodings, 
            $this->_acceptEncoding, 
            $this->_defaultEncoding
        );
    }

    /**
     * Find Language match
     *
     * @access  public
     * @return  string
     * @param   array   $langs
     */
    function getLanguageMatch($langs = null)
    {
        return $this->_getMatch(
            $langs, 
            $this->_acceptLanguage,
            $this->_defaultLanguage
        );
    }
    
    /**
     * Find locale match
     *
     * @access  public
     * @return  string
     * @param   array   $langs
     * @param   array   $countries
     */
    function getLocaleMatch($langs = null, $countries = null)
    {
        $lang = $this->_getMatch($langs, $this->_acceptLanguage, $this->_defaultLanguage);
        $ctry = $this->_getMatch($countries, @$this->_country[$lang], $this->_defaultCountry);
        return $lang . ($ctry ? '_' . $ctry : '');
    }
    
    /**
     * Return first matched value from first and second parameter.
     * If there is no match found, then return third parameter.
     * 
     * @access  private
     * @return  string
     * @param   array   $needle
     * @param   array   $haystack
     * @param   string  $default
     */
    function _getMatch($needle, $haystack, $default = '')
    {
        if (!$haystack) {
            return $default;
        }
        if (!$needle) {
            return current($haystack);
        }
        if ($result = current($a = array_intersect($haystack, $needle))) {
            return $result;
        }
        return $default;
    }
    
    /**
     * Find Country name for country code passed 
     * 
     * @access  private
     * @return  void
     * @param   string  $code   country code
     */
    function getCountryName($code)
    {
        $this->singleI18NCountry();
        return $this->I18NCountry->getName($code);
    }

    /**
     * Find Country name for country code passed 
     * 
     * @access  private
     * @return  void
     * @param   string      $code   language code
     */
    function getLanguageName($code)
    {
        $this->singleI18NLanguage();
        return $this->I18NLang->getName($code);
    }

    /**
     * Create the Language helper object
     * 
     * @access  public
     * @return  object
     */
    function &singleI18NLanguage()
    {
        if (!isset($this->I18NLang)) {
            include_once 'I18Nv2/Language.php';
            $this->I18NLang = &new I18Nv2_Language(
                $this->_defaultLanguage, 
                $this->_defaultEncoding
            );
        }
        return $this->I18NLang;
    }

    /**
     * Create the Country helper object
     * 
     * @access  public
     * @return  object
     */
    function &singleI18NCountry()
    {
        if (!isset($this->I18NCountry)) {
            include_once 'I18Nv2/Country.php';
            $this->I18NCountry = &new I18Nv2_Country(
                $this->_defaultLanguage,
                $this->_defaultEncoding
            );
        }
        return $this->I18NCountry;
    }
}
?>
