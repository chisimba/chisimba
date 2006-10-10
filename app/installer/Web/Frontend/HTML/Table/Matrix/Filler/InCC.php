<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
require_once 'HTML/Table/Matrix/Filler.php';

/**
 * Fill inwards, clockwise.
 *
 * @author Arpad Ray <arpad@rajeczy.com>
 * @package HTML_Table_Matrix
 */
class HTML_Table_Matrix_Filler_InCC extends HTML_Table_Matrix_Filler {

    /**
     * Number of columns to move towards the right for the next cell
     *
     * @type int
     */
    var $_right = 0;

    /**
     * Number of rows to move downwards for the next cell
     *
     * @type int
     */
    var $_down = 1;

    /**
     * Number of cells inwards for the current revolution
     *
     * @type int
     */
    var $_in = 0;
    
    /**
     * Constructor
     *
     * @param Object $matrix Reference to the HTML_Table_Matrix instance we are
     *                       filling data for.
     * @param array $options Options for this Filler
     * @return void
     */
    function HTML_Table_Matrix_Filler_InCC(&$matrix, $options = false) {
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
            $this->row += $this->_down;
            $this->col += $this->_right;
            if ($this->row == $this->matrix->_rows - $this->_in - 1) {
                // last row
                if ($this->col == $this->matrix->_cols - $this->_in - 1) {
                    // last column
                    $this->_right = 0;
                    $this->_down = -1;
                } else if ($this->col == $this->matrix->_fillStartCol + $this->_in) {
                    // first column
                    $this->_right = 1;
                    $this->_down = 0;
                }
            } else if ($this->row == $this->matrix->_fillStartRow + $this->_in
                 && $this->col == $this->matrix->_cols - $this->_in - 1
                 && $this->col != $this->matrix->_fillStartCol + $this->_in) {
                // first row, last column, revolution width != 1
                $this->_right = -1;
                $this->_down = 0;
            } else if ($this->col + $this->_right == $this->matrix->_fillStartCol + $this->_in
                 && $this->row + $this->_down == $this->matrix->_fillStartRow + $this->_in) {
                // next cell would be the starting
                $this->_in++;
                $this->_right = 0;
                $this->_down = 1;
            }
        }
        return array($this->row, $this->col);
    }
}
?>
