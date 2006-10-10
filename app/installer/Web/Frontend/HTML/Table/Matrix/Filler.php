<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Ian Eure <ieure@php.net>                                    |
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * Base class common to all Fillers
 *
 * @author Ian Eure <ieure@php.net>
 * @package HTML_Table_Matrix
 * @since 1.0
 */
class HTML_Table_Matrix_Filler {
    /**
     * Default filler options
     *
     * @type array
     * @access protected
     * @see setOptions()
     */
    var $_defaultOptions = array();
    
    /**
     * Filler options
     *
     * @type array
     * @see setOptions()
     */
    var $options = '';
    
    /**
     * Reference to the HTML_Table_Matrix instance we will be Filling for
     *
     * @type object
     */
    var $matrix = '';
    
    /**
     * Current row to fill
     *
     * @type int
     */
    var $row = 0;
    
    /**
     * Current column to fill
     *
     * @type int
     */
    var $col = 0;

    /**
     * Callback function applied to _data
     *
     * @type mixed
     */
    var $callback;

    /**
     * Create an instance of a Filler
     *
     * @param string $type Type of filler to instantiate
     * @param Object $matrix Reference to the HTML_Table_Matrix instance
     *                       the Filler will work with
     * @param array $options Filler options
     * @return mixed Filler instance on success, PEAR_Error otherwise
     */
    function &factory($type, &$matrix, $options = array())
    {
        $class = 'HTML_Table_Matrix_Filler_'.$type;
        $file = str_replace('_', '/', $class).'.php';
        @include_once $file;
        if (!class_exists($class)) {
            return PEAR::raiseError("Filler \"$type\" does not exist.");
        }
        $instance = new $class($matrix, $options);
        return $instance;
    }
    
    /**
     * Set options for this Filler
     *
     * @param array $options Options to set
     * @return void
     */
    function setOptions($options = array()) {
        $opts = array_merge($this->_defaultOptions, $options);
        $this->options = $opts;
    }

    /**
     * Determine if a given object is a valid H_T_M Filler
     *
     * @param mixed $object Object to check
     * @return boolean true if valid, false otherwise
     */
    function isValid(&$object)
    {
        if (!is_a($object, 'HTML_Table_Matrix_Filler')) {
            return false;
        }
        return true;
    }

    /**
     * Get the next cell.
     *
     * @param int $index Where we're at in the data-set
     * @return array 1-dimensional array in the form of (row, col) containing the
     *               coordinates to put the data for this loop iteration
     */
    function next($index)
    {
        return PEAR::raiseError("Function not implemented.");
    }
}
?>
