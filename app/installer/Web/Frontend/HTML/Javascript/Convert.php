<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Tal Peer <tal@php.net>                                      |
// |          Pierre-Alain Joye <paj@pearfr.org>                          |
// +----------------------------------------------------------------------+
// $Id$
/**
 * A class for converting PHP variables into JavaScript variables
 *
 * Usage example:
 * <code>
 * $js = new HTML_Javascript_Convert()
 * $a = array('foo','bar','buz',1,2,3);
 * $b = $js->convertVar($a, 'arr', true);
 * </code>
 * or
 * <code>
 * echo HTML_Javascript_Convert::convertArray($a);
 * </code>
 *
 * @author Tal Peer <tal@php.net>
 * @author Pierre-Alain Joye <paj@pearfr.org>
 * @package HTML_Javascript
 * @subpackage Convert
 * @version 1.1.1
 * @licence http://www.php.net/license/3_0.txt PHP Licence 3.0
 * @access public
 * @example examples/js.php How to use Convert
 */

/**
 * Invalid variable error
 */
define('HTML_JAVASCRIPT_CONVERT_ERROR_INVVAR', 502, true);

if(!defined('HTML_JAVASCRIPT_NL')){
    /** Linefeed to use, default set to Unix linefeed.
     * Define it before the include/require if you want to
     * override it.
     */
    define('HTML_JAVASCRIPT_NL',"\n");
}

/**
 * PHP to Javascript conversion classes
 *
 * It converts Variable or value to Javascript.
 *
 * @package HTML_Javascript
 * @subpackage Convert
 */
class HTML_Javascript_Convert
{
    // {{{ escapeString

    /**
     * Used to terminate escape characters in strings,
     * as javascript doesn't allow them
     *
     * @param   string  the string to be processed
     * @return  mixed   the processed string
     *
     * @access public
     * @source
     */
    function escapeString($str)
    {
        $js_escape = array(
            "\r"    => '\r',
            "\n"    => '\n',
            "\t"    => '\t',
            "'"     => "\\'",
            '"'     => '\"',
            '\\' => '\\\\'
        );

        return strtr($str,$js_escape);
    }

    // }}} escapeString
    // {{{ convertVar

    /**
     * Converts  a PHP variable into a JS variable
     * you can safely provide strings, arrays
     * or booleans as arguments for this function
     *
     * @access public
     * @param  mixed   $var     the variable to convert
     * @param  string  $varname the variable name to declare
     * @param  boolean $global  if true, the JS var will be global
     * @return mixed   a PEAR_Error if no script was started
     *                 or the converted variable
     */
    function convertVar($var, $varname, $global = false)
    {
        $var_type    = gettype($var);
        switch ( $var_type ) {
            case 'boolean':
                return HTML_Javascript_Convert::convertBoolean(
                            $var, $varname, $global
                        );
                        
            case 'integer':
            case 'double':
                $ret = '';
                if ($global) {
                    $ret = 'var ';
                }
                $ret .= $varname.' = '.$var;
                return $ret.';'.HTML_JAVASCRIPT_NL;
                
            case 'string':
                return HTML_Javascript_Convert::convertString(
                            $var, $varname, $global
                        );
                        
            case 'array':
                return HTML_Javascript_Convert::convertArray(
                            $var, $varname, $global
                        );
                        
            case 'NULL':
                return HTML_Javascript_Convert::convertNull(
                            $varname, $global
                        );
                        
            default:
                return HTML_Javascript_Convert::raiseError(
                        HTML_JAVASCRIPT_ERROR_CONVERT_INVVAR, __FUNCTION__.':'.$var_type
                    );
        }
    }

    // }}} convertVar
    // {{{ raiseError

    /**
     * A custom error handler
     *
     * @access public
     * @param  integer $code the error code
     * @return mixed   false if the error code is invalid,
     *                 or a PEAR_Error otherwise
     */
    function raiseError($code,$str='')
    {
        require_once 'PEAR.php';
        switch ($code) {
            case HTML_JAVASCRIPT_CONVERT_ERROR_INVVAR:
                return PEAR::raiseError(
                    'Invalid variable:'.$str, $code 
                );
                
            default:
                return PEAR::raiseError(
                    'Unknown Error:'.$str, $code 
                );
        }
    }

    // }}} raiseError
    // {{{ convertString

    /**
     * Converts  a PHP string into a JS string
     *
     * @access public
     * @param  string  $str     the string to convert
     * @param  string  $varname the variable name to declare
     * @param  boolean $global  if true, the JS var will be global
     * @return mixed   a PEAR_Error if no script was started
     *                 or the converted string
     */
    function convertString($str, $varname, $global = false)
    {
        $var = '';
        if ($global) {
            $var = 'var ';
        }
        $str = HTML_Javascript_Convert::escapeString($str);
        $var .= $varname.' = "'.$str.'"';
        return $var.';'.HTML_JAVASCRIPT_NL;;
    }

    // }}} convertString
    // {{{ convertBoolean

    /**
     * Converts a PHP boolean variable into a JS boolean variable.
     * Note this function does not check the type of $bool, only if
     * the expression $bool is true or false.
     *
     * @access public
     * @param  boolean $bool    the boolean variable
     * @param  string  $varname the variable name to declare
     * @param  boolean $global  set to true to make the JS variable global
     * @return string  the value as javascript 
     */
    function convertBoolean($bool, $varname, $global = false)
    {
        $var = '';
        if ($global) {
            $var = 'var ';
        }
        $var    .= $varname.' = ';
        $var    .= $bool?'true':'false';
        return $var.';'.HTML_JAVASCRIPT_NL;
    }

    // }}} convertBoolean
    // {{{ convertNull

    /**
     * Converts a PHP null variable into a JS null value.
     *
     * @access public
     * @param  string  $varname the variable name to declare
     * @param  boolean $global  set to true to make the JS variable global
     * @return string  the value as javascript 
     */
    function convertNull($varname, $global = false)
    {
        $var = '';
        if($global) {
            $var = 'var ';
        }
        return $varname.' = null;'.HTML_JAVASCRIPT_NL;
    }

    // }}} convertNull
    
    // {{{ convertArray

    /**
     * Converts  a PHP array into a JS array
     * supports of multu-dimensional array.
     * Keeps keys as they are (associative arrays).
     *
     * @access public
     * @param  string  $arr     the array to convert
     * @param  string  $varname the variable name to declare
     * @param  boolean $global  if true, the JS var will be global
     * @param  int     $level   Not public, used for recursive calls
     * @return mixed   a PEAR_Error if no script was started
     *                 or the converted array
     */
    function convertArray($arr, $varname, $global = false, $level=0)
    {
        $var = '';
        if ($global) {
            $var = 'var ';
        }
        if ( is_array($arr) ){
            $length = sizeof($arr);
            $var    .= $varname . ' = Array('. $length .')'.HTML_JAVASCRIPT_NL;
            foreach ( $arr as $key=>$cell ){
                $jskey  = '"' . $key . '"';
                if ( is_array( $cell ) ){
                    $level++;
                    $var    .= HTML_Javascript_Convert::convertArray(
                                    $cell,'tmp'.$level,$global,$level
                                );
                    $var    .= $varname .
                                "[$jskey] = tmp$level".
                                HTML_JAVASCRIPT_NL;
                    $var    .= "tmp$level = null".HTML_JAVASCRIPT_NL;
                } else {
                    $value  = is_string($cell)?
                                '"' .
                                HTML_Javascript_Convert::escapeString($cell) .
                                '"'
                                :$cell;
                    $var    .= $varname . "[$jskey] = $value".
                                HTML_JAVASCRIPT_NL;
                }
            }
            return $var;
        } else {
            return HTML_Javascript_Convert::raiseError(
                        HTML_JAVASCRIPT_CONVERT_ERROR_INVVAR, __FUNCTION__.':'.gettype($arr)
                    );
        }
    }

    // }}} convertArray
    // {{{ convertArrayToProperties

    /**
     * Converts a PHP array into a JS object
     * supports of multu-dimensional array.
     * Keeps keys as they are (associative arrays).
     *
     * @access public
     * @param  string  $arr     the array to convert
     * @param  string  $varname the variable name to declare
     * @param  boolean $new     if true, the JS var will be set
     * @return mixed   a PEAR_Error or the converted array
     */
    function convertArrayToProperties( $array, $varname, $global=false, $new=true )
    {
        if(is_array($array)){
            $cnt = sizeof($array)-1;
            $i  = 0;
            $convert = $global?'var ':'';
            $convert .= $new?$varname.'={'.HTML_JAVASCRIPT_NL:'{';
            foreach( $array as $key=>$val) {
                $key = $key?'"'.$key.'"':"'0'";
                $convert .= $key.':';
                if(is_array($val)){
                    $convert .= HTML_Javascript_Convert::convertArrayToProperties($val,'',false, false);
                    $convert .= $i++<$cnt?','.HTML_JAVASCRIPT_NL:'';
                } else {
                    $convert .= HTML_Javascript_Convert::convertValue($val);
                    $convert .= $i++<$cnt?',':'';
                }
            }
            $convert .= HTML_JAVASCRIPT_NL.'}';
        }
        if($new){
            $convert .= ';';
        }
        return $convert;
    }

    // }}} convertArrayToProperties
    // {{{convertValue

    /**
     * Converts a variable value to its javascript equivalent.
     * String variables are escaped (see {@link escapeString()}).
     *
     * @param  string  $param coment
     * @access public
     * @return mixed return
     */
    function convertValue( $val )
    {
        switch ( gettype($val) ) {
            case 'boolean':
                return $val ? 'true' : 'false';
                
            case 'integer':
            case 'double':
                return $val;
                
            case 'string':
                return "'".HTML_Javascript_Convert::escapeString($val)."'";
                
            case 'array':
                return HTML_Javascript_Convert::convertArray(
                            $val, $varname, $global
                        );
                        
            case 'NULL':
                return 'null';
                
            default:
                return HTML_Javascript_Convert::raiseError(
                        HTML_JAVASCRIPT_ERROR_CONVERT_INVVAR, __FUNCTION__.':'.gettype($val)
                    );
        }
    }

    // }}} convertValue

}
