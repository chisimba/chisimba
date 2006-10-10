<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Main HTML_Table_Matrix class
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @package    HTML_Table_Matrix
 * @author     Ian Eure <ieure@php.net>
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @copyright  (c) 2003-2005 Ian Eure
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @link       http://pear.php.net/package/html_table_matrix/
 * @see        HTML_Table
 */

require_once 'PEAR.php';
require_once 'HTML/Table.php';
require_once 'HTML/Table/Matrix/Filler.php';

/**
 * Fills a HTML table with data.
 *
 * Simple usage:
 *
 * // This is the data to put in the table.
 * $data = array('one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight',
 *               'nine', 'ten');
 * $m = &new HTML_Table_Matrix;
 * $m->setData($data);
 * // Pick a filler class. We use the stock left-to-right-top-to-bottom here.
 * $f = &HTML_Table_Matrix_Filler::factory('LRTB');
 * $m->accept($f);
 * // Make the table 2 rows deep by 5 cols wide
 * $m->setTableSize(2, 5);
 * // Output the table.
 * print $m->toHtml();
 *
 *
 * @package    HTML_Table_Matrix
 * @category   HTML
 * @author     Ian Eure <ieure@php.net>
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @copyright  (c) 2003-2005 Ian Eure
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @link       http://pear.php.net/package/html_table_matrix/
 * @see        HTML_Table
 */
class HTML_Table_Matrix extends HTML_Table {

    /**
     * The filler
     *
     * @type object
     * @access private
     * @see accept()
     */
     var $_filler = '';

    /**
     * The row to start filling at. Useful if you want to put other stuff in
     * the table.
     *
     * @access private
     * @var int
     * @see setFillStart()
     */
    var $_fillStartRow = 0;

    /**
     * The column to start filling at. Useful if you want to put other stuff in
     * the table.
     *
     * @access private
     * @var int
     * @see setFillStart()
     */
    var $_fillStartCol = 0;

    /**
     * The number of rows in the table. 0 = Undefined.
     *
     * @access private
     * @var int
     * @see setTableSize()
     */
    var $_rows = 0;

    /**
     * The number of columns in the table. 0 = Undefined.
     *
     * @access private
     * @var int
     * @see setTableSize()
     */
    var $_cols = 10;

    /**
     * Has the table been filled?
     *
     * @access private
     * @var boolean
     */
    var $_isFilled = FALSE;

    /**
     * Data to fill table with
     *
     * @access private
     * @var array
     * @see setData()
     */
    var $_data = array();


    /**
     * Sets data to fill table with.
     *
     * @return void
     * @param array $data 1-dimensional array of matrix data
     */
    function setData(&$data)
    {
        $this->_data = $data;
    }

    /**
     * Set the row & column to start filling at.
     *
     * Defaults to (0,0), which is the upper-left corner of the table. Setting
     * this to a larger value will leave other cells empty, e.g. if you want to
     * add a header or other information in the table in addition to the matrix
     * data.
     *
     * @param int $row Row to start filling at
     * @param int $col Column to start filling at
     * @return void
     */
    function setFillStart($row, $col)
    {
        $this->_fillStartRow = $row;
        $this->_fillStartCol = $col;
    }

    /**
     * Set the size of the resulting table.
     *
     * The table will be forced to this size, regardless of whether or not
     * there is enough (or too much) data to fill it up. If the table size
     * (rows * cols) is smaller than the amount of data given to us, only
     * (rows * cols) items are laid out.
     *
     * @param int $rows Number of rows, or zero to auto-size.
     * @param int $cols Number of columns, or zero to auto-size.
     * @return void
     */
    function setTableSize($rows = 0, $cols = 0)
    {
        $this->_rows = $rows;
        $this->_cols = $cols;
    }

    /**
     * Return the total table size (w * h)
     *
     * @return int Table size
     * @access protected
     */
    function _getTableSize()
    {
        if ($this->_cols == 0 || $this->_rows == 0) {
            return count($this->_data);
        }
        return $this->_rows * $this->_cols;
    }

    /**
     * Accept a Filler
     */
    function accept(&$filler)
    {
        if (!HTML_Table_Matrix_Filler::isValid($filler)) {
            return PEAR::raiseError("Provided filler is of the wrong class.");
        }
        $this->_filler = $filler;
        return true;
    }

    /**
     * Calculates the size of the table based on the data provided.
     *
     * @access private
     * @return void
     * @see setData()
     */
    function _calculateSize()
    {
        reset($this->_data);
        $n = count($this->_data);

        if (!$this->_rows && $this->_cols) {
            $this->_rows = ceil($n / $this->_cols);
        } else if (!$this->_cols && $this->_rows) {
            $this->_cols = ceil($n / $this->_rows);
        }
    }

    /**
     * Fills table with provided data. RL & BT modes are not implemented yet.
     *
     * This function does the actual laying out of the data into the table.
     * It isn't necessary to call this unless you want to add or change something
     * in the table, as toHtml() calls this automatically if the table has not
     * yet been filled with data.
     *
     * @return mixed boolean true on success, PEAR_Error otherwise
     * @see setData()
     */
    function fillTable()
    {
        if (!HTML_Table_Matrix_Filler::isValid($this->_filler)) {
            return PEAR::raiseError("No Filler has been set.");
        }

        $this->_calculateSize();
        reset($this->_data);
        $size = $this->_getTableSize();
        $this->_data = array_slice($this->_data, 0, $size);
        if (isset($this->_filler->callback)) {
            if (!is_callable($this->_filler->callback)
                || !is_array($cr = call_user_func($this->_filler->callback, $this->_data))) {
                return PEAR::raiseError("Invalid filler callback.");
            }
            $this->_data = $cr;
        }
        for ($i = $index = 0; $i < $size; $i++, $index++) {
            list($row, $col) = $this->_filler->next($index);
            $this->_fillCell($row, $col);
        }

        $this->_isFilled = TRUE;
        return true;
    }

    /**
     * Fills a cell with data.
     *
     * Note: this depends on the array pointer of $_data pointing at the
     * right item. Possibly not be the best way to handle this.
     *
     * @access private
     * @param int $row Row of cell to fill.
     * @param int $col Column of cell to fill.
     */
    function _fillCell($row, $col)
    {
        list($null, $data) = each($this->_data);
        $this->setCellContents($row, $col, $data);
    }

    /**
     * Returns HTML table. Calls fillTable() if the table has not already
     * been filled.
     *
     * @return string HTML Table
     * @see HTML_Table::toHtml()
     */
    function toHtml()
    {
        if (!$this->_isFilled) {
            $this->fillTable();
        }

        return(parent::toHtml());
    }
}
?>
