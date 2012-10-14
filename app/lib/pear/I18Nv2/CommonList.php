<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: CommonList                                         |
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
 * I18Nv2::CommonList
 * 
 * @author      Michael Wallner <mike@php.net>
 * @package     I18Nv2
 * @category    Internationalization
 */

/** 
 * I18Nv2_CommonList
 * 
 * Base class for I18Nv2_Country and I18Nv2_Language that performs some basic
 * work, so code doesn't get written twice or even more often in the future.
 *
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @access      public
 */
class I18Nv2_CommonList
{
    /**
     * Codes
     * 
     * @access  protected
     * @var     array
     */
    var $codes = array();
    
    /**
     * Language
     * 
     * @access  protected
     * @var     string
     */
    var $language = '';
    
    /**
     * Encoding
     * 
     * @access  protected
     * @var     string
     */
    var $encoding = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string  $language
     * @param   string  $encoding
     */
    function I18Nv2_CommonList($language = null, $encoding = null)
    {
        if (!$this->setLanguage($language)) {
            if (class_exists('I18Nv2')) {
                $l = I18Nv2::lastLocale(0, true);
                if (!isset($l) || !$this->setLanguage($l['language'])) {
                    $this->setLanguage('en');
                }
            } else {
                $this->setLanguage('en');
            }
        }
        if (!$this->setEncoding($encoding)) {
            $this->setEncoding('UTF-8');
        }
    }

    /**
     * Set active language
     * 
     * Note that each time you set a different language the corresponding
     * language file has to be loaded again, too.
     *
     * @access  public
     * @return  bool
     * @param   string  $language
     */
    function setLanguage($language)
    {
        if (!isset($language)) {
            return false;
        }
        $language = strToLower($language);
        if ($language === $this->language) {
            return true;
        }
        if ($this->loadLanguage($language)) {
            $this->language = $language;
            return true;
        }
        return false;
    }
    
    /**
     * Get current language
     * 
     * @access  public
     * @return  string
     */
    function getLanguage()
    {
        return $this->language;
    }
    
    /**
     * Set active encoding
     *
     * @access  public
     * @return  bool
     * @param   string  $encoding
     */
    function setEncoding($encoding)
    {
        if (!isset($encoding)) {
            return false;
        }
        $this->encoding = strToUpper($encoding);
        return true;
    }
    
    /** 
     * Get current encoding
     * 
     * @access  public
     * @return  string
     */
    function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Check if code is valid
     * 
     * @access  public
     * @return  bool
     * @param   string  $code   code
     */
    function isValidCode($code)
    {
        return isset($this->codes[$this->changeKeyCase($code)]);
    }

    /**
     * Return corresponding name of code
     * 
     * @access  public
     * @return  string  name
     * @param   string  $code   code
     */
    function getName($code)
    {
        $code = $this->changeKeyCase($code);
        if (!isset($this->codes[$code])) {
            return '';
        }
        if ('UTF-8' !== $this->encoding) {
            return iconv('UTF-8', $this->encoding .'//TRANSLIT', $this->codes[$code]);
        }
        return $this->codes[$code];
    }

    /**
     * Return all the codes
     *
     * @access  public
     * @return  array   all codes as associative array
     */
    function getAllCodes()
    {
        if ('UTF-8' !== $this->encoding) {
            $codes = $this->codes;
            array_walk($codes, array(&$this, '_iconv'));
            return $codes;
        }
        return $this->codes;
    }
    
    /**
     * @access  private
     * @return  void
     */
    function _iconv(&$code, $key)
    {
        $code = iconv('UTF-8', $this->encoding .'//TRANSLIT', $code);
    }
    
    /** 
     * Load Language
     * 
     * @access  proteceted
     * @return  bool
     * @param   string  $language
     */
    function loadLanguage($language)
    {
        return false;
    }
    
    /**
     * Change Key Case
     *
     * @access  protected
     * @return  string
     * @param   string  $code
     */
    function changeKeyCase($code)
    {
        return $code;
    }
    
    /**
     * Decorate this list
     *
     * @access  public
     * @return  object  I18NV2_DecoratedList
     * @param   string  $type
     */
    function &toDecoratedList($type)
    {
        require_once 'I18Nv2/DecoratedList/'. $type .'.php';
        $decoratedList = 'I18Nv2_DecoratedList_' . $type;
        return new $decoratedList($this);
    }    
}
?>
