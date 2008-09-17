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
*
* Tigra Node class is a helper class used to define tigra menu nodes (A text element in your menu)
* These nodes can be combined to relate in a parent/child hierarcy to produce you menu structure.
*
*/
class tigraNode extends object
{

    /**
    *
    * @var string $caption: The caption of the menu item
    */
    public $caption;

    /**
    *
    * @var string $link: The link target of the menu item
    */
    public $link;

    /**
    *
    * @var string $scope: Use this to specify target-frame/window (tw), statusbar message (sb), tooltip (tt)
    */
    public $scope;

    /**
    *
    * @var array $children: an associative array of child nodes
    */
    public $children;


    /**
    * Constructor to establish the default values for the
    * node properties
    */
    public function init()
    {
		//Default CSS
        $this->width = "99%";
        $this->border = 0;
        $this->cellpadding = 0;
        $this->cellspacing = 0;
        $this->css_class = null;
    }

    /**
    * Alternate constructor
    */
    public function tigranode($caption, $link = null, $scope = null)
    {
	    $this->caption = $caption;
        $this->link = $link;
        $this->scope = $scope;
        $this->children = null;
	
        return $this->init();
    }


    /**
    * Alternate constructor
    */
    public function show()
    {
		$jscript = '';

/* Text for entire node
		var MENU_ITEMS = [
        	['Home', '?module=cms', null],
	        ['User', null, null,
	                ['CMS', '?module=cms'],
	                ['File Manager', '?module=filemanager']
	        ],
	        ['Admin', null, null,
	
	                ['CMS Admin', '?module=cmsadmin'],
	                ['Logger', '?module=logger'],
	                ['Module Catalogue', '?module=modulecatalogue'],
	                ['Site Admin', '?module=toolbar']
	        ],
	        ['Logout', "javascript: if(confirm('Are you sure you want to logout?')) {document.location= '?module=security&action=logoff'};"]
		];
*/


        return $this->init();
    }

    /**
    * Method to add a child node to the current node
    *
    * @param array  $caption    : The caption of the menu item
    * @param string $link       : The link of the menu item
    * @param string $scope 		: The scope of the menu item (target, tooltip, status bar message)
    */
    public function addChild($caption, $link = null, $scope = null)
    {
		$node = new tigranode($caption, $link, $scope);	
		
    }

    /**
    * Method to add a header row to a table
    *
    * @param array  $content        : The array of cell entries for the table
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
    * @param  array   $content        : The array of cell entries for the table
    * @param  string  $trClass        : optional CSS class from the skin (normally odd, even, heading)
    * @param  string  $tdClass        : optional CSS class from the skin (normally odd, even, heading)
    * @param  string  $row_attributes : any additional attributes that you want to pass to the TD tag
    * @return string  $row: the formatted table body with the new row added
    *                 
    *                 ......PLEASE DO NOT MUCK ABOUT IN HERE...CONTACT DEREK FIRST
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
    * @param  array   $content        : The array of cell entries for the table
    * @param  string  $trClass        : optional CSS class from the skin (normally odd, even, heading)
    * @param  string  $tdClass        : optional CSS class from the skin (normally odd, even, heading)
    * @param  string  $row_attributes : any additional attributes that you want to pass to the TD tag
    * @return string  $row: the formatted table body with the new row added
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
    * @param  string  $content : The content that you wish to validate
    * @return true    | false
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
    * @param  array   $ar : the array to parse
    * @return array   return: a simple array of keys for use to build table header
    */
    private function _getArrayKeys($ar)
    {
        foreach ($ar as $line) { // This is the best I can do, there must be a better way
            return array_keys($line);
        }
    }
}

?>
