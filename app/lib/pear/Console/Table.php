<?php
// +-----------------------------------------------------------------------+
// | Copyright (c) 2002-2003 Richard Heyes                                 |
// | All rights reserved.                                                  |
// |                                                                       |
// | Redistribution and use in source and binary forms, with or without    |
// | modification, are permitted provided that the following conditions    |
// | are met:                                                              |
// |                                                                       |
// | o Redistributions of source code must retain the above copyright      |
// |   notice, this list of conditions and the following disclaimer.       |
// | o Redistributions in binary form must reproduce the above copyright   |
// |   notice, this list of conditions and the following disclaimer in the |
// |   documentation and/or other materials provided with the distribution.|
// | o The names of the authors may not be used to endorse or promote      |
// |   products derived from this software without specific prior written  |
// |   permission.                                                         |
// |                                                                       |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
// |                                                                       |
// +-----------------------------------------------------------------------+
// | Author: Richard Heyes <richard@phpguru.org>                           |
// |         Jan Schneider <jan@horde.org>                                 |
// +-----------------------------------------------------------------------+
//
// $Id$
//
// Utility for printing tables from cmdline scripts
//

define('CONSOLE_TABLE_HORIZONTAL_RULE', 1);
define('CONSOLE_TABLE_ALIGN_LEFT', -1);
define('CONSOLE_TABLE_ALIGN_CENTER', 0);
define('CONSOLE_TABLE_ALIGN_RIGHT', 1);

class Console_Table
{
    /**
     * The table headers.
     *
     * @var array
     */
    var $_headers = array();

    /**
     * The data of the table.
     *
     * @var array
     */
    var $_data = array();

    /**
     * The max number of columns in a row.
     *
     * @var integer
     */
    var $_max_cols = 0;

    /**
     * The max number of rows in the table.
     *
     * @var integer
     */
    var $_max_rows = 0;

    /**
     * Lengths of the columns, calculated when rows are added to the table.
     *
     * @var array
     */
    var $_cell_lengths = array();

    /**
     * Heights of the rows.
     *
     * @var array
     */
    var $_row_heights = array();

    /**
     * How many spaces to use to pad the table.
     *
     * @var integer
     */
    var $_padding = 1;

    /**
     * Column filters.
     *
     * @var array
     */
    var $_filters = array();

    /**
     * Columns to calculate totals for.
     *
     * @var array
     */
    var $_calculateTotals;

    /**
     * Alignment of the columns.
     *
     * @var array
     */
    var $_col_align = array();

    /**
     * Default alignment of columns.
     *
     * @var integer
     */
    var $_defaultAlign;

    /**
     * Charset of the data.
     *
     * @var string
     */
    var $_charset = 'utf-8';

    /**
     * Constructor.
     *
     * @param integer $align  Default alignment
     */
    function Console_Table($align = CONSOLE_TABLE_ALIGN_LEFT)
    {
        $this->_defaultAlign = $align;
    }

    /**
     * Converts an array to a table. Must be 
     *
     * @static
     *
     * @param array $headers         Headers for the table.
     * @param array $data            A two dimensional array with the table
     *                               data.
     * @param boolean $returnObject  Whether to return the Console_Table object
     *                               instead of the rendered table.
     *
     * @return Console_Table|string  A Console_Table object or the generated
     *                               table.
     */
    function fromArray($headers, $data, $returnObject = false)
    {
        if (!is_array($headers) || !is_array($data)) {
            return false;
        }

        $table = &new Console_Table();
        $table->setHeaders($headers);

        foreach ($data as $row) {
            $table->addRow($row);
        }

        return $returnObject ? $table : $table->getTable();
    }

    /**
     * Adds a filter to a column.
     *
     * Filters are standard PHP callbacks which are run on the data before
     * table generation is performed. Filters are applied in the order they
     * are added. The callback function must accept a single argument, which
     * is a single table cell.
     *
     * @param integer $col     Column to apply filter to.
     * @param mixed $callback  PHP callback to apply.
     */
    function addFilter($col, &$callback)
    {
        $this->_filters[] = array($col, &$callback);
    }

    /**
     * Sets the charset of the provided table data.
     *
     * @string $charset  A charset supported by the mbstring PHP extension.
     */
    function setCharset($charset)
    {
        $this->_charset = strtolower($charset);
    }

    /**
     * Sets the alignment for the columns.
     *
     * @param integer $col_id  The column number.
     * @param integer $align   Alignment to set for this column. One of
     *                         CONSOLE_TABLE_ALIGN_LEFT
     *                         CONSOLE_TABLE_ALIGN_CENTER
     *                         CONSOLE_TABLE_ALIGN_RIGHT.
     */
    function setAlign($col_id, $align = CONSOLE_TABLE_ALIGN_LEFT)
    {
        switch ($align) {
            case CONSOLE_TABLE_ALIGN_CENTER:
                $pad = STR_PAD_BOTH;
                break;
            case CONSOLE_TABLE_ALIGN_RIGHT:
                $pad = STR_PAD_LEFT;
                break;
            default:
                $pad = STR_PAD_RIGHT;
                break;
        }
        $this->_col_align[$col_id] = $pad;
    }

    /**
     * Specifies which columns are to have totals calculated for them and
     * added as a new row at the bottom.
     *
     * @param array $cols  Array of column numbers (starting with 0).
     */
    function calculateTotalsFor($cols)
    {
        $this->_calculateTotals = $cols;
    }

    /**
     * Sets the headers for the columns.
     *
     * @param array $headers  The column headers.
     */
    function setHeaders($headers)
    {
        $this->_headers = array(array_values($headers));
        $this->_updateRowsCols($headers);
    }

    /**
     * Adds a row to the table.
     *
     * @param array $row       The row data to add.
     * @param boolean $append  Whether to append or prepend the row.
    */
    function addRow($row, $append = true)
    {
        if ($append) {
            $this->_data[] = array_values($row);
        } else {
            array_unshift($this->_data, array_values($row));
        }

        $this->_updateRowsCols($row);
    }

    /**
     * Inserts a row after a given row number in the table.
     *
     * If $row_id is not given it will prepend the row.
     *
     * @param array $row       The data to insert.
     * @param integer $row_id  Row number to insert before.
     */
    function insertRow($row, $row_id = 0)
    {
        array_splice($this->_data, $row_id, 0, array($row));

        $this->_updateRowsCols($row);
    }

    /**
     * Adds a column to the table.
     *
     * @param array $col_data  The data of the column.
     * @param integer $col_id  The column index to populate.
     * @param integer $row_id  If starting row is not zero, specify it here.
     */
    function addCol($col_data, $col_id = 0, $row_id = 0)
    {
        foreach ($col_data as $col_cell) {
            $this->_data[$row_id++][$col_id] = $col_cell;
        }

        $this->_updateRowsCols();
        $this->_max_cols = max($this->_max_cols, $col_id + 1);
    }

    /**
     * Adds data to the table.
     *
     * @param array $data      A two dimensional array with the table data.
     * @param integer $col_id  Starting column number.
     * @param integer $row_id  Starting row number.
     */
    function addData($data, $col_id = 0, $row_id = 0)
    {
        foreach ($data as $row) {
            if ($row === CONSOLE_TABLE_HORIZONTAL_RULE) {
                $this->_data[$row_id] = CONSOLE_TABLE_HORIZONTAL_RULE;
                $row_id++;
                continue;
            }
            $starting_col = $col_id;
            foreach ($row as $cell) {
                $this->_data[$row_id][$starting_col++] = $cell;
            }
            $this->_updateRowsCols();
            $this->_max_cols = max($this->_max_cols, $starting_col);
            $row_id++;
        }
    }

    /**
     * Adds a horizontal seperator to the table.
     */
    function addSeparator()
    {
        $this->_data[] = CONSOLE_TABLE_HORIZONTAL_RULE;
    }

    /*
     * Returns the table in wonderful ASCII art.
     *
     * @return string  The generated table.
     */
    function getTable()
    {
        $this->_applyFilters();
        $this->_calculateTotals();
        $this->_validateTable();

        return $this->_buildTable();
    }

    /**
     * Calculates totals for columns.
     */
    function _calculateTotals()
    {
        if (!empty($this->_calculateTotals)) {
            $this->addSeparator();

            $totals = array();
            foreach ($this->_data as $row) {
                if (is_array($row)) {
                    foreach ($this->_calculateTotals as $columnID) {
                        $totals[$columnID] += $row[$columnID];
                    }
                }
            }

            $this->_data[] = $totals;
            $this->_updateRowsCols();
        }
    }

    /**
     * Applies any column filters to the data.
     */
    function _applyFilters()
    {
        if (!empty($this->_filters)) {
            foreach ($this->_filters as $filter) {
                $column   = $filter[0];
                $callback = $filter[1];

                foreach ($this->_data as $row_id => $row_data) {
                    if ($row_data !== CONSOLE_TABLE_HORIZONTAL_RULE) {
                        $this->_data[$row_id][$column] = call_user_func($callback, $row_data[$column]);
                    }
                }
            }
        }
    }

    /**
     * Ensures that column and row counts are correct.
     */
    function _validateTable()
    {
        if (!empty($this->_headers)) {
            $this->_calculateRowHeight(-1, $this->_headers[0]);
        }

        for ($i = 0; $i < $this->_max_rows; $i++) {
            for ($j = 0; $j < $this->_max_cols; $j++) {
                if (!isset($this->_data[$i][$j]) &&
                    (!isset($this->_data[$i]) ||
                     $this->_data[$i] !== CONSOLE_TABLE_HORIZONTAL_RULE)) {
                    $this->_data[$i][$j] = '';
                }

            }
            $this->_calculateRowHeight($i, $this->_data[$i]);

            if ($this->_data[$i] !== CONSOLE_TABLE_HORIZONTAL_RULE) {
                 ksort($this->_data[$i]);
            }

        }

        $this->_splitMultilineRows();

        // Update cell lengths.
        for ($i = 0; $i < count($this->_headers); $i++) {
            $this->_calculateCellLengths($this->_headers[$i]);
        }
        for ($i = 0; $i < $this->_max_rows; $i++) {
            $this->_calculateCellLengths($this->_data[$i]);
        }

        ksort($this->_data);
    }

    /**
     * Splits multiline rows into many smaller one-line rows.
     */
    function _splitMultilineRows()
    {
        ksort($this->_data);
        $sections = array(&$this->_headers, &$this->_data);
        $max_rows = array(count($this->_headers), $this->_max_rows);
        $row_height_offset = array(-1, 0);

        for ($s = 0; $s <= 1; $s++) {
            $inserted = 0;
            $new_data = $sections[$s];

            for ($i = 0; $i < $max_rows[$s]; $i++) {
                // Process only rows that have many lines.
                if (($height = $this->_row_heights[$i + $row_height_offset[$s]]) > 1) {
                    // Split column data into one-liners.
                    $split = array();
                    for ($j = 0; $j < $this->_max_cols; $j++) {
                        $split[$j] = preg_split('/\r?\n|\r/', $sections[$s][$i][$j]);
                    }

                    $new_rows = array();
                    // Construct new 'virtual' rows - insert empty strings for
                    // columns that have less lines that the highest one.
                    for ($i2 = 0; $i2 < $height; $i2++) {
                        for ($j = 0; $j < $this->_max_cols; $j++) {
                            $new_rows[$i2][$j] = !empty($split[$j][$i2]) ? $split[$j][$i2] : '';
                        }
                    }

                    // Replace current row with smaller rows.  $inserted is
                    // used to take account of bigger array because of already
                    // inserted rows.
                    array_splice($new_data, $i + $inserted, 1, $new_rows);
                    $inserted += count($new_rows) - 1;
                }
            }

            // Has the data been modified?
            if ($inserted > 0) {
                $sections[$s] = $new_data;
                $this->_updateRowsCols();
            }
        }
    }

    /**
     * Builds the table.
     */
    function _buildTable()
    {
        $return = array();
        for ($i = 0; $i < count($this->_data); $i++) {
            for ($j = 0; $j < count($this->_data[$i]); $j++) {
                if ($this->_data[$i] !== CONSOLE_TABLE_HORIZONTAL_RULE &&
                    $this->_strlen($this->_data[$i][$j]) < $this->_cell_lengths[$j]) {
                    $this->_data[$i][$j] = str_pad($this->_data[$i][$j],
                                            $this->_cell_lengths[$j],
                                            ' ',
                                            $this->_col_align[$j]);
                }
            }

            if ($this->_data[$i] !== CONSOLE_TABLE_HORIZONTAL_RULE) {
                $row_begin    = '|' . str_repeat(' ', $this->_padding);
                $row_end      = str_repeat(' ', $this->_padding) . '|';
                $implode_char = str_repeat(' ', $this->_padding) . '|' .
                    str_repeat(' ', $this->_padding);
                $return[] = $row_begin . implode($implode_char, $this->_data[$i]) .
                    $row_end;
            } else {
                $return[] = $this->_getSeparator();
            }

        }

        $return = $this->_getSeparator() . "\r\n" . implode("\n", $return) .
            "\r\n" . $this->_getSeparator() . "\r\n";

        if (!empty($this->_headers)) {
            $return = $this->_getHeaderLine() .  "\r\n" . $return;
        }

        return $return;
    }

    /**
     * Creates a horizontal separator for header separation and table
     * start/end etc.
     */
    function _getSeparator()
    {
        foreach ($this->_cell_lengths as $cl) {
            $return[] = str_repeat('-', $cl);
        }

        $row_begin    = '+' . str_repeat('-', $this->_padding);
        $row_end      = str_repeat('-', $this->_padding) . '+';
        $implode_char = str_repeat('-', $this->_padding) . '+' .
            str_repeat('-', $this->_padding);

        return $row_begin . implode($implode_char, $return) . $row_end;
    }

    /**
     * Returns header line for the table.
     */
    function _getHeaderLine()
    {
        // Make sure column count is correct
        for ($j = 0; $j < count($this->_headers); $j++) {
            for ($i = 0; $i < $this->_max_cols; $i++) {
                if (!isset($this->_headers[$j][$i])) {
                    $this->_headers[$j][$i] = '';
                }
            }
        }

        for ($j = 0; $j < count($this->_headers); $j++) {
            for ($i = 0; $i < count($this->_headers[$j]); $i++) {
                if ($this->_strlen($this->_headers[$j][$i]) < $this->_cell_lengths[$i]) {
                    $this->_headers[$j][$i] = str_pad($this->_headers[$j][$i],
                                                      $this->_cell_lengths[$i],
                                                      ' ',
                                                      $this->_col_align[$i]);
                }
            }
        }

        $row_begin    = '|' . str_repeat(' ', $this->_padding);
        $row_end      = str_repeat(' ', $this->_padding) . '|';
        $implode_char = str_repeat(' ', $this->_padding) . '|' .
            str_repeat(' ', $this->_padding);

        $return[] = $this->_getSeparator();
        for ($j = 0; $j < count($this->_headers); $j++) {
            $return[] = $row_begin .
                implode($implode_char, $this->_headers[$j]) .
                $row_end;
        }

        return implode("\r\n", $return);
    }

    /**
     * Update max cols/rows.
     */
    function _updateRowsCols($rowdata = null)
    {
        // Update max cols
        $this->_max_cols = max($this->_max_cols, count($rowdata));

        // Update max rows
        ksort($this->_data);
        $keys = array_keys($this->_data);
        $this->_max_rows = end($keys) + 1;

        switch ($this->_defaultAlign) {
            case CONSOLE_TABLE_ALIGN_CENTER: $pad = STR_PAD_BOTH; break;
            case CONSOLE_TABLE_ALIGN_RIGHT:  $pad = STR_PAD_LEFT; break;
            default:                         $pad = STR_PAD_RIGHT; break;
        }

        // Set default column alignments
        for ($i = count($this->_col_align); $i < $this->_max_cols; $i++) {
            $this->_col_align[$i] = $pad;
        }
    }

    /**
     * This function given a row of data will calculate the max length for
     * each column and store it in the _cell_lengths array.
     *
     * @param array $row  The row data.
     */
    function _calculateCellLengths($row)
    {
        for ($i = 0; $i < count($row); $i++) {
            if (!isset($this->_cell_lengths[$i])) {
                $this->_cell_lengths[$i] = 0;
            }
            $this->_cell_lengths[$i] = max($this->_cell_lengths[$i],
                                           $this->_strlen($row[$i]));
        }
    }

    /**
     * This function given a row of data will calculate the max height for all
     * columns and store it in the _row_heights array.
     *
     * @param integer $row_number  The row number.
     * @param array $row           The row data.
     */
    function _calculateRowHeight($row_number, $row)
    {
        if (!isset($this->_row_heights[$row_number])) {
            $this->_row_heights[$row_number] = 1;
        }

        // Do not process horizontal rule rows.
        if ($row === CONSOLE_TABLE_HORIZONTAL_RULE) {
            return;
        }

        for ($i = 0, $c = count($row); $i < $c; ++$i) {
            $lines = preg_split('/\r?\n|\r/', $row[$i]);
            $this->_row_heights[$row_number] = max($this->_row_heights[$row_number],
                                                   count($lines));
        }
    }

    /**
     * Returns the character length of a string.
     *
     * @param string $str  A multibyte or singlebyte string.
     *
     * @return integer  The string length.
     */
    function _strlen($str)
    {
        static $mbstring, $utf8;

        // Cache expensive function_exists() calls.
        if (!isset($mbstring)) {
            $mbstring = function_exists('mb_strlen');
        }
        if (!isset($utf8)) {
            $utf8 = function_exists('utf8_decode');
        }

        if ($utf8 &&
            ($this->_charset == strtolower('utf-8') ||
             $this->_charset == strtolower('utf8'))) {
            return strlen(utf8_decode($str));
        }
        if ($mbstring) {
            return mb_strlen($str, $this->_charset);
        }

        return strlen($str);
    }

}
