<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright  (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package    htmlelements
* @subpackage view
* @version    0.1
* @since      28 February 2005
* @author     Jonathan Abrahams
* @filesource
*/

/**
* Wrapper class for sorttable.js.
*
* Example of use:<code>
* $tblEntries = $this->newObject( 'sorttable','htmlelements' );
* $tblEntries->width = '100%';
* $tblEntries->cellpadding = 5;
* $tblEntries->cellspacing = 2;
*
* $tblEntries->startHeaderRow();
*    $tblEntries->addHeaderCell( 'HeaderCell1','5%','','right' );
*    $tblEntries->addHeaderCell( 'HeaderCell2' );
*    $tblEntries->addHeaderCell( 'HeaderCell3','10%');
* $tblEntries->endHeaderRow();
*
* $dbData = array( ... ); // Your table row data.
* $oddEven = 'odd';
* foreach( $dbData as $entry ) {
*    $tblEntries->row_attributes = "class=\"$oddEven\"";
*    $tblEntries->startRow();
*        $tblEntries->addCell($lnkComment->show());
*
*        // Sort your column by meaning data
*        $lnkEntry = $this->newObject( 'link', 'htmlelments' );
*        $lnkEntry->href = Your link stuff;
*        $lnkEntry->link = Your link text which get used for the sort;
*        $tblEntries->sortData = $lnkEntry->link;
*        $tblEntries->addCell($lnkEntry->show());
*
*        $tblEntries->addCell($lnkTopic->show());
*    $tblEntries->endRow();
*    $oddEven = $oddEven=='odd'?'even':'odd';
* }
* echo $tblEntries->show();
* </code>
* @author Jonathan Abrahams
*/
class sortTable extends object
{
    /**
    * @var string $name: The javascript table name
    */
    public $name;
    /**
    * @var string $arrHeader: The table header data.
    */
    public $arrHeader;
    /**
    * @var string $arrRow: The table data, for rows and cells (data and attributes)
    */
    public $arrRow;

    /**
    * @var string $row_attributes: Allows the passing of row attributes
    *             to be used in the TR tag
    */
    public $row_attributes;
    /**
    * @var string $currentRow: To keep track of the current row.
    */
    public $currentRow;
    /**
    * @var string $currentCell: To keep track of the current cell.
    */
    public $currentCell;
    /**
    * @var string $sortData: To data to use when sorting.
    */
    public $sortData;

    /**
    * Constructor to establish the default values for the
    * table properties
    */
    public function init()
    {
        // Insert the javascript into the header
        $this->appendArrayVar( 'headerParams', $this->getJavascriptFile('sorttable.js','htmlelements') );
        $this->objLanguage = $this->getObject('language', 'language');

        $this->currentRow = 0;
        $this->currentCell = 0;
        $this->sortData = '';
        $this->arrRow = array();
        $this->name = 't';
    }

    /**
    * Method to start a row
    */
    public function startRow()
    {
        // Get the current row attributes
        $this->arrRow[$this->currentRow]['row_attributes']=$this->row_attributes;
        // Start at the first cell for this row
        $this->currentCell=0;
    }

    /**
    * Method to start a Header row
    */
    public function startHeaderRow()
    {
        $this->arrHeader= array();
    }

    /**
    * Method to add a cell
    *
    * @param  string  Cell     string
    * @param  string  Width    cell attribute
    * @param  string  Vertical align cell attribute
    * @param  string  Align    cell attribute
    * @param  string  CSS      class cell attribute
    * @param  string  Extra    cell attributes
    * @return nothing
    */
    public function addCell($str, $width=NULL, $valign="top", $align=NULL, $class=NULL, $attrib=NULL)
    {
        // Add cell data and attributes for the current row and current cell..
        $this->arrRow[$this->currentRow]['cells'][$this->currentCell++] = array( 'str'=>$str, 'width'=>$width,'valign'=>$valign,'align'=>$align,
            'class'=>$class,'attrib'=>$attrib, 'sortData'=>$this->sortData );
        $this->sortData = '';
    }

    /**
    * Method to add a header cell
    *
    * @param  string  Header   cell string
    * @param  string  Width    header cell attribute
    * @param  string  Vertical align header cell attribute
    * @param  string  Align    header cell attribute
    * @param  string  CSS      class header cell attribute
    * @param  string  Extra    header cell attributes
    * @return nothing
    */
    public function addHeaderCell($str, $width=NULL, $valign="top", $align='left', $class=NULL, $attrib=NULL)
    {
        $this->arrHeader[]=array( 'str'=>$str, 'width'=>$width,'valign'=>$valign,'align'=>$align,
            'class'=>$class,'attrib'=>$attrib);
    }

    /**
    * Method to end a row
    */
    public function endRow()
    {
        $this->currentRow++;
    }

    /**
    * Method to end a row
    */
    public function endHeaderRow()
    {
    }

    /**
    * Method to return the completed table for rendering
    */
    public function show()
    {
        $sortTable = '<script language="JavaScript">';
        $sortTable.= $this->create();

        // Header
        foreach( $this->arrHeader as $headerCell ) {
            $name=$headerCell['str'];
            $td=$this->_getAttributes($headerCell);
            $align=$headerCell['align'];
            $type='';
            $sortTable.= $this->AddColumn($name,$td,$align,$type);
        }

        // Rows
        $row_attributes='';
        foreach( $this->arrRow as $row ) {
            $array = array();
            $row_attributes=$row['row_attributes'];
            foreach( $row['cells'] as $rowCell ) {
                // Row cell attributes are lost, and uses header cell attributesc
                $array['line'][] = $rowCell['str'];
                $array['sortData'][] = $rowCell['sortData'];
            }
            $sortTable.= $this->AddLine($array['line']);
            $sortTable.= $this->AddLineSortData($array['sortData']);
            $sortTable.= $this->AddLineProperties( $row_attributes );
        }
        $sortTable .='</script>';

        // Table definition stuff
        $tableAttributes = $this->_getTableAttributes();
        $sortTable .= sprintf('<table %s>', $tableAttributes );
        // Build header
        $th = '';
        // Index for javascript: Column index to sort by.
        $index = 0;
        foreach( $this->arrHeader as $headerCell ){
            $thAttr = $this->_getAttributes( $headerCell );
            $thData = $headerCell['str'];
            // TODO: Enable/Disable which columns to sort.
            $js = $this->SortRows( $this->name, $index++ );
            $th .= sprintf('<th %s><a href="javascript:%s">%s</a></th>',$thAttr,$js,$thData);
        }

        $sortTable .= sprintf('<thead><tr %s>%s</tr></thead>', $row_attributes, $th );
        // Build Body
        $sortTable .= sprintf('<tbody><script language="JavaScript" type="text/javascript">%s</script></tbody>', $this->WriteRows());
        $sortTable .= '</table>';
        return $sortTable;
    }

    //--- PRIVATE FUNCTION/WRAPPER ---//

    /**
    * Private method used to get the element attributes.
    *
    * @param  array  The attributes in an associative array
    * @return string The element attributes as a string.
    */
    private function _getAttributes($array)
    {
        extract( $array );
        $attr  = $width ? " width=\"$width\"":NULL;
        $attr .= $valign ? " valign=\"$valign\"":NULL;
        $attr .= $align ? " align=\"$align\"":NULL;
        $attr .= $class ? " class=\"$class\"":NULL;
        $attr .= $attrib ? " ".$attrib:NULL;
        return $attr;
    }

    /**
    * Private method used to get the table attributes.
    *
    * @param  array  The attributes in an associative array
    * @return string The table attributes as a string.
    */
    private function _getTableAttributes()
    {
        $attr  = '';
        $attr .= $this->width ? " width='$this->width'":NULL;
        $attr .= $this->cellpadding ? " cellpadding='$this->cellpadding'":NULL;
        $attr .= $this->cellspacing ? " cellspacing='$this->cellspacing'":NULL;
        return $attr;
    }

    /**
    * Wrapper function for sorttable js.
    */
    public function WriteRows()
    {
        return sprintf( "%s.WriteRows();\n", $this->name );
    }

    /**
    * Wrapper function for sorttable js.
    */
    public function SortRows($table,$column)
    {
        return sprintf("SortRows(%s,'%s');\n",$table,$column);
    }
    /**
    * Wrapper function for sorttable js.
    */
    public function create()
    {
        return sprintf("var %s = new SortTable('%s');\n", $this->name, $this->name);
    }

    /**
    * Wrapper function for sorttable js.
    */
    public function AddColumn($name,$td,$align,$type)
    {
        return sprintf("%s.AddColumn('%s','%s','%s','%s');\n",$this->name,$name, $td,$align,$type);
    }

    /**
    * Wrapper function for sorttable js.
    */
    function AddLine( $array )
    {
        return sprintf( "%s.AddLine('%s');\n",$this->name, implode("','",$array) );
    }

    /**
    * Wrapper function for sorttable js.
    */
    public function AddLineSortData( $array )
    {
        return sprintf( "%s.AddLineSortData('%s');\n",$this->name, implode("','",$array) );
    }

    /**
    * Wrapper function for sorttable js.
    */
    function AddLineProperties( $prop )
    {
        return sprintf( "%s.AddLineProperties('%s');\n", $this->name, $prop );
    }
}
?>