<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML interface class
require_once("ifhtml_class_inc.php");

/**
* HTML TABLE class for outputting HTML tables. The HTML TABLE
* class can be used to build up complex tables by simply setting parameters
* then passing the data needed to build the table.
*
* Developers: If you add features to this class
*   it it crucial that it work hand in hand with the CSS
*   stylesheet that makes up the skin. Avoid putting styles
*   in the table or any other HTML object.
*
* @package htmlelements
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version $Id$;
* @author Derek Keats
* @example
*        $myTable=$this->newObject('htmltable','htmlelements');
*        $myTable->width='60%';
*        $myTable->border='1';
*        $myTable->cellspacing='1';
*        $myTable->cellpadding='10';
*
*        $myTable->startHeaderRow();
*        $myTable->addHeaderCell('header1');
*        $myTable->addHeaderCell('header2');
*        $myTable->endHeaderRow();
*
*        $myTable->startRow();
*        $myTable->addCell('cell1');
*        $myTable->addCell('cell2');
*        $myTable->endRow();
*
*        echo $myTable->show();
*
* @todo Implement --> Shulam..you should add the method you ddeveloped to this class,
*    but it needs to use some of the capabilities of this class.
*/
class htmlTable extends object implements ifhtml
{
    /**
    *
    * @var string $id: the ID tag from the CSS
    */
    public $id;
    /**
    *
    * @var stiong $caption: The table caption, uses styles
    * defined for caption in the CSS
    */
    public $caption;
    /**
    *
    * @var sting $heading: The table heading
    * @todo -c"htmlTable" Implement htmlTable heading and footing in building the output
    */
    public $heading;
    /**
    *
    * @var sting $footing: The table footing if used
    * @todo -c"htmlTable" Implement htmlTable heading and footing in building the output
    */
    public $footing;
    /**
    *
    * @var sring $width: The width of the table
    */
    public $width;
    /**
    *
    * @var int $border: The width of the table border. Use NULL to override.
    * @todo -c"htmlTable" Implement htmlTable change border to override if NULL
    * and use the values from the CSS class
    */
    public $border=0;
    /**
    *
    * @var int cellpadding: the cell padding to use
    */
    public $cellpadding=0;
    /**
    *
    * @var int cellspacing: the cell spacing to use
    */
    public $cellspacing=0;
    /**
    *
    * @var string $css_class: the class from the style sheet to use
    * Note: Do not confuse with internal variables with the same name
    * @todo -c"htmlTable" Implement htmlTable Change internal css_class
    * to another variable and remove this note.
    */
    public $css_class;
    public $cssClass;
    /**
    *
    * @var string $attributes: allows passing any attributes to the table
    */
    public $attributes;
    /**
    *
    * @var string $tr_start: The TR start tag, used to build up the table row tag.
    */
    public $tr_start;
    /**
    *
    * @var sring $row_tag: the row tag for the table. Used in add row or add header
    * to inplement TH for header cell and TD for normal cell
    */
    public $row_tag;
    /**
    *
    * @var string $primary_key: The primary key of a table being
    * passed to arrayToTable so that the edit links can be inserted
    * @see arrayToTable
    * @todo -c"htmlTable" Implement htmlTable Add add|edit|delete to arrayToTable
    */
    public $primary_key;
    /**
    *
    * @var string $cell_attributes: Allows the passing of cell attributes
    * to the table Cells in a row
    */
    public $cell_attributes=Null;
    /**
    *
    * @var string $row_attributes: Allows the passing of row attributes
    * to be used in the TR tag
    */
    public $row_attributes;
    /**
    *
    * @var boolean $alternate_row_colors: TRUE | FALSE whether to implement
    * alternating row colors in the table. Note the US spelling of colour as
    * color as is the case in HTML.
    */
    public $alternate_row_colors;
    /**
    *
    * @var boolean $active_rows: TRUE|FALSE - Whether to implement active rows
    */
    public $active_rows;
    /**
    *
    * @var string $content: The content of the table to be rendered
    * on execution of the show method
    */
    public $content;
    /**
    *
    * @var int $rows: The number of rows a table has
    */
    public $rows;
    /**
    *
    * @var int $cols: The number of columns in a table
    */
    public $cols;
    /**
    *
    * @var string $tdClasses: css associated with the <td>
    */
    public $tdClass;
    /**
    *
    * @var string $tdClasses: css associated with the <tr>
    */
    public $trClass;
    /**
    *
    * @var int $attrs: attributes of the <tr>,<td>
    */
    public $attrs;
    /**
    *
    * @var string $summary: Specifies a summary of the table for speech-synthesizing/non-visual browsers
    */
    public $summary;

    /**
    * Constructor to establish the default values for the
    * table properties
    */
    public function init()
    {
        $this->id = null;
        $this->caption = null;
        $this->content = null;
        $this->heading = null;
        $this->footing = null;
        $this->width = "99%";
        $this->border = 0;
        $this->cellpadding = 0;
        $this->cellspacing = 0;
        $this->css_class = null;
        $this->attributes = null;
        $this->tr_start = "<tr>\n";
        $this->head = null;
        $this->rows = 1;
        $this->cols = 1;
        $this->tdClasses = null;
        $this->trClasses = null;
        $this->attrs = null;
        $this->summary = null;
    }

    /**
    * Alternate constructor for including the file. eg. from radio buttons class
    */
    public function htmltable()
    {
        return $this->init();
    }

    /**
    * Method to add a row to the table (uses corresponding internal method)
    *
    * @param array $content : The array of cell entries for the table
    * @param string $cssClass : optional CSS class from the skin (normally odd, even, heading)
    * @param string $row_attributes : any additional attributes that you want to pass to the TD tag
    */
    public function addRow($content, $tdClass = null, $row_attributes = null)
    {
        if ($this->_validateContent($content)) {
            $this->content .= $this->_addRow($content, null, $tdClass, $row_attributes);
        }
    }

    /**
    * Method to add a header row to a table
    *
    * @param array $content : The array of cell entries for the table
    * @param string $row_attributes : any additional attributes that you want to pass to the TD tag
    */
    public function addHeader($content, $tdClass=null, $row_attributes = null, $trClass = null)
    {
        if ($this->_validateContent($content)) {
            // adds the header row to the top of the table
            $this->heading = $this->_addHeaderRow($content, $trClass, $tdClass, $row_attributes);
        }
    }

    /**
    * Method to add a summary to a table
    *
    * @param string $summary : summary description for tables
    */
    public function addSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
    * Method to build a table from an indexed array. It creates a header
    * from the array keys if no header exists already
    *
    * @param array $ar : the array to be processed
    */
    public function arrayToTable($ar, $limit = null)
    {
        $rowcount = 0; // initialize the odd/even counter
        $oddOrEven = "even";
        if (!is_array($ar)) {
            die("Data supplied to table is not an array");
        }
        foreach ($ar as $line) { // there must be a better way to do this...
            if ($this->alternate_row_colors==TRUE) {
                $oddOrEven = ($rowcount == 0) ? "odd" : "even";
            } else {
                $oddOrEven = null;
            }
            // If a table heading has not been defined
            if (!$this->heading) {
                $this->addHeader(array_keys($line));
            }
            $tableRow = array();
            foreach($line as $value) {
                if (empty($value)) {
                    $tableRow[] = '&nbsp;';
                } elseif ($limit) {
                    $tableRow[] = substr($value, 0, $limit);
                } else {
                    $tableRow[] = $value;
                }
            }
            $this->addRow($tableRow, $oddOrEven,
                $this->cell_attributes);
            $rowcount = ($rowcount == 0) ? 1 : 0;
        }
    }

    /**
    * Method to start a row
    */
    public function startRow($class = NULL)
    {
        $this->content .= "<tr";
		if (!is_null($class)) {
		    $this->content .= " class=\"" . $class . "\"";
		}
		else if ($this->trClass) {
            $this->content .= " class=\"" . $this->trClass . "\"";
        }
        if ($this->row_attributes) {
            $this->content .= " " . $this->row_attributes;
        }
        $this->content .= ">";
    }

    /**
    * Method to start a Header row: <thead>
    */
    public function startHeaderRow()
    {
        $this->heading .= "<thead";
        if ($this->row_attributes) {
            $this->heading .= " " . $this->row_attributes;
        }
        $this->heading .= "><tr>";
    }


    /**
    * Method to add a cell
    */
    public function addCell($str, $width=null, $valign="top", $align=null, $class=null, $attrib=Null,$border = '0')
    {
        $this->content .= '<td';
        if ($width) {
            $this->content .= ' width="'.$width.'"';
        }
	
        if ($border) {
           $this->content .= ' border="'.$border.'"';
       }
        if ($valign) {
            $this->content .= ' valign="'.$valign.'"';
        }
        if ($align) {
            $this->content .= ' align="'.$align.'"';
        }
        if ($class) {
           $this->content .= " class=\"" . $class . "\"";
        }
        if ($attrib) {
            $this->content .= ' '.$attrib;
        }
        $this->content .= ">".$str."</td>\n\n";
    }

    /**
    * Method to add a header cell
    */
    public function addHeaderCell($str, $width=null, $valign="top", $align='left', $class=null, $attrib=Null)
    {
        $this->heading .= '<th';
        if ($width) {
            $this->heading .= ' width="'.$width.'"';
        }
        if ($valign) {
            $this->heading .= ' valign="'.$valign.'"';
        }
        if ($align) {
            $this->heading .= ' align="'.$align.'"';
        }
        if ($class) {
           $this->heading .= " class=\"" . $class . "\"";
        }
        if ($attrib) {
            $this->heading .= ' '.$attrib;
        }
        if (trim($str) == '') {
            $str = '&nbsp;';
        }
        $this->heading .= ">".$str."</th>\n\n";
    }


    /**
    * Method to end a row
    */
    public function endRow()
    {
        $this->content .= "</tr>";
    }

    /**
    * Method to end a row
    */
    public function endHeaderRow()
    {
        $this->heading .= "</tr></thead>";
    }


    /**
    * Method to return the completed table for rendering
    */
    public function show()
    {
        $ts = "\n\n<table";
        $ts .= " cellspacing=\"" . $this->cellspacing . "\"";
        $ts .= " cellpadding=\"" . $this->cellpadding . "\"";
        if ($this->id) {
            $ts .= " id=\"" . $this->id . "\"";
        }
        if ($this->width) {
            $ts .= " width=\"" . $this->width . "\"";
        }
        if ($this->border) {
            $ts .= " border=\"" . $this->border . "\"";
        }
        if ($this->summary) {
            $ts .= ' summary="'.$this->summary.'"';
        }
        if ($this->css_class) {
            $ts .= " class=\"" . $this->css_class . "\"";
        } //deprecated
        if ($this->cssClass) {
            $ts .= " class=\"" . $this->cssClass . "\"";
        }
        if ($this->attributes) {
            $ts .= ' '.$this->attributes;
        }

        $ts .= ">";
        if ($this->caption) {
            $ts .= "<caption>" . $this->caption . "</caption>";
        }
        if ($this->heading) {
            $ts .= $this->heading;
        }

        $ts .= $this->content . "</table>";
        return $ts;
    }

    /*------------------------- PRIVATE METHODS BELOW THIS LINE --------------*/

    /**
    * Internal method to add a row to the table
    *
    * @access private
    * @param array $content : The array of cell entries for the table
    * @param string $trClass : optional CSS class from the skin (normally odd, even, heading)
    * @param string $tdClass : optional CSS class from the skin (normally odd, even, heading)
    * @param string $row_attributes : any additional attributes that you want to pass to the TD tag
    * @return string $row: the formatted table body with the new row added
    *
    * ......PLEASE DO NOT MUCK ABOUT IN HERE...CONTACT DEREK FIRST
    *
    */
    private function _addRow($content, $trClass = null, $tdClass=NULL, $row_attributes = null)
    {
        if ($row_attributes) {
            $this->row_attributes=$row_attributes;
        }
        // The number of cols in the content array
        $cols = count($content);
        $this->tr_start = "<tr";
        if ($trClass) {
            $this->tr_start .= " class=\"" . $trClass . "\"";
        }
        if ($this->row_attributes) {
            $this->tr_start .= " " . $this->row_attributes;
        }
        if ($this->active_rows) {
            if ($trClass != "heading") {
                if (!$trClass) {
                    $trClass = "transparentbg"; // Default to transparent after mouseout
                }
                $this->tr_start .= " onMouseOver=\"this.className='mouseover'\" onMouseOut=\"this.className='"
                  . $trClass . "'\" style=\"CURSOR: hand\"";
            }
        }
        $this->tr_start .= ">";
        $row = $this->tr_start . "\n";
        for($idx = 0; $idx < $cols; $idx++) {
            $row .= "<td";
            if (!$this->active_rows) { // Can't use TD class and have active rows!!
                if ($tdClass) {
                    $row .= " class=\"" . $tdClass . "\" ";
                }
            }
            if ($this->cell_attributes) {
                $row .= " " . $this->cell_attributes;
            }
            $row .= ">";
            $row .= $content[$idx] . "</td>";
        }
        $row .= "</tr>\n\n\n";
        return $row;
    }

    /**
    * Internal method to add a Header row to the table
    *
    * @access private
    * @param array $content : The array of cell entries for the table
    * @param string $trClass : optional CSS class from the skin (normally odd, even, heading)
    * @param string $tdClass : optional CSS class from the skin (normally odd, even, heading)
    * @param string $row_attributes : any additional attributes that you want to pass to the TD tag
    * @return string $row: the formatted table body with the new row added
    */
    private function _addHeaderRow($content, $trClass = null, $tdClass=NULL, $row_attributes = null)
    {
        if ($row_attributes) {
            $this->row_attributes=$row_attributes;
        }
        // The number of cols in the content array
        $cols = count($content);
        $this->tr_start = "<thead";
        if ($trClass) {
            $this->tr_start .= " class=\"" . $trClass . "\"";
        }
        if ($this->row_attributes) {
            $this->tr_start .= " " . $this->row_attributes;
        }
        $this->tr_start .= "><tr>";
        $row = $this->tr_start . "\n";
        for($idx = 0; $idx < $cols; $idx++) {
            $row .= "<th";
            if (!$this->active_rows) { // Can't use TD class and have active rows!!
                if ($tdClass) {
                    $row .= " class=\"" . $tdClass . "\" ";
                }
            }
            if ($this->cell_attributes) {
                $row .= " " . $this->cell_attributes;
            }
            $row .= ">";
            $row .= $content[$idx] . "</th>";
        }
        $row .= "</tr></thead>\n\n\n";
        return $row;
    }

    /**
    * Method to validate content and die if there is no content
    *
    * @access private
    * @param string $content : The content that you wish to validate
    * @return true | false
    */
    private function _validateContent($ct)
    {
        if ($ct) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Method to return the keys of an indexed array
    *
    * @access private
    * @param array $ar : the array to parse
    * @return array return: a simple array of keys for use to build table header
    */
    private function _getArrayKeys($ar)
    {
        foreach ($ar as $line) { // This is the best I can do, there must be a better way
            return array_keys($line);
        }
    }
}

?>