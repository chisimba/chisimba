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

require_once 'HTML/Table/Matrix/Filler.php';

/**
 * Fill top-to-bottom, left-to-right.
 *
 * @author Ian Eure <ieure@php.net>
 * @package HTML_Table_Matrix
 * @since 1.0
 */
class HTML_Table_Matrix_Filler_TBLR extends HTML_Table_Matrix_Filler {
    /**
     * Constructor
     *
     * @param Object $matrix Reference to the HTML_Table_Matrix instance we are
     *                       filling data for.
     * @param array $options Options for this Filler
     * @return void
     */
    function HTML_Table_Matrix_Filler_TBLR(&$matrix, $options = false) {
        $this->setOptions($options);
        $this->matrix = $matrix;
    }

    /**
     * Get the next cell.
     *
     * @param int $index Where we're at in the data-set
     * @return array 1-dimensional array in the form of (row, col) containing the
     *               coordinates to put the data for this loop iteration
     */
    function next($index) {
        if ($index == 0) {
            $this->row = $this->matrix->_fillStartRow;
            $this->col = $this->matrix->_fillStartCol;
        } else {
            $this->row = ($this->row == $this->matrix->_rows) ? $this->matrix->_fillStartRow : $this->row + 1;
            $this->col = ($this->row == $this->matrix->_fillStartRow) ? $this->col + 1 : $this->col;
        }

        return array($this->row, $this->col);
    }
}
?>
